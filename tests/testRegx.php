<?php

@ ini_set( 'date.timezone', 'Etc/GMT＋0' );
@ date_default_timezone_set( 'Etc/GMT＋0' );

@ ini_set( 'display_errors',	'on' );
@ ini_set( 'max_execution_time',	'60' );
@ ini_set( 'max_input_time',	'0' );
@ ini_set( 'memory_limit',	'512M' );

//	mb 环境定义
mb_internal_encoding( "UTF-8" );

//	Turn on output buffering
ob_start();


require_once( dirname( __DIR__ ) . "/vendor/autoload.php" );

/**
 * Created by PhpStorm.
 * User: xing
 * Date: 5/24/16
 * Time: 10:09 PM
 */
class testRegx extends PHPUnit_Framework_TestCase
{
	//	lava="1"
	const CONST_REGX_LADEP		= "/lava[ ]*=[ ]*[\"']{0,1}[ ]*1[ ]*[\"']{0,1}/i";


	const CONST_REGX_SCRIPT = "/<script.*?" .
				"src[ ]*=[ ]*[\"']{0,1}[\{\{asset\(']*([^'\"]+)['\)\}\}]*[ ]*[\"']{0,1}.*?>[ ]*" .
				"<\/[ ]*script[ ]*>/i";

	const CONST_LABEL_COMPRESSED_SCRIPT	= "<script compressed=\"lava\">";
	const CONST_LABEL_COMPRESSED_STYLE	= "<style compressed=\"lava\">";


	public function testRegx1()
	{
		$arrResult = [];
		$arrLines =
			[
				"\r\n<script language=\"JavaScript\" src=\"/js/jweixin-1.0.0.js\"></script>\r\n",
				"\r\n<script language=\"JavaScript\" src=\"/js/jweixin-1.0.0.js\" lava='1'></script>\r\n",
				"\r\n<script language=\"JavaScript\" src=\"/js/jweixin-1.0.0.js\" lava=1></script>\r\n",
				"\r\n<script language=\"JavaScript\" src=\"/js/jweixin-1.0.0.js\" lava=\"1\"></script>\r\n",
			];

		foreach ( $arrLines as $sLine )
		{
			$arrResult[] = $this->_IsMatchedLine( self::CONST_REGX_SCRIPT, $sLine );
		}

		var_dump( $arrResult );
	}




	private function _IsMatchedLine( $sRegx, $sLine )
	{
		if ( ! is_string( $sRegx ) || 0 == strlen( $sRegx ) )
		{
			return false;
		}
		if ( ! is_string( $sLine ) || 0 == strlen( $sLine ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		$arrMatches	= null;
		$nMatchCount	= preg_match( $sRegx, $sLine, $arrMatches );

		if ( false !== $nMatchCount &&
			$nMatchCount > 0 &&
			is_array( $arrMatches ) &&
			count( $arrMatches ) > 1 &&
			( ! strstr( $sLine, self::CONST_LABEL_COMPRESSED_SCRIPT ) &&
				! strstr( $sLine, self::CONST_LABEL_COMPRESSED_STYLE ) ) )
		{
			//
			//	matched "lava=1"
			//
			if ( 1 == preg_match( self::CONST_REGX_LADEP, $sLine ) )
			{
				$bRet = true;
			}
		}

		//	...
		return $bRet;
	}
}
