<?php

namespace dekuan\lava\libs;


class Lang
{
	static function Get( $sLangId )
	{
		if ( ! is_string( $sLangId ) || empty( $sLangId ) )
		{
			return '';
		}

		//	...
		$sRet		= '';
		$sFullFilename	= Lib::GetFullPath( sprintf( "/lang/%s.php", self::GetLangCode() ) );
		$arrResult	= Lib::LoadArray( $sFullFilename );
		if ( is_array( $arrResult ) &&
			array_key_exists( $sLangId, $arrResult ) )
		{
			$sRet = $arrResult[ $sLangId ];
		}

		return $sRet;
	}

	static function GetLangCode()
	{
		$sRet		= '';
		$sLangCode	= Config::Get( 'langcode' );
		if ( is_string( $sLangCode ) && 3 == strlen( $sLangCode ) &&
			preg_match( "/[0-9a-z]{3}/", $sLangCode ) )
		{
			$sRet = $sLangCode;
		}

		return $sRet;
	}

}