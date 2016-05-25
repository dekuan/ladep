<?php

namespace xscn\xsdep\models\classes;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use xscn\xsdep\libs;



class CSSHCmdViewRemote extends CSSHCmd
{
	public function ViewRemoteVersion( $arrSrvList, callable $pfnCbFunc = null )
	{
		return $this->_ViewRemoteVersion( $arrSrvList, $pfnCbFunc );
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//


	private function _ViewRemoteVersion( $arrSrvList, callable $pfnCbFunc = null )
	{
		if ( ! is_array( $arrSrvList ) || 0 == count( $arrSrvList ) )
		{
			return null;
		}

		//	...
		$arrRet	= [];

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
		//	RETURN
		//	arrRet
		//	[
		//		'123.56.168.190'	=>
		//		[
		//			'1.0.19'	=> '1.0.19'
		//			'1.0.18'	=> '1.0.18-_-20160412142130'
		//			'1.0.16'	=> '1.0.16-_-20160322042105'
		//		]
		//	];
		//
		foreach ( $arrSrvList as $arrItem )
		{
			if ( $this->_IsValidItem( $arrItem ) )
			{
				$sCmdLine		= '';
				$sHost			= $arrItem[ 'host' ];
				$sCmdVersionList	= $this->_GetCommandVersionList( $arrItem );
				$sCurrentInserviceLink	= $this->_GetInserviceWwwrootLink( $arrItem );

				if ( is_string( $sCmdVersionList ) && strlen( $sCmdVersionList ) > 0 )
				{
					$sCmdLine = sprintf( "%s", $sCmdVersionList );

					//
					//	execute the command
					//
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'info', "EXECUTE\t\t: command on server [ $sHost ]" );
						$pfnCbFunc( 'comment', "\t\t  $sCmdLine" );
						$pfnCbFunc( 'info', "\t\t  listing versions on server [ $sHost ]" );
					}

					$cProcess = new Process\Process( $sCmdLine );
					$cProcess
						->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
						->enableOutput()
						->run()
					;

					if ( $cProcess->isSuccessful() )
					{
						$arrVersionList	= $this->_GetVersionListArray( $cProcess->getOutput() );
						if ( is_array( $arrVersionList ) && count( $arrVersionList ) > 0 )
						{
							//	...
							$sKeyWwwroot = libs\Config::Get( 'dir_wwwroot' );
							$arrVersionList[ $sKeyWwwroot ]	= $sCurrentInserviceLink;
							$arrRet[ $sHost ]		= $arrVersionList;

							if ( is_callable( $pfnCbFunc ) )
							{
								//	$pfnCbFunc( 'array', $arrVersionList );
								//	$pfnCbFunc( 'result', $arrVersionList );
							}
						}
						else
						{
							if ( is_callable( $pfnCbFunc ) )
							{
								$pfnCbFunc( 'comment', "# No released version found on server." );
							}
						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( 'error', "# " . $cProcess->getErrorOutput() );
						}
					}
				}
				else
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'error', "# Failed to build list command." );
					}
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( 'error', "# Invalid project configuration." );
				}
			}
		}

		return $arrRet;
	}


	private function _GetCommandVersionList( $arrItem )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}

		//
		//	ssh lqx@123.56.168.190 mkdir -p /home/lqx/www/pay.xs.cn/release/ &&
		// 		ssh lqx@123.56.168.190 mkdir -p /home/lqx/www/pay.xs.cn/release/1.0.0/
		//
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );
		$sCmdLineLs	= sprintf
		(
			"ls -l \"%s/%s/\" | awk '{ print \$NF }'",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_release' )
		);

		//	...
		return sprintf( "%s %s", $sCmdSSH, $sCmdLineLs );
	}

	private function _GetVersionListArray( $sBuffer )
	{
		//
		//	sBuffer like:
		//
		//	8
		//	1.0.0
		//	1.0.1
		//
		//	ls -l respond:
		//	total 8
		//	drwxr-xr-x 9 lqx lqx 4096 Jan 27 20:59 1.0.0
		//	drwxr-xr-x 9 lqx lqx 4096 Jan 29 18:35 1.0.1
		//
		//
		//	RETURN
		//	arrRet
		//	[
		//		'1.0.19'	=> '1.0.19'
		//		'1.0.18'	=> '1.0.18-_-20160412142130'
		//		'1.0.16'	=> '1.0.16-_-20160322042105'
		//	];
		//

		if ( ! is_string( $sBuffer ) || 0 == strlen( $sBuffer ) )
		{
			return [];
		}

		$arrRet		= [];
		$arrResult	= [];
		$arrList	= explode( "\n", $sBuffer );

		if ( is_array( $arrList ) && count( $arrList ) > 1 )
		{
			for ( $i = 1; $i < count( $arrList ); $i ++ )
			{
				$sDirName = trim( $arrList[ $i ], "\r\n\t " );
				if ( is_string( $sDirName ) && strlen( $sDirName ) > 0 )
				{
					$arrResult[] = $sDirName;
				}
			}
		}

		//	...
		$sSplitChars	= libs\Config::Get( 'split_char_of_version', '-_-' );

		if ( count( $arrResult ) > 0 )
		{
			rsort( $arrResult, SORT_STRING );

			foreach ( $arrResult as $sVerStr )
			{
				$sVerStr	= trim( $sVerStr );
				$nPos		= strpos( $sVerStr, $sSplitChars );
				if ( false !== $nPos )
				{
					$sVersionNumber	= substr( $sVerStr, 0, $nPos );
				}
				else
				{
					$sVersionNumber	= $sVerStr;
				}

				//
				//	arrRet
				//	[
				//		'1.0.19'	=> '1.0.19'
				//		'1.0.18'	=> '1.0.18-_-20160412142130'
				//		'1.0.16'	=> '1.0.16-_-20160322042105'
				//	];
				//
				$arrRet[ $sVersionNumber ] = $sVerStr;
			}
		}

		return $arrRet;
	}
}