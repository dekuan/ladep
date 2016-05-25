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
 *	class of CPulllog
 */
class CPulllog
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function Run( $arrParameter, callable $pfnCbFunc )
	{
		//
		//	cOutput
		//	arrParameter	- [ 'project_config' => '', 'to_version' => '' ]
		//	pfnCbFunc	- address of callback function
		//	RETURN		- error id
		//
		$nRet		= -1;	//	xsconst\CConst::ERROR_UNKNOWN;

		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$sProjectConfig	= array_key_exists( 'project_config', $arrParameter ) ? $arrParameter['project_config'] : '';

		//	...
		$cSSHCmdPL	= new classes\CSSHCmdPullLogs();

		$sLogsDir	= libs\Config::Get( 'dir_logs' );
		$arrServerList	= $this->_ScanAllServerList( $sProjectConfig );

		if ( is_array( $arrServerList ) && count( $arrServerList ) > 0 )
		{
			if ( ! is_dir( $sLogsDir ) )
			{
				@ mkdir( $sLogsDir );
			}

			if ( $cSSHCmdPL->PullLogs( $arrServerList, $sLogsDir, $pfnCbFunc ) )
			{
				$nRet = 0;
			}
			else
			{
				$pfnCbFunc( 'error', 'failed to pull logs.' );
			}
		}
		else
		{
			$pfnCbFunc( 'error', 'can not get server list.' );
		}

		return $nRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _ScanAllServerList( $sProjectConfig = '' )
	{
		//
		//	sProjectConfig	- the full filename of project
		//	RETURN		- array of server list of null
		//			[
		//				{
		//					"host"	: "101.200.161.46",
		//					"user"	: "worker",
		//					"pwd"	: "",
		//					"path"	: "/var/www/account.xs.cn/"
		//				},
		//				...
		//			]
		//
		$arrRet	= [];

		if ( is_string( $sProjectConfig ) && is_file( $sProjectConfig ) )
		{
			$cPj	= new classes\CProject();
			if ( 0 == $cPj->Load( $sProjectConfig ) )
			{
				//
				//	[
				//		{
				//			"host"	: "101.200.161.46",
				//			"user"	: "worker",
				//			"pwd"	: "",
				//			"path"	: "/var/www/account.xs.cn/"
				//		}
				//	]
				//
				$arrSrvList	= $cPj->GetServerList();
				if ( $cPj->IsValidServerList( $arrSrvList ) )
				{
					$arrRet = $arrSrvList;
				}
			}
			unset( $cPj );
			$cPj = null;
		}
		else
		{
			$cPjFiles	= new classes\CProjectFiles();

			$sProjectDir	= libs\Config::Get( 'dir_projects' );
			$sProjectExt	= libs\Config::Get( 'ext_project' );

			$arrFiles	= $cPjFiles->ScanAll( $sProjectDir, $sProjectExt );
			if ( is_array( $arrFiles ) && count( $arrFiles ) )
			{
				foreach ( $arrFiles as $sFullFilename )
				{
					$cPj		= new classes\CProject();
					$sErrorPath	= '';
					$nErrorId	= $cPj->Load( $sFullFilename, $sErrorPath );

					if ( 0 == $nErrorId )
					{
						$arrSrvList	= $cPj->GetServerList();

						if ( $cPj->IsValidServerList( $arrSrvList ) )
						{
							$arrRet = array_merge( $arrRet, $arrSrvList );
						}
					}
					unset( $cPj );
					$cPj = null;
				}
			}
		}

		return $arrRet;
	}






	private function _GetServerListByProjectConfig( $sProjectConfig, callable $pfnCbFunc )
	{
		if ( ! is_callable( $pfnCbFunc ) )
		{
			$pfnCbFunc( 'error', 'pfnCbFunc is not a callable address.' );
			return false;
		}
		if ( ! is_string( $sProjectConfig ) || ! is_file( $sProjectConfig ) )
		{
			$pfnCbFunc( 'error', sprintf( "Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
			return false;
		}

		//	...
		$sErrorPath	= '';

		//	...
		$pfnCbFunc( 'info', sprintf( "Try to load project : %s", $sProjectConfig ) );
		$nErrorId = $this->m_cProject->Load( $sProjectConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$pfnCbFunc( 'info', sprintf( "Load project successfully" ) );
			$pfnCbFunc( 'info', sprintf( "%s::%s _SyncLocalToRemote", __CLASS__, __FUNCTION__ ) );

			//	...
			$arrSrvList		= $this->m_cProject->GetServerList();
		}
		else if ( -100002 == $nErrorId )
		{
			$pfnCbFunc( 'error', sprintf( "Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}
		else
		{
			$sFormat	= libs\Lang::Get( "error_load_config" );
			$pfnCbFunc( 'error', sprintf( "Failed to load project, error : %s", sprintf( $sFormat, $sErrorPath ) ) );
		}

		return true;
	}
}