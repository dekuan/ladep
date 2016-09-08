<?php

namespace dekuan\ladep\models\classes;

use dekuan\vdata\CConst;
use dekuan\ladep\libs;


class CStatus
{
	const KEY_READY		= 'ready';

	//	...
	private $m_arrData;

	public function __construct()
	{
		$this->m_arrData	= null;
	}
	public function __destruct()
	{
	}

	public function LoadStatus( $sReleaseDir )
	{
		//
		//	sTargetDir	- [in] full filename of configuration file
		//	sErrorPath	- [out/opt] the path while a error occurred
		//	RETURN		- error id based dekuan\vdata\CConst
		//
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return null;
		}

		/*
		{
			"ready"	: 1/0,
		}
		*/

		//	...
		$sFullFilename	= $this->_GetFullStatusFilename( $sReleaseDir );
		$arrData	= [];
		if ( is_string( $sFullFilename ) && is_file( $sFullFilename ) )
		{
			$arrData = @ json_decode( @ file_get_contents( $sFullFilename ), true );
		}
		if ( ! is_array( $arrData ) )
		{
			$arrData = [];
		}

		//	...
		$arrData['ready'] = intval( array_key_exists( 'ready', $arrData ) ? $arrData['ready'] : 0 );

		//	...
		$this->m_arrData = $arrData;

		//	...
		return $arrData;
	}

	public function IsReadyStatus( $sReleaseDir )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		$bRet = false;

		//	...
		$arrCfg	= $this->LoadStatus( $sReleaseDir );
		if ( is_array( $arrCfg ) )
		{
			$bRet = boolval( $this->GetStatus() );
		}

		return $bRet;
	}

	public function GetStatus()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'ready' ] : 0;
	}

	public function SaveStatus( $sReleaseDir, $bReady )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		$arrData = $this->LoadStatus( $sReleaseDir );
		if ( ! is_array( $arrData ) )
		{
			$arrData = [];
		}

		//	...
		$arrData[ self::KEY_READY ] = ( $bReady ? 1 : 0 );

		//	...
		$sFullFilename	= $this->_GetFullStatusFilename( $sReleaseDir );
		$nSaved		= @ file_put_contents( $sFullFilename, @ json_encode( $arrData ) );
		if ( false !== $nSaved && $nSaved > 0 )
		{
			$bRet = true;

			//	...
			$this->m_arrData = $arrData;
		}

		//	...
		return $bRet;
	}

	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _GetFullStatusFilename( $sReleaseDir )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return '';
		}

		return sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $sReleaseDir ),
			libs\Lib::TrimPath( libs\Config::Get( 'path_file_status' ) )
		);
	}
}