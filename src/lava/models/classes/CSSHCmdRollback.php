<?php

namespace dekuan\lava\models\classes;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use dekuan\lava\libs;


class CSSHCmdRollback extends CSSHCmd
{
	public function RollbackToVersion( $arrRollbackList, callable $pfnCbFunc = null )
	{
		return $this->_RollbackToVersion( $arrRollbackList, $pfnCbFunc );
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//


	private function _RollbackToVersion( $arrRollbackList, $pfnCbFunc )
	{
		//
		//	arrRollbackList
		//	[
		//		'101.200.161.46'	=>
		//		[
		//			'ht'	=>
		//			[
		//				"host"	: "123.56.168.190",
		//				"user"	: "lqx",
		//				"pwd"	: "lqx",
		//				"path"	: "/home/lqx/www/"
		//			],
		//			'fr'	=> '',
		//			'to'	=> [ 'vern' => 'version number', 'vernf' => 'full version with datetime' ]
		//		]
		//	]
		//
		//
		if ( ! is_array( $arrRollbackList ) || 0 == count( $arrRollbackList ) )
		{
			return false;
		}

		//	...
		$bRet	= false;

		foreach ( $arrRollbackList as $arrNode )
		{
			if ( ! array_key_exists( 'ht', $arrNode ) ||
				! array_key_exists( 'fr', $arrNode ) ||
				! array_key_exists( 'to', $arrNode ) ||
				! array_key_exists( 'vern', $arrNode['to'] ) ||
				! array_key_exists( 'vernf', $arrNode['to'] ) )
			{
				continue;
			}

			$arrItem	= $arrNode[ 'ht' ];
			$sVerNumFrom	= $arrNode[ 'fr' ];
			$sToVerNum	= $arrNode[ 'to' ][ 'vern' ];
			$sToVerNumFull	= $arrNode[ 'to' ][ 'vernf' ];

			if ( $this->_IsValidItem( $arrItem ) &&
				is_string( $sVerNumFrom ) &&
				is_string( $sToVerNum ) && strlen( $sToVerNum ) > 0 &&
				is_string( $sToVerNumFull ) && strlen( $sToVerNumFull ) > 0 )
			{
				$sCmdLine	= '';
				$sHost		= $arrItem[ 'host' ];
				$sCmdRollback	= $this->_GetCommandRollback( $arrItem, $sToVerNum, $sToVerNumFull );

				if ( is_string( $sCmdRollback ) && strlen( $sCmdRollback ) > 0 )
				{
					$sCmdLine = $sCmdRollback;

					//
					//	execute the command
					//
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'info', "EXECUTE\t\t: command on server [ $sHost ]" );
						$pfnCbFunc( 'comment', "\t\t  $sCmdLine" );
						$pfnCbFunc( 'sinfo', "\t\t  Rolling back [$sVerNumFrom] to [$sToVerNum] on server [ $sHost ] ..." );
					}

					$cProcess = new Process\Process( $sCmdLine );
					$cProcess
						->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
						->enableOutput()
						->run()
					;

					if ( $cProcess->isSuccessful() )
					{
						$bRet = true;

						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( 'sinfo', "\t[OK]" );
							$pfnCbFunc( 'info', "" );
						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
						}
					}
				}
				else
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'error', "Failed to build list command." );
					}
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( 'error', "Invalid project configuration." );
				}
			}
		}

		return $bRet;
	}

	private function _GetCommandRollback( $arrItem, $sToVerNum, $sToVerNumFull )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sToVerNum ) || 0 == strlen( $sToVerNum ) )
		{
			return '';
		}
		if ( ! is_string( $sToVerNumFull ) || 0 == strlen( $sToVerNumFull ) )
		{
			return '';
		}


		//	...
		if ( false !== strrpos( $sToVerNumFull, '.tar' ) )
		{
			$sToVerNumFull	= libs\Lib::RTrimPath( $sToVerNumFull );
		}

		//	...
		$sDirWwwroot		= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_wwwroot' )
		);
		$sDirReleased		= sprintf
		(
			"%s/%s/",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' )
		);
		$sDirReleasedVersion	= sprintf
		(
			"%s/%s/",
			libs\Lib::RTrimPath( $sDirReleased ),
			$sToVerNum
		);
		$sDirReleasedVersionFull = sprintf
		(
			"%s/%s/%s/",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' ),
			$sToVerNumFull
		);

		//	...
		$arrCmdList	= [];

		//	...
		$sLastVerDir	= '';
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );
		$sCmdLineRenew	= $this->_GetCommandRenew( $sCmdSSH, $sDirWwwroot, $sToVerNum, $sLastVerDir );


		//
		//	1, try to unpack archived files if necessarily
		//
		if ( false !== strrpos( $sToVerNumFull, '.tar' ) )
		{
			//	.../1.0.30-_-20160510195125.tar/
			//	.../1.0.30-_-20160510195125.tar
			$sDirReleasedVersionFull	= libs\Lib::RTrimPath( $sDirReleasedVersionFull );
			$sTarFFN			= $sDirReleasedVersionFull;

			//	.../1.0.30-_-20160510195125
			$nRealDirLength			= strlen( $sDirReleasedVersionFull ) - 4;
			$sDirReleasedVersionFull	= substr( $sDirReleasedVersionFull, 0, $nRealDirLength );

			//	...

			$sTargetDir	= $sDirReleasedVersionFull;
			$arrCmdList[]	= $this->_GetCommandUnpackArchivedTar( $sCmdSSH, $sTarFFN, $sTargetDir, true );
		}


		//	2, rm symlink if exists
		$arrCmdList[]	= sprintf( "%s rm -fv \"%s\"", $sCmdSSH, $sDirWwwroot );

		//	3, rename basename for current version from version only to full version
		//	   1.0.16	-> 1.0.16-_-20160322042105
		if ( strlen( $sCmdLineRenew ) > 0 )
		{
			$arrCmdList[]	= sprintf( "%s %s", $sCmdSSH, $sCmdLineRenew );
		}

		//
		//	4, rename basename from full version to version only
		//	   it makes it as current in format
		$arrCmdList[]	= sprintf
		(
			"%s mv -fv \"%s\" \"%s\"",
			$sCmdSSH,
			$sDirReleasedVersionFull,
			$sDirReleasedVersion
		);

		//
		//	5, create new symlink
		//
		$arrCmdList[]	= sprintf( "%s ln -sv \"%s\" \"%s\"", $sCmdSSH, $sDirReleasedVersion, $sDirWwwroot );

		//
		//	6, archive the last version which was turned off line just now
		//
		if ( is_string( $sLastVerDir ) && strlen( $sLastVerDir ) > 0 )
		{
			$sTarTargetFFN	= sprintf( "%s.tar", libs\Lib::RTrimPath( $sLastVerDir ) );
			$sTarSourceDir	= sprintf( "%s", libs\Lib::RTrimPath( $sLastVerDir ) );
			$arrCmdList[]	= $this->_GetCommandMakeTarArchive
			(
				$sCmdSSH, $sTarSourceDir, $sTarTargetFFN, true
			);
		}

		//	...
		return implode( ' && ', $arrCmdList );
	}
}