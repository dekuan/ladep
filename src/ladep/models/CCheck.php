<?php

namespace dekuan\ladep\models;

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use dekuan\vdata;
use dekuan\ladep\libs;
use dekuan\ladep\models\classes;




/**
 *	class of CCheck
 */
class CCheck
{
	//	...
	const CONST_CHECK_SERVER_NAME	= 'LADEP CHECK WEB SERVICE';
	const CONST_CHECK_SERVER_HOST	= '127.0.0.1';	//	server host
	const CONST_CHECK_SERVER_PORT	= 9916;		//	server port

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
				//	...
				if ( $this->_IsCheckServerListening() )
				{
					$this->_StopCheckService( $pfnCbFunc );
				}

				if ( ! $this->_IsCheckServerListening() )
				{
					if ( $this->_StartCheckService( $sReleaseDir, $pfnCbFunc ) )
					{
						sleep( 3 );

						$nStatusCode	= 0;
						if ( $this->_IsWebsiteAvailable( $nStatusCode ) )
						{
							$nRet = 0;

							//	...
							$pfnCbFunc( 'info', "\t\t: Website works well!" );
						}
						else
						{
							$pfnCbFunc( 'comment', sprintf( "\t\t# Website is not available, status code is %d.", $nStatusCode ) );
						}

						//	...
						sleep( 1 );
						$this->_StopCheckService( $pfnCbFunc );
					}
					else
					{
						$pfnCbFunc( 'error', "\t\t# Failed to start check web service." );
					}
				}
				else
				{
					$pfnCbFunc( 'error', sprintf( "\t\t# Port %d already in use.", self::CONST_CHECK_SERVER_PORT ) );
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
	private function _IsCheckServerListening()
	{
		$bRet = false;

		//	...
		$nErrorId	= -1;
		$sErrorStr	= '';
		$nTimeout	= 3;

		try
		{
			$fp = fsockopen( self::CONST_CHECK_SERVER_HOST, self::CONST_CHECK_SERVER_PORT, $nErrorId, $sErrorStr, $nTimeout );
			if ( false !== $fp )
			{
				$bRet = true;

				fclose( $fp );
			}
		}
		catch ( \Exception $e )
		{}

		//	...
		return $bRet;
	}

	private function _IsWebsiteAvailable( & $nStatusCode = 0 )
	{
		$cRequest	= vdata\CRequest::GetInstance();

		//	...
		$bRet		= false;

		//	...
		$arrResp	= null;
		$sUrl		= $this->_GetCheckServiceUrl();

		$nCall		= $cRequest->HttpRaw
		(
			[
				'method'	=> 'GET',
				'url'		=> $sUrl,
			],
			$arrResp
		);
		if ( vdata\CConst::ERROR_SUCCESS == $nCall &&
			$cRequest->IsValidRawResponse( $arrResp ) )
		{
			//	...
			$nStatusCode	= $arrResp['status'];

			//	...
			if ( 200 == $arrResp['status'] )
			{
				$bRet = true;
			}
		}

		return $bRet;
	}

	private function _GetCheckServiceUrl()
	{
		return sprintf( "http://%s:%d", self::CONST_CHECK_SERVER_HOST, self::CONST_CHECK_SERVER_PORT );
	}

	private function _StartCheckService( $sReleaseDir, callable $pfnCbFunc )
	{
		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			$pfnCbFunc( 'error', "\t\t# Error in parameter [sReleaseDir] in " . __FUNCTION__ );
			return null;
		}

		//	...
		$bRet = false;

		//	...
		$sCmdLine	= sprintf
		(
			"php -S %s:%d -t \"%s/public\" > /dev/null 2>&1 &",
			self::CONST_CHECK_SERVER_HOST,
			self::CONST_CHECK_SERVER_PORT,
			libs\Lib::RTrimPath( $sReleaseDir )
		);

		//	...
		$cProcess	= new Process\Process( $sCmdLine );
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
				$pfnCbFunc( 'info', "\t\t: Start [" . self::CONST_CHECK_SERVER_NAME . "] successfully." );
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "\t\t: Failed to start [" . self::CONST_CHECK_SERVER_NAME . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}

		return $bRet;
	}

	private function _StopCheckService( callable $pfnCbFunc )
	{
		$bRet = false;

		if ( $this->_IsCheckServerListening() )
		{
			//	...
			$sCmdLine	= sprintf( "kill -9 $(lsof -t -i:%d) > /dev/null 2>&1", self::CONST_CHECK_SERVER_PORT );

			//	...
			$cProcess	= new Process\Process( $sCmdLine );
			$cProcess
				->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
				->enableOutput()
				->run()
			;

			if ( $cProcess->isSuccessful() )
			{
				$bRet = true;

				$pfnCbFunc( 'info', "\t\t: Stop [" . self::CONST_CHECK_SERVER_NAME . "] successfully." );
			}
			else
			{
				$pfnCbFunc( 'error', "\t\t: Failed to stop [" . self::CONST_CHECK_SERVER_NAME . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}
		else
		{
			$pfnCbFunc( 'info', "\t\t: [" . self::CONST_CHECK_SERVER_NAME . "] already stopped." );
		}

		return $bRet;
	}
}