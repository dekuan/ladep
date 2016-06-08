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
 *	class of CPush
 */
class CPush
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
		$sErrorDesc	= '';
		$sErrorPath	= '';
		$cGit		= new classes\CGit();


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
			$arrSrvConfig		= $this->m_cProject->GetServerConfig();
			$arrSrvList		= $this->m_cProject->GetServerList();
			$arrSrvListWithKey	= $this->m_cProject->GetServerListWithKey();
			$sSrvListString		= $this->m_cProject->GetServerHostListString();

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
			$sDirNew	= libs\Lib::GetLocalReleasedVersionDir( $sProjectName, $sRepoVer );

			//	checking
			$pfnCbFunc( 'sinfo', sprintf( "CHECK\t\t: if the project is ready to be released." ) );

			//	...
			if ( $this->_IsReadyStatus( $sDirNew, $pfnCbFunc ) )
			{
				$pfnCbFunc( 'sinfo', "\t\t\t[OK]" );
				$pfnCbFunc( 'info', "" );

				//
				//	Are you sure ?
				//
				$pfnCbFunc( 'sinfo', "CONFIRM\t\t: push version [$sRepoVer] to server [$sSrvListString] ? (yes/no):" );
				$sAnswer = trim( fgets( STDIN ) );

				if ( 0 == strcasecmp( 'yes', $sAnswer ) || 0 == strcasecmp( 'y', $sAnswer ) )
				{
					$pfnCbFunc( 'info', "CHECK\t\t: if exists version [$sRepoVer] on server [$sSrvListString] ?" );

					//	...
					$arrSrvListNew	= $this->_GetTargetServerList( $arrSrvListWithKey, $sRepoVer, $pfnCbFunc );
					if ( is_array( $arrSrvListNew ) && count( $arrSrvListNew ) > 0 )
					{
						//	...
						$pfnCbFunc( 'info', "PUSH\t\t: start to push version [$sRepoVer] to server [$sSrvListString]." );

						//	...
						if ( $this->_SyncLocalToRemote( $arrSrvListNew, $sRepoVer, $sDirNew, $pfnCbFunc ) )
						{
							//	...
							$nRet = 0;
							$pfnCbFunc( "info", sprintf( "RESULT\t\t: %s [%s] was released successfully!", $sProjectName, $sRepoVer ) );
							$pfnCbFunc( "info", "" );
							$pfnCbFunc( "info", "" );
						}
						else
						{
							$pfnCbFunc( "info", "" );
							$pfnCbFunc( 'comment', "# Failed to upload files, in _SyncLocalToRemote" );
						}
					}
					else
					{
						$pfnCbFunc( "info", "" );
						$pfnCbFunc( 'comment', "# version [$sRepoVer] already exists on server [$sSrvListString], please push a fresh one." );
					}
				}
				else
				{
					$pfnCbFunc( "info", "" );
					$pfnCbFunc( 'comment', "# Okay, We have canceled the job." );
				}
			}
			else
			{
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( 'comment', "Project is not ready to be released, please make sure you run build command correctly." );
			}
		}
		else if ( -100002 == $nErrorId )
		{
			$pfnCbFunc( 'error', sprintf( "# Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}
		else
		{
			$sFormat	= libs\Lang::Get( "error_load_config" );
			$pfnCbFunc( 'error', sprintf( "# Failed to load project, error : %s", sprintf( $sFormat, $sErrorPath ) ) );
		}

		return $nRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _GetTargetServerList( $arrSrvListWithKey, $sRepoVer, callable $pfnCbFunc )
	{
		//
		//	arrSrvList	- [in] array	server list
		//	sRepoVer	- [in] string	the version of the project ready to be released
		//	pfnCbFunc	- [in] callable	callback function
		//	RETURN		- array	the version list of rollback
		//
		if ( ! is_array( $arrSrvListWithKey ) || 0 == count( $arrSrvListWithKey ) )
		{
			$pfnCbFunc( 'error', "# Error in parameter [arrSrvListWithKey] in function _GetTargetServerList." );
			return null;
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			$pfnCbFunc( 'error', "# Error in parameter [arrSrvListWithKey] in function _GetTargetServerList." );
			return null;
		}

		$arrRet		= null;
		$cSSHCmdVR	= new classes\CSSHCmdViewRemote();

		//	...
		$arrVersionList	= $cSSHCmdVR->ViewRemoteVersion( $arrSrvListWithKey, $pfnCbFunc );
		if ( is_array( $arrVersionList ) && count( $arrVersionList ) > 0 )
		{
			if ( count( $arrSrvListWithKey ) == count( $arrVersionList ) )
			{
				$arrRet	= [];
				foreach ( $arrVersionList as $sHost => $arrVerList )
				{
					if ( is_array( $arrVerList ) )
					{
						if ( ! array_key_exists( $sRepoVer, $arrVerList ) )
						{
							if ( array_key_exists( $sHost, $arrSrvListWithKey ) &&
								is_array( $arrSrvListWithKey[ $sHost ] ) )
							{
								$arrRet[] = $arrSrvListWithKey[ $sHost ];
							}
						}
					}
				}
			}
			else
			{
				$pfnCbFunc( 'error', "# Failed to obtain version list from server(s)." );
			}
		}
		else
		{
			//$pfnCbFunc( 'error', sprintf( "# Failed to list all released version on server : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
			$pfnCbFunc( 'comment', sprintf( "# No released version found on all of server(s)." ) );

			//
			//	try to push the project to all of server(s).
			//
			foreach ( $arrSrvListWithKey as $sHost => $arrData )
			{
				$arrRet[] = $arrData;
			}
		}

		//	...
		return $arrRet;
	}

	private function _SyncLocalToRemote( $arrSrvList, $sRepoVer, $sReleaseDir, callable $pfnCbFunc )
	{
		$cSSHCmdPh = new classes\CSSHCmdPush();

		//	...
		return $cSSHCmdPh->SyncLocalToRemote
		(
			$arrSrvList,
			$sRepoVer,
			$sReleaseDir,
			$pfnCbFunc
		);
	}

	private function _IsReadyStatus( $sReleaseDir, callable $pfnCbFunc )
	{
		$cStatus = new classes\CStatus();

		//	...
		return $cStatus->IsReadyStatus( $sReleaseDir );
	}
}