<?php

namespace dekuan\lava\models\compressores;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use dekuan\lava\libs;



class CCompressor
{
	public function CreateCompressedFile( $sFullFilename, & $sOutputFullFilename = null, callable $pfnCbFunc = null )
	{
		//
		//	sFullFilename		- [in] string	the filename of all in one javascript
		//	sOutputFullFilename	- [out] string	the output filename
		//	RETURN
		//
		if ( ! is_string( $sFullFilename ) || ! is_file( $sFullFilename ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//
		//	/private/var/folders/yq/ccc3knfj4rg295hs9wrp1m8h8888gn/T/ladep_nS9dLo.js
		//	[
		//		'dirname'	=> '/private/var/folders/yq/ccc3knfj4rg295hs9wrp1m8h8888gn/T',
		//		'basename'	=> 'ladep_nS9dLo.js',
		//		'extension'	=> 'js',
		//		'filename'	=> 'ladep_nS9dLo'
		//	]
		//
		$arrPathInfo = @ pathinfo( $sFullFilename );
		if ( is_array( $arrPathInfo ) &&
			array_key_exists( 'dirname', $arrPathInfo ) &&
			array_key_exists( 'extension', $arrPathInfo ) &&
			array_key_exists( 'filename', $arrPathInfo ) &&
			strlen( $arrPathInfo[ 'dirname' ] ) > 0 &&
			strlen( $arrPathInfo[ 'extension' ] ) > 0 &&
			strlen( $arrPathInfo[ 'filename' ] ) > 0 &&
			(
				0 == strcasecmp( 'js', $arrPathInfo[ 'extension' ] ) ||
				0 == strcasecmp( 'css', $arrPathInfo[ 'extension' ] ) ||
				0 == strcasecmp( 'php', $arrPathInfo[ 'extension' ] ) ||
				0 == strcasecmp( 'html', $arrPathInfo[ 'extension' ] ) ||
				0 == strcasecmp( 'htm', $arrPathInfo[ 'extension' ] )
			) )
		{
			$sOutputFullFilename = sprintf
			(
				"%s/%s_min.%s",
				rtrim( $arrPathInfo['dirname'], "\r\n\t \\/" ),
				$arrPathInfo['filename'],
				$arrPathInfo['extension']
			);
			if ( is_string( $sOutputFullFilename ) &&
				strlen( $sOutputFullFilename ) > 0 )
			{
				//	...
				@ unlink( $sOutputFullFilename );

				//	...
				$sCmdLine = $this->GetCommandLine( $sFullFilename, $sOutputFullFilename );
				if ( is_string( $sCmdLine ) && strlen( $sCmdLine ) > 0 )
				{
					$cProcess = new Process\Process( $sCmdLine );
					$cProcess
						->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
						->enableOutput()
						->run()
					;

					//	...
					if ( $cProcess->isSuccessful() )
					{
						$bRet = true;

//						//
//						//	Removes multi-line comments and does not create
//						//	a blank line, also treats white spaces/tabs
//						//
//						$sCnt = @ file_get_contents( $sOutputFullFilename );
//						if ( is_string( $sCnt ) && strlen( $sCnt ) > 0 )
//						{
//							$sCnt = preg_replace( '!/\*.*?\*/!s', "", $sCnt );
//							$sCnt = preg_replace( '/\n\s*\n/', "\n", $sCnt );
//							@ file_put_contents( $sOutputFullFilename, $sCnt );
//						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "error", $cProcess->getErrorOutput() );
						}
					}

				}
			}
		}

		return $bRet;
	}


	public function GetCommandLine( $sAllInOneJsFFN, $sOutputFullFilename )
	{
		return '';
	}
	public function GetCompressorFullFilename()
	{
		return '';
	}

	public function GetCompressorFullFilenameByName( $sFilename )
	{
		if ( ! is_string( $sFilename ) || 0 == strlen( $sFilename ) )
		{
			return '';
		}

		//	...
		$sRet = '';

		//	...
		$sWorkingRootDir	= libs\Lib::GetLocalWorkingRootDir();
		$sWorkingJarFFN		= sprintf( "%s/%s", libs\Lib::RTrimPath( $sWorkingRootDir ), $sFilename );
		if ( is_file( $sWorkingJarFFN ) )
		{
			$sRet = $sWorkingJarFFN;
		}
		else
		{
			$sPharFFN = libs\Lib::GetFullPath( "/tools/$sFilename" );
			if ( false !== file_put_contents( $sWorkingJarFFN, @ file_get_contents( $sPharFFN ) ) )
			{
				$sRet = $sWorkingJarFFN;
			}
		}

		return $sRet;
	}

}