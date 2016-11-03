<?php

namespace dekuan\ladep\models;

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use dekuan\ladep\libs;
use dekuan\ladep\models\classes;




/**
 *	class of CCheck
 */
class CCheck
{
	//	...
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
		$nRet		= -1;	//	dekuan\vdata\CConst::ERROR_UNKNOWN;
		$sErrorDesc	= '';
		$sErrorPath	= '';
		$cGit		= new classes\CGit();
		$cMiniSrv	= classes\CMiniServer::GetInstance();


		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$sProjectConfig	= array_key_exists( 'project_config', $arrParameter ) ? $arrParameter['project_config'] : '';

		if ( ! is_string( $sProjectConfig ) || ! is_file( $sProjectConfig ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s error in parameters.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$bObtainLastTag	= array_key_exists( 'last', $arrParameter ) ? boolval( $arrParameter['last'] ) : false;

		//	...
		$pfnCbFunc( 'sinfo', sprintf( "READ\t\t: %s", $sProjectConfig ) );
		$nErrorId = $this->m_cProject->Load( $sProjectConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$pfnCbFunc( 'sinfo', "\t\t\t[OK]" );
			$pfnCbFunc( 'info', "" );

			//	...
			$sProjectName		= $this->m_cProject->GetName();
			$sRepoUrl		= $this->m_cProject->GetRepoUrl();
			$sRepoVer		= $this->m_cProject->GetRepoVer();

			if ( $bObtainLastTag )
			{
				$pfnCbFunc( 'sinfo', sprintf( "LAST\t\t: Try to obtain the last tag from remote repository ..." ) );
				$sRemoteLastTag	= $cGit->GetLastTagFromRemoteRepository( $sRepoUrl, null );
				if ( is_string( $sRemoteLastTag ) && strlen( $sRemoteLastTag ) )
				{
					$pfnCbFunc( 'sinfo', "\t\t\t[$sRemoteLastTag]" );
					$sRepoVer = $sRemoteLastTag;
				}
				else
				{
					$pfnCbFunc( 'sinfo', "\t\t\tERROR, use [$sRepoVer]" );
				}
				$pfnCbFunc( 'info', "" );
			}

			//	...
			$sErrorDesc	= '';
			$sReleaseDir	= libs\Lib::GetLocalReleasedVersionDir( $sProjectName, $sRepoVer );

			if ( is_string( $sReleaseDir ) && is_dir( $sReleaseDir ) )
			{
				//
				//	try to stop mini http server
				//
				$pfnCbFunc( 'info',
					sprintf( "MINI-SERVER\t: Try to launch a mini-server on tcp port %d to check your website", $cMiniSrv->GetServerPort() ) );
				$cMiniSrv->SafeRestart( $sReleaseDir, $pfnCbFunc );

				//	...
				usleep( 1000 );

				if ( $cMiniSrv->IsListening() )
				{
					$pfnCbFunc( 'sinfo', sprintf( "CHECKING\t: %s", $cMiniSrv->GetServerUrl() ) );

					$nStatusCode	= 0;
					if ( $cMiniSrv->IsWebAvailable( $nStatusCode ) )
					{
						$nRet = 0;

						//	...
						$pfnCbFunc( 'sinfo', "\t\t\t\t\t\t[WORKS WELL]" );
					}
					else
					{
						$pfnCbFunc( 'scomment', sprintf( "\t\t\t\t\t\t[NOT AVAILABLE] http code %d.", $nStatusCode ) );
					}
					$pfnCbFunc( 'info', "" );
				}
				else
				{
					$pfnCbFunc( 'error', sprintf( "\t\t# Failed to start %s", $cMiniSrv->GetServerName() ) );
				}
			}
			else
			{
				$pfnCbFunc( 'error', sprintf( "\t\t# Release dir [%s] does not exist.", $sReleaseDir ) );
			}
		}
		else if ( -100002 == $nErrorId )
		{
			$pfnCbFunc( 'error', sprintf( "\t\t# Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}
		else
		{
			$sFormat	= libs\Lang::Get( "error_load_config" );
			$pfnCbFunc( 'error', sprintf( "\t\t# Failed to load project, error : %s", sprintf( $sFormat, $sErrorPath ) ) );
		}

		//	...
		$pfnCbFunc( 'info', "" );
		$pfnCbFunc( 'info', "" );

		return $nRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

}