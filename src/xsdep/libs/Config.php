<?php

namespace xscn\xsdep\libs;


class Config
{
	static function Get( $sConfigId, $vDefault = null )
	{
		if ( ! is_string( $sConfigId ) || 0 == strlen( $sConfigId ) )
		{
			return '';
		}

		//	...
		$vRet		= $vDefault;
		$sFullFilename	= Lib::GetFullPath( "/config/app.php" );
		$arrResult	= Lib::LoadArray( $sFullFilename );

		if ( is_array( $arrResult ) &&
			array_key_exists( $sConfigId, $arrResult ) )
		{
			$vRet = $arrResult[ $sConfigId ];
		}

		return $vRet;
	}
}