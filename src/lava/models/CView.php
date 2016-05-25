<?php

namespace dekuan\lava\models;

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use xscn\xsconst;
use dekuan\lava\libs;
use dekuan\lava\models\classes;



/**
 *	class of CView
 */
class CView
{
	private $m_cProject	= null;


	public function __construct()
	{
		$this->m_cProject	= new classes\CProject();
	}
	public function __destruct()
	{
	}

	public function Run( $arrParameter, callable $pfnCbFunc )
	{
		$nRet		= -1;	//	xsconst\CConst::ERROR_UNKNOWN;
		$sErrorPath	= '';
		$cSSHCmdVR	= new classes\CSSHCmdViewRemote();

		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$sProjectConfig	= array_key_exists( 'project_config', $arrParameter ) ? $arrParameter['project_config'] : '';

		//	...
		$pfnCbFunc( 'sinfo', sprintf( "READ\t\t: %s", $sProjectConfig ) );

		//	...
		$nErrorId = $this->m_cProject->Load( $sProjectConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$pfnCbFunc( 'sinfo', "\t\t\t[OK]" );
			$pfnCbFunc( 'info', "" );

			$arrSrvListWithKey	= $this->m_cProject->GetServerListWithKey();


			$arrVersionList = $cSSHCmdVR->ViewRemoteVersion
				(
					$arrSrvListWithKey,
					$pfnCbFunc
				);
			if ( is_array( $arrVersionList ) && count( $arrVersionList ) > 0 )
			{
				$nRet = 0;

				//
				//	print the result
				//
				$this->_PrintResult( $arrSrvListWithKey, $arrVersionList, $pfnCbFunc );
				$pfnCbFunc( 'info', "" );
				$pfnCbFunc( 'info', "" );

				//$pfnCbFunc( 'info', sprintf( "ListRemoteVersion execute on %d server(s).", count( $arrVersionList ) ) );
			}
			else
			{
				$pfnCbFunc( 'error', sprintf( "# Failed to list all released version on server : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
			}
		}
		else if ( -100002 == $nErrorId )
		{
			$pfnCbFunc( 'error', sprintf( "# Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}
		else
		{
			$sFormat = libs\Lang::Get( "error_load_config" );
			$pfnCbFunc( 'error', sprintf( "# Failed to load project, error : %s", sprintf( $sFormat, $sErrorPath ) ) );
		}

		return $nRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _PrintResult( $arrSrvListWithKey, $arrVersionList, callable $pfnCbFunc )
	{
		if ( ! is_callable( $pfnCbFunc ) )
		{
			return false;
		}
		if ( ! is_array( $arrSrvListWithKey ) || 0 == count( $arrSrvListWithKey ) )
		{
			return false;
		}
		if ( ! is_array( $arrVersionList ) || 0 == count( $arrVersionList ) )
		{
			return false;
		}

		//	...
		$sKeyWwwroot	= libs\Config::Get( 'dir_wwwroot' );

		//$pfnCbFunc( 'info', "RESULT\t\t: - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -" );
		$pfnCbFunc( 'info', "RESULT\t\t: ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..." );
		foreach ( $arrVersionList as $sHost => $arrVersion )
		{
			if ( ! is_string( $sHost ) || 0 == strlen( $sHost ) )
			{
				continue;
			}
			if ( ! is_array( $arrVersion ) || 0 == count( $arrVersion ) )
			{
				continue;
			}
			if ( ! array_key_exists( $sHost, $arrSrvListWithKey ) ||
				! $this->m_cProject->IsValidServerItem( $arrSrvListWithKey[ $sHost ] ) )
			{
				$pfnCbFunc( 'comment', "\t\t  invalid server item of [$sHost]" );
				continue;
			}

			//	...
			$arrItem = $arrSrvListWithKey[ $sHost ];

			$pfnCbFunc( 'info', "\t\t  released versions on server [$sHost]" );
			$sReadlinkDir	= ( array_key_exists( $sKeyWwwroot, $arrVersion ) ? $arrVersion[ $sKeyWwwroot ] : '' );
			$sInserviceDir	= '';
			foreach ( $arrVersion as $sVerNum => $sVerNumFull )
			{
				if ( 0 == strcmp( $sVerNum, $sVerNumFull ) )
				{
					$sInserviceDir = sprintf
					(
						"%s/%s/%s",
						libs\Lib::RTrimPath( $arrItem['path'] ),
						libs\Config::Get( 'dir_release' ),
						$sVerNumFull
					);
					break;
				}
			}

			$bListedInservice	= false;
			$sReadlinkDir		= libs\Lib::RTrimPath( $sReadlinkDir );
			$sInserviceDir		= libs\Lib::RTrimPath( $sInserviceDir );

			if ( is_string( $sReadlinkDir ) && strlen( $sReadlinkDir ) &&
				is_string( $sInserviceDir ) && strlen( $sInserviceDir ) &&
				0 == strcmp( $sReadlinkDir, $sInserviceDir ) )
			{
				$bListedInservice = true;
				$pfnCbFunc( 'scomment', "\t\t  INSERVICE" );
				$pfnCbFunc( 'sinfo', " : $sReadlinkDir/" );
				$pfnCbFunc( 'info', "" );
			}
			else
			{
				$pfnCbFunc( 'comment', "\t\t  # NO VERSION IS NOW INSERVICE" );
			}

			foreach ( $arrVersion as $sVerNum => $sVerNumFull )
			{
				if ( 0 == strcmp( $sKeyWwwroot, $sVerNum ) )
				{
//					$pfnCbFunc( 'scomment', "\t\t  INSERVICE" );
//					$pfnCbFunc( 'sinfo', " : $sVerNumFull" );
//					$pfnCbFunc( 'info', "" );
				}
				else if ( 0 == strcmp( $sVerNum, $sVerNumFull ) )
				{
					if ( ! $bListedInservice )
					{
						$sDirReleased = sprintf
						(
							"%s/%s/%s%s",
							libs\Lib::RTrimPath( $arrItem['path'] ),
							libs\Config::Get( 'dir_release' ),
							$sVerNumFull,
							( false !== strrpos( $sVerNumFull, '.tar' ) ? '' : '/' )
						);

						$pfnCbFunc( 'info', "\t\t  OFFLINE   : $sDirReleased" );
					}
				}
				else
				{
					$sDirReleased = sprintf
					(
						"%s/%s/%s%s",
						libs\Lib::RTrimPath( $arrItem['path'] ),
						libs\Config::Get( 'dir_release' ),
						$sVerNumFull,
						( false !== strrpos( $sVerNumFull, '.tar' ) ? '' : '/' )
					);

					$pfnCbFunc( 'info', "\t\t  OFFLINE   : $sDirReleased" );
				}
			}
		}

		return true;
	}

}