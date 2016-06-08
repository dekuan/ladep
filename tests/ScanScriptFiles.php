<?php

require_once ( __DIR__ . '/../src/lava/libs/Lib.php' );
require_once ( __DIR__ . '/../src/lava/models/compressores/CYUICompressor.php' );
require_once ( __DIR__ . '/../vendor/autoload.php' );
require_once ( __DIR__ . '/../vendor/xscn/xsconst/src/CConst.php' );


use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use dekuan\lava\libs;
use dekuan\lava\models\compressores;



/**
 * Created by PhpStorm.
 * User: xing
 * Date: 4/15/16
 * Time: 3:42 AM
 */
class ScanScriptFiles extends PHPUnit_Framework_TestCase
{
	public function testScanScriptFiles()
	{
		$sViewDir		= sprintf( "%s/tests/%s", getcwd(), 'views/' );
		//$sViewFullFilename	= sprintf( "%s/tests/%s", getcwd(), 'usersignin.blade.php' );
		$sWebRootDir		= '/Users/xing/wwwroot/websites/account/public/';

		$sViewFullFilename	= sprintf( "%s/tests/views/%s", getcwd(), 'usersignup.blade.php' );
		$this->_CreateCompressedView( $sViewFullFilename, $sWebRootDir );

//		$this->_CompressAllViews( $sViewDir, $sWebRootDir );
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _CompressAllViews( $sViewDir, $sWebRootDir )
	{
		if ( ! is_string( $sViewDir ) || ! is_dir( $sViewDir ) )
		{
			return null;
		}
		if ( ! is_string( $sWebRootDir ) || ! is_dir( $sWebRootDir ) )
		{
			return null;
		}

		//
		//	...
		//
		$arrFiles = dekuan\lava\libs\Lib::REnumerateDir( $sViewDir );

		if ( is_array( $arrFiles ) && count( $arrFiles ) > 0 )
		{
			print_r( $arrFiles );

			foreach ( $arrFiles as $sViewFFN )
			{
				$this->_CreateCompressedView( $sViewFFN, $sWebRootDir );
			}
		}
	}
	private function _CreateCompressedView( $sViewFullFilename, $sWebRootDir )
	{
		if ( ! is_string( $sViewFullFilename ) || ! is_file( $sViewFullFilename ) )
		{
			return false;
		}
		if ( ! is_string( $sWebRootDir ) || ! is_dir( $sWebRootDir ) )
		{
			return false;
		}

		//	...
		$cJsCompressor		= new compressores\CMakeCompressedJs();
		$cCssCompressor		= new compressores\CMakeCompressedCss();
		$arrMakeJsReturn	= [];
		$arrMakeCssReturn	= [];
		$nJsCompress		= -1;
		$nCssCompress		= -1;

		$nJsCompress	= $cJsCompressor->MakeCompressedView( $sViewFullFilename, $sWebRootDir, false, $arrMakeJsReturn, null );
		$nCssCompress	= $cCssCompressor->MakeCompressedView( $sViewFullFilename, $sWebRootDir, false, $arrMakeCssReturn, null );

		//	...
		echo "Compressing\t: $sViewFullFilename\r\n";

		if ( is_array( $arrMakeJsReturn ) )
		{
			if ( array_key_exists( 'all_in_one_ffn', $arrMakeJsReturn ) &&
				is_string( $arrMakeJsReturn['all_in_one_ffn'] ) &&
				is_file( $arrMakeJsReturn['all_in_one_ffn'] ) )
			{
				if ( array_key_exists( 'compressed_ffn', $arrMakeJsReturn ) &&
					is_string( $arrMakeJsReturn['compressed_ffn'] ) &&
					is_file( $arrMakeJsReturn['compressed_ffn'] ))
				{
					@unlink( $arrMakeJsReturn['compressed_ffn'] );
				}
				else
				{
					echo "\t\t  error in compress js / compressed file : error id = " . $nJsCompress . "\r\n";
				}

				@unlink( $arrMakeJsReturn['all_in_one_ffn'] );
			}
			else if ( compressores\CMakeCompressed::ERROR_EXTRACT_ORIGINAL_FILE_LIST_FROM_VIEW == $nJsCompress )
			{
				echo "\t\t  No js file(s) found\r\n";
			}
			else
			{
				echo "\t\t  error in compress js / create all-in-one file : error id = " . $nJsCompress . "\r\n";
			}
		}
		else
		{
			echo "\t\t  error in compress js : error id = " . $nJsCompress . "\r\n";
		}

		if ( is_array( $arrMakeCssReturn ) )
		{
			if ( array_key_exists( 'all_in_one_ffn', $arrMakeCssReturn ) &&
				is_string( $arrMakeCssReturn['all_in_one_ffn'] ) &&
				is_file( $arrMakeCssReturn['all_in_one_ffn'] ) )
			{
				if ( array_key_exists( 'compressed_ffn', $arrMakeCssReturn ) &&
					is_string( $arrMakeCssReturn['compressed_ffn'] ) &&
					is_file( $arrMakeCssReturn['compressed_ffn'] ))
				{
					@unlink( $arrMakeCssReturn['compressed_ffn'] );
				}
				else
				{
					echo "\t\t  error in compress css / compressed file : error id = " . $nCssCompress . "\r\n";
				}

				@unlink( $arrMakeCssReturn['all_in_one_ffn'] );
			}
			else if ( compressores\CMakeCompressed::ERROR_EXTRACT_ORIGINAL_FILE_LIST_FROM_VIEW == $nCssCompress )
			{
				echo "\t\t  No css file(s) found\r\n";
			}
			else
			{
				echo "\t\t  error in compress css / create all-in-one file : error id = " . $nCssCompress . "\r\n";
			}
		}
		else
		{
			echo "\t\t  error in compress css : error id = " . $nCssCompress . "\r\n";
		}

		return true;
	}
}
