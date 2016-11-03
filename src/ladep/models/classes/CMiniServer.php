<?php

namespace dekuan\ladep\models\classes;


use Symfony\Component\Process;
use dekuan\delib;
use dekuan\vdata;

use dekuan\ladep\libs;




/**
 *	class of CMiniServer
 */
class CMiniServer
{
	//	...
	const DEFAULT_CHECK_SERVER_NAME	= 'Ladep Mini-Server';
	const DEFAULT_CHECK_SERVER_HOST	= '127.0.0.1';	//	server host
	const DEFAULT_CHECK_SERVER_PORT	= 9916;		//	server port


	protected static $g_cStaticMiniServerInstance;

	private $m_sServerName;
	private $m_sServerHost;
	private $m_nServerPort;


	public function __construct()
	{
		$this->m_sServerName	= self::DEFAULT_CHECK_SERVER_NAME;
		$this->m_sServerHost	= self::DEFAULT_CHECK_SERVER_HOST;
		$this->m_nServerPort	= self::DEFAULT_CHECK_SERVER_PORT;
	}
	public function __destruct()
	{
	}
	static function GetInstance()
	{
		if ( is_null( self::$g_cStaticMiniServerInstance ) || ! isset( self::$g_cStaticMiniServerInstance ) )
		{
			self::$g_cStaticMiniServerInstance = new self();
		}
		return self::$g_cStaticMiniServerInstance;
	}


	//
	//	configuration
	//
	public function GetServerName()
	{
		return $this->m_sServerName;
	}
	public function SetServerName( $vVal )
	{
		if ( ! delib\CLib::IsExistingString( $vVal ) )
		{
			return false;
		}

		$this->m_sServerName = $vVal;
		return true;
	}

	public function GetServerHost()
	{
		return $this->m_sServerHost;
	}
	public function SetServerHost( $vVal )
	{
		if ( ! delib\CLib::IsExistingString( $vVal ) )
		{
			return false;
		}

		$this->m_sServerHost = $vVal;
		return true;
	}

	public function GetServerPort()
	{
		return $this->m_nServerPort;
	}
	public function SetServerPort( $vVal )
	{
		if ( ! is_numeric( $vVal ) )
		{
			return false;
		}

		$this->m_nServerPort = $vVal;
		return true;
	}


	public function SafeRestart( $sDocRoot, callable $pfnCbFunc )
	{
		$bRet = false;

		//
		//	try to stop mini http server
		//
		for ( $i = 0; $i < 3; $i ++ )
		{
			if ( $this->IsListening() )
			{
				$this->StopServer( $pfnCbFunc );
				sleep( 1 );
			}

			if ( ! $this->IsListening() )
			{
				break;
			}
		}

		//
		//	try to start mini http server
		//
		for ( $i = 0; $i < 3; $i ++ )
		{
			if ( ! $this->IsListening() )
			{
				$this->StartServer( $sDocRoot, $pfnCbFunc );
				sleep( 3 );
			}

			if ( $this->IsListening() )
			{
				$bRet = true;
				break;
			}
		}

		return $bRet;
	}

	public function IsListening()
	{
		$bRet = false;

		//	...
		$nErrorId	= -1;
		$sErrorStr	= '';
		$nTimeout	= 3;

		try
		{
			$fp = fsockopen( $this->m_sServerHost, $this->m_nServerPort, $nErrorId, $sErrorStr, $nTimeout );
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

	public function IsWebAvailable( & $nStatusCode = 0 )
	{
		$cRequest	= vdata\CRequest::GetInstance();

		//	...
		$bRet		= false;

		//	...
		$arrResp	= null;
		$sUrl		= $this->GetServerUrl();

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

	public function GetServerUrl()
	{
		return sprintf( "http://%s:%d", $this->m_sServerHost, $this->m_nServerPort );
	}

	public function StartServer( $sDocRoot, callable $pfnCbFunc )
	{
		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}
		if ( ! is_string( $sDocRoot ) || ! is_dir( $sDocRoot ) )
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
			$this->m_sServerHost,
			$this->m_nServerPort,
			libs\Lib::RTrimPath( $sDocRoot )
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
				$pfnCbFunc( 'info', "\t\t: Start [" . $this->m_sServerName . "] successfully." );
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( 'error', "\t\t: Failed to start [" . $this->m_sServerName . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}

		return $bRet;
	}

	public function StopServer( callable $pfnCbFunc )
	{
		$bRet = false;

		if ( $this->IsListening() )
		{
			//	...
			$sCmdLine	= sprintf( "kill -9 $(lsof -t -i:%d) > /dev/null 2>&1", $this->m_nServerPort );

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

				$pfnCbFunc( 'info', "\t\t: Stop [" . $this->m_sServerName . "] successfully." );
			}
			else
			{
				$pfnCbFunc( 'error', "\t\t: Failed to stop [" . $this->m_sServerName . "]." );
				$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
			}
		}
		else
		{
			$pfnCbFunc( 'info', "\t\t: [" . $this->m_sServerName . "] already stopped." );
		}

		return $bRet;
	}
}