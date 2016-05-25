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
 *	class of CRollback
 */
class CRollback
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
		//
		//	cOutput
		//	arrParameter	- [ 'project_config' => '', 'to_version' => '' ]
		//	pfnCbFunc	- address of callback function
		//	RETURN		- error id
		//
		$nRet		= -1;	//	xsconst\CConst::ERROR_UNKNOWN;
		$sErrorPath	= '';

		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$sProjectConfig	= array_key_exists( 'project_config', $arrParameter ) ? $arrParameter['project_config'] : '';
		$sToVersion	= array_key_exists( 'to_version', $arrParameter ) ? $arrParameter['to_version'] : '';

		if ( ! is_string( $sProjectConfig ) || ! is_file( $sProjectConfig ) ||
			! is_string( $sToVersion ) || 0 == strlen( $sToVersion ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s error in parameters.", __CLASS__, __FUNCTION__ ) );
		}


		//	...
		$pfnCbFunc( 'sinfo', sprintf( "READ\t\t: %s", $sProjectConfig ) );
		$nErrorId = $this->m_cProject->Load( $sProjectConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$pfnCbFunc( 'sinfo', sprintf( "\t\t\t[OK]" ) );
			$pfnCbFunc( 'info', "" );

			$sProjectName		= $this->m_cProject->GetName();
			$sRepoUrl		= $this->m_cProject->GetRepoUrl();
			$sRepoVer		= $this->m_cProject->GetRepoVer();
			$arrSrvConfig		= $this->m_cProject->GetServerConfig();
			$arrSrvList		= $this->m_cProject->GetServerList();
			$arrSrvListWithKey	= $this->m_cProject->GetServerListWithKey();
			$sSrvListString		= $this->m_cProject->GetServerHostListString();

			//	...
			$arrRollbackList	= $this->_CalcRollbackVersionList( $arrSrvListWithKey, $sToVersion, $pfnCbFunc );
			if ( is_array( $arrRollbackList ) && count( $arrRollbackList ) > 0 )
			{
				//
				//	Are you sure ?
				//
				$pfnCbFunc( 'sinfo', "CONFIRM\t\t: rollback [$sProjectName] to version [$sToVersion] ? (yes/no):" );
				$sAnswer = trim( fgets( STDIN ) );

				if ( 0 == strcasecmp( 'yes', $sAnswer ) || 0 == strcasecmp( 'y', $sAnswer ) )
				{
					if ( $this->_RollbackToVersion( $arrRollbackList, $pfnCbFunc ) )
					{
						$nRet = 0;
						$pfnCbFunc( 'info', "RESULT\t\t: successfully." );
						$pfnCbFunc( 'info', "" );
						$pfnCbFunc( 'info', "" );
					}
					else
					{
						$pfnCbFunc( 'error', "# Failed in _RollbackToVersion" );
					}
				}
				else
				{
					$pfnCbFunc( 'comment', "# Okay, We have canceled the job." );
				}
			}
			else
			{
				$pfnCbFunc( 'error', "# Failed in obtaining rollback list." );
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

	private function _CalcRollbackVersionList( $arrSrvListWithKey, $sToVersion, callable $pfnCbFunc )
	{
		//
		//	arrSrvListWithKey	- [in] array	server list
		//	sToVersion		- [in] string	the version rollback to
		//	pfnCbFunc		- [in] callable	callback function
		//	RETURN			- array	the version list of rollback
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
		if ( ! is_array( $arrSrvListWithKey ) || 0 == count( $arrSrvListWithKey ) )
		{
			$pfnCbFunc( 'error', "Error in parameter [arrSrvList] in function _AreVersionExist." );
			return null;
		}
		if ( ! is_string( $sToVersion ) || 0 == strlen( $sToVersion ) )
		{
			$pfnCbFunc( 'error', "Error in parameter [sToVersion] in function _AreVersionExist." );
			return null;
		}

		$arrRet		= [];
		$cSSHCmdVR	= new classes\CSSHCmdViewRemote();
		$nExistsCount	= 0;

		//	...
		$arrVersionList	= $cSSHCmdVR->ViewRemoteVersion( $arrSrvListWithKey, $pfnCbFunc );
		if ( is_array( $arrVersionList ) && count( $arrVersionList ) > 0 )
		{
			if ( count( $arrSrvListWithKey ) == count( $arrVersionList ) )
			{
				//$pfnCbFunc( 'info', sprintf( "ListRemoteVersion execute on %d server(s).", count( $arrVersionList ) ) );

				foreach ( $arrVersionList as $sHost => $arrVerList )
				{
					//
					//	arrVerList
					//	[
					//		'1.0.19'	=> '1.0.19',				//	current
					//		'1.0.15'	=> '1.0.15-_-20160304105424',
					//		'1.0.16'	=> '1.0.16-_-20160322042105',
					//	]
					//
					if ( ! array_key_exists( $sHost, $arrSrvListWithKey ) ||
						! is_array( $arrSrvListWithKey[ $sHost ] ) )
					{
						continue;
					}

					//	...
					$arrHostInfo	= $arrSrvListWithKey[ $sHost ];
					if ( is_array( $arrVerList ) &&
						array_key_exists( $sToVersion, $arrVerList ) &&
						is_string( $arrVerList[ $sToVersion ] ) &&
						strlen( $arrVerList[ $sToVersion ] ) > 0 )
					{
						$sCurrentVersion	= $this->_GetCurrentVersion( $arrVerList );
						$sToVersionFull		= $arrVerList[ $sToVersion ];

						if ( 0 != strcmp( $sToVersion, $sToVersionFull ) )
						{
							//
							//	build the rollback list
							//
							$arrRet[ $sHost ] =
							[
								'ht'	=> $arrHostInfo,
								'fr'	=> $sCurrentVersion,
								'to'	=> [ 'vern' => $sToVersion, 'vernf' => $sToVersionFull ],
							];

							//	...
							$nExistsCount ++;
							$pfnCbFunc( 'info', "\t\t  Version [$sToVersion] was found on server [$sHost]." );
						}
						else
						{
							$pfnCbFunc( 'error', "# We can not rollback to current version [$sToVersion] on server [$sHost]." );
						}
					}
					else
					{
						$pfnCbFunc( 'error', "# Version [$sToVersion] not exists on server [$sHost]." );
					}
				}
			}
			else
			{
				$pfnCbFunc( 'error', "# Not all server has version [$sToVersion] to roll back to." );
			}
		}
		else
		{
			$pfnCbFunc( 'error', sprintf( "# Failed to list all released version on server : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}

		//	...
		return ( count( $arrRet ) == count( $arrSrvListWithKey ) ? $arrRet : null );
	}
	private function _RollbackToVersion( $arrRollbackList, callable $pfnCbFunc )
	{
		if ( ! is_array( $arrRollbackList ) || 0 == count( $arrRollbackList ) )
		{
			$pfnCbFunc( 'error', "Error in parameter [arrSrvList] in function _AreVersionExist." );
			return false;
		}

		$cSSHCmdRB	= new classes\CSSHCmdRollback();

		return $cSSHCmdRB->RollbackToVersion( $arrRollbackList, $pfnCbFunc );
	}

	private function _GetCurrentVersion( $arrVerList )
	{
		if ( ! is_array( $arrVerList ) || 0 == count( $arrVerList ) )
		{
			return '';
		}

		$sRet = '';

		foreach ( $arrVerList as $sVerNum => $sFullVer )
		{
			if ( 0 == strcmp( $sVerNum, $sFullVer ) )
			{
				$sRet = $sVerNum;
				break;
			}
		}

		return $sRet;
	}
}