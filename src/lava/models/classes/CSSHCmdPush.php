<?php

namespace dekuan\lava\models\classes;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use dekuan\lava\libs;



class CSSHCmdPush extends CSSHCmd
{
	public function SyncLocalToRemote( $arrSrvList, $sRepoVer, $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_SyncLocalToRemote( $arrSrvList, $sRepoVer, $sReleaseDir, $pfnCbFunc );
	}

	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _SyncLocalToRemote( $arrSrvList, $sRepoVer, $sReleaseDir, callable $pfnCbFunc = null )
	{
		if ( ! is_array( $arrSrvList ) || 0 == count( $arrSrvList ) )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "# Error in parameter " . __FUNCTION__ . ", arrSrvList" );
			}
			return false;
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "# Error in parameter " . __FUNCTION__ . ", sRepoVer" );
			}
			return false;
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "# Error in parameter " . __FUNCTION__ . ", sReleaseDir" );
			}
			return false;
		}

		//	...
		$bRet	= false;

		//
		//	"list" :
		//	[
		//		{
		//			"host"	: "123.56.168.190",
		//			"user"	: "lqx",
		//			"pwd"	: "lqx",
		//			"path"	: "/home/lqx/www/"
		//		}
		//	]
		//
		foreach ( $arrSrvList as $arrItem )
		{
			if ( $this->_IsValidItem( $arrItem ) )
			{
				$sCmdLine		= '';
				$sHost			= $arrItem[ 'host' ];
				$sCmdCreateDir		= $this->_GetCommandCreateDir( $arrItem, $sRepoVer );
				$sCmdRSync		= $this->_GetCommandRSync( $arrItem, $sReleaseDir, $sRepoVer );
				$sCmdChMods		= $this->_GetCommandChangeFileModes( $arrItem, $sRepoVer );
				$sCmdCreateSymlink	= $this->_GetCommandNewRelease( $arrItem, $sRepoVer );

				if ( is_string( $sCmdCreateDir ) && strlen( $sCmdCreateDir ) > 0 &&
					is_string( $sCmdRSync ) && strlen( $sCmdRSync ) > 0 &&
					is_string( $sCmdChMods ) && strlen( $sCmdChMods ) > 0 &&
					is_string( $sCmdCreateSymlink ) && strlen( $sCmdCreateSymlink ) > 0 )
				{
					$arrCmdList	=
						[
							$sCmdCreateDir,
							$sCmdRSync,
							$sCmdCreateSymlink,
							$sCmdChMods
						];

					$sCmdLine = implode( ' && ', $arrCmdList );

					//
					//	execute the command
					//
					if ( null !== $pfnCbFunc )
					{
						$pfnCbFunc( 'info', "EXECUTE\t\t: command on server [ $sHost ]" );
						$pfnCbFunc( 'comment', "\t\t  $sCmdLine" );
						$pfnCbFunc( 'sinfo', "\t\t  pushing files to server [ $sHost ] ... " );
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
							$pfnCbFunc( 'sinfo', "\t\t[OK]" );
							$pfnCbFunc( 'info', "" );
						}
					}
					else
					{

						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
						}

						throw new \RuntimeException( $cProcess->getErrorOutput() );
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

	private function _GetCommandRSync( $arrItem, $sReleaseDir, $sRepoVer )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sReleaseDir ) || 0 == strlen( $sReleaseDir ) )
		{
			return '';
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			return '';
		}

		return sprintf
		(
			"rsync -aruv --verbose \"%s/\" %s@%s:\"%s/%s/%s/\"",
			libs\Lib::RTrimPath( $sReleaseDir ),
			trim( $arrItem['user'] ),
			trim( $arrItem['host'] ),
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' ),
			$sRepoVer
		);
	}

	private function _GetCommandCreateDir( $arrItem, $sRepoVer )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			return '';
		}

		//
		//	ssh lqx@123.56.168.190 mkdir -p /home/lqx/www/pay.xs.cn/release/ &&
		// 		ssh lqx@123.56.168.190 mkdir -p /home/lqx/www/pay.xs.cn/release/1.0.0/
		//
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );
		$sCmdLineMkDir1	= sprintf
		(
			"mkdir -pv \"%s/%s/\"",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' )
		);
		$sCmdLineMkDir2	= sprintf
		(
			"mkdir -pv \"%s/%s/%s/\"",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' ),
			$sRepoVer
		);

		//	...
		return sprintf
		(
			"%s %s && %s %s",
			$sCmdSSH,
			$sCmdLineMkDir1,
			$sCmdSSH,
			$sCmdLineMkDir2
		);
	}

	private function _GetCommandNewRelease( $arrItem, $sRepoVer )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			return '';
		}

		$sDirWwwroot		= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_wwwroot' )
		);
		$sDirReleasedVersion	= sprintf
		(
			"%s/%s/%s/",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' ),
			$sRepoVer
		);

		$arrCmdList	= [];

		//	...
		$sLastVerDir	= '';
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );
		$sCmdLineRenew	= $this->_GetCommandRenew( $sCmdSSH, $sDirWwwroot, $sRepoVer, $sLastVerDir );

		//	1, rm symlink if exists
		$arrCmdList[]	= sprintf( "%s rm -fv \"%s\"", $sCmdSSH, $sDirWwwroot );

		//	2, renew basename of symlink target
		if ( strlen( $sCmdLineRenew ) > 0 )
		{
			$arrCmdList[]	= sprintf( "%s %s", $sCmdSSH, $sCmdLineRenew );
		}

		//	3, create new symlink
		$arrCmdList[]	= sprintf( "%s ln -sv \"%s\" \"%s\"", $sCmdSSH, $sDirReleasedVersion, $sDirWwwroot );

		//
		//	4, archive the last version which was turned off line just now
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


	private function _GetCommandChangeFileModes( $arrItem, $sRepoVer )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			return '';
		}

		//	...
		$sDirReleasedVersion	= sprintf
		(
			"%s/%s/%s/",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' ),
			$sRepoVer
		);

		//	...
		$arrDirs	= libs\Config::Get( 'path_chmod_dirs', null );
		$arrCmdList	= [];
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );

		if ( is_array( $arrDirs ) && count( $arrDirs ) > 0 )
		{
			foreach ( $arrDirs as $sDirName => $nAttrVal )
			{
				$sPath		= sprintf( "%s/%s", libs\Lib::RTrimPath( $sDirReleasedVersion ), libs\Lib::LTrimPath( $sDirName ) );
				$sCommand	= sprintf( "%s chmod -R %d \"%s\"", $sCmdSSH, $nAttrVal, $sPath );
				$arrCmdList[]	= $sCommand;

			}
		}

		//	...
		return implode( ' && ', $arrCmdList );
	}
}