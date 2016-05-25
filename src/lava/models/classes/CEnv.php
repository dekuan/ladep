<?php

namespace dekuan\lava\models\classes;

use dekuan\lava\libs;


/**
 *	class of CEnv
 */
class CEnv
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function CleanUpEnv( $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_CreateNewEvnFile( $sReleaseDir, $pfnCbFunc );
	}



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _CreateNewEvnFile( $sReleaseDir, callable $pfnCbFunc = null )
	{
		//	...
		\Phar::interceptFileFuncs();

		//	...
		$sDefaultFullFilename	= libs\Lib::GetFullPath( "/resources/env.default" );
		$sNewEnvFullFilename	= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $sReleaseDir ),
			libs\Config::Get( 'path_file_env' )
		);
		$sRandom = libs\Lib::GetRandomString( 32 );

		return $this->_SetupRandomAppKey( $sDefaultFullFilename, $sNewEnvFullFilename, $sRandom, $pfnCbFunc );
	}

	private function _SetupRandomAppKey( $sSrcFullFilename, $sDstFullFilename, $sRandom, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sSrcFullFilename ) || 0 == strlen( $sSrcFullFilename ) || ! is_file( $sSrcFullFilename ) )
		{
			return false;
		}
		if ( ! is_string( $sDstFullFilename ) || 0 == strlen( $sDstFullFilename ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		if ( ! is_string( $sRandom ) || 0 == strlen( $sRandom ) )
		{
			$sRandom = libs\Lib::GetRandomString( 32 );
		}

		if ( is_callable( $pfnCbFunc ) )
		{
			$pfnCbFunc( "info", "Generate random APP_KEY [$sRandom]." );
		}

		$sContent = file_get_contents( $sSrcFullFilename );
		if ( is_string( $sContent ) && strlen( $sContent ) > 0 )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", "Load default file successfully : " . strlen( $sContent ) );
			}

			//
			//	...
			//
			$sContent = str_replace( "[SOMERANDOMSTRING]", $sRandom, $sContent );

			//	...
			if ( false !== file_put_contents( $sDstFullFilename, $sContent ) )
			{
				$bRet = true;

				//	...
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "Create new file successfully : " . $sDstFullFilename );
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "error", "Failed to create new file : " . $sDstFullFilename );
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Default file not exists : " . $sSrcFullFilename );
			}
		}

		return $bRet;
	}
}