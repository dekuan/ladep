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
	const CONST_CHECK_SERVER_PORT	= 9916;	//	65525


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
			$sRepoVer		= $this->m_cProject->GetRepoVer();

			//	...
			$sErrorDesc	= '';
			$sReleaseDir	= libs\Lib::GetLocalReleasedVersionDir( $sProjectName, $sRepoVer );

			if ( is_string( $sReleaseDir ) && is_dir( $sReleaseDir ) )
			{
				//	...
				$this->_StopCheckService( $pfnCbFunc );
				if ( $this->_StartCheckService( $sReleaseDir, $pfnCbFunc ) )
				{
					$nStatusCode	= 0;
					if ( $this->_IsWebsiteAvailable( $nStatusCode ) )
					{
						$pfnCbFunc( 'info', "Website works well!" );
					}
					else
					{
						$pfnCbFunc( 'comment', sprintf( "# Website is not available, status code is %d.", $nStatusCode ) );
					}
				}
				else
				{
					$pfnCbFunc( 'error', "# Failed to start check web service." );
				}
			}
			else
			{
				$pfnCbFunc( 'error', sprintf( "# Release dir [%s] does not exist.", $sReleaseDir ) );
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
		return sprintf( "http://127.0.0.1:%d", self::CONST_CHECK_SERVER_PORT );
	}

	private function _StartCheckService( $sReleaseDir, callable $pfnCbFunc )
	{
		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			$pfnCbFunc( 'error', "# Error in parameter [sReleaseDir] in " . __FUNCTION__ );
			return null;
		}

		//	...
		$bRet = false;

		//	...
		$sCmdLine	= sprintf
		(
			"php -S 0.0.0.0:%d -t \"%s/public\" & echo //////// $! ////////",
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
				$pfnCbFunc( 'info', "\t\tStart [" . self::CONST_CHECK_SERVER_NAME . "] successfully." );
				$pfnCbFunc( 'info', "" );
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "\t\tFailed to start [" . self::CONST_CHECK_SERVER_NAME . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}

		return $bRet;
	}

	private function _StopCheckService( callable $pfnCbFunc )
	{
		$bRet = false;

		//	...
		$sCmdLine	= sprintf( "kill -9 $(lsof -i:%d -t)", self::CONST_CHECK_SERVER_PORT );

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
				$pfnCbFunc( 'info', "\t\tStop [" . self::CONST_CHECK_SERVER_NAME . "] successfully." );
				$pfnCbFunc( 'info', "" );
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "\t\tFailed to stop [" . self::CONST_CHECK_SERVER_NAME . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}

		return $bRet;
	}
}