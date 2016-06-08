<?php

namespace dekuan\lava\models\classes;

use xscn\xsconst;
use dekuan\lava\libs;
use dekuan\lava\constants;


/**
 *	class of CSetup
 */
class CSetup
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function SetupConfigApp( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return ( xsconst\CConst::ERROR_SUCCESS == $this->_SetupConfigFile( 'app.php', $sReleaseDir, $cProject, $pfnCbFunc ) );
	}
	public function SetupConfigDatabase( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return ( xsconst\CConst::ERROR_SUCCESS == $this->_SetupConfigFile( 'database.php', $sReleaseDir, $cProject, $pfnCbFunc ) );
	}
	public function SetupConfigSession( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		$bRet	= false;
		$nCall	= $this->_SetupConfigFile( 'session.php', $sReleaseDir, $cProject, $pfnCbFunc );
		if ( xsconst\CConst::ERROR_SUCCESS == $nCall ||
			constants\CUConsts::CONST_ERROR_FAILED_COPY_FILE == $nCall )
		{
			$bRet = true;
		}

		return $bRet;
	}


	public function SetupConfigDatabaseAsLocal( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return $this->_SetupConfigDatabaseAsLocal( $sReleaseDir, $cProject, $pfnCbFunc );
	}
	public function SetupConfigSessionAsLocal( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return $this->_SetupConfigSessionAsLocal( $sReleaseDir, $cProject, $pfnCbFunc );
	}
	public function SetupHttpErrorsPage( $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_SetupHttpErrorsPage( $sReleaseDir, $pfnCbFunc );
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _SetupConfigDatabaseAsLocal( $sReleaseDir, $cProject, $pfnCbFunc )
	{
		//	...
		\Phar::interceptFileFuncs();

		//	...
		$sFFNDefault	= libs\Lib::GetFullPath( "/resources/database.php.default" );
		$sFFNNew	= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $sReleaseDir ),
			'config/database.php'
		);

		//	...
		@ unlink( $sFFNNew );

		//	...
		return copy( $sFFNDefault, $sFFNNew );
	}
	private function _SetupConfigSessionAsLocal( $sReleaseDir, $cProject, $pfnCbFunc )
	{
		//	...
		\Phar::interceptFileFuncs();

		//	...
		$sFFNDefault	= libs\Lib::GetFullPath( "/resources/session.php.default" );
		$sFFNNew	= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $sReleaseDir ),
			'config/session.php'
		);

		//	...
		@ unlink( $sFFNNew );

		//	...
		return copy( $sFFNDefault, $sFFNNew );
	}

	private function _SetupConfigFile( $sConfigFilename, $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sConfigFilename ) || 0 == strlen( $sConfigFilename ) )
		{
			return xsconst\CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return xsconst\CConst::ERROR_PARAMETER;
		}
		if ( ! $cProject instanceof CProject )
		{
			return xsconst\CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = xsconst\CConst::ERROR_UNKNOWN;

		//	...
		$sName		= $cProject->GetName();
		$arrSrvConfig	= $cProject->GetServerConfig();

		if ( is_string( $sName ) && strlen( $sName ) > 0 )
		{
			if ( is_array( $arrSrvConfig ) && count( $arrSrvConfig ) > 0 )
			{
				$sSrc	= '';
				$sDst	= '';
				$sUrl	= array_key_exists( 'url', $arrSrvConfig ) ? $arrSrvConfig['url'] : null;
				$sType	= array_key_exists( 'type', $arrSrvConfig ) ? $arrSrvConfig['type'] : null;

				if ( 0 == strcasecmp( 'file', $sType ) )
				{
					$sSrc	= sprintf( "%s/%s", $sUrl, $sConfigFilename );
					$sDst	= sprintf( "%s/config/%s", $sReleaseDir, $sConfigFilename );
					if ( is_file( $sSrc ) )
					{
						if ( copy( $sSrc, $sDst ) )
						{
							//
							//	copy successfully
							//
							$nRet = xsconst\CConst::ERROR_SUCCESS;
						}
						else
						{
							$nRet = constants\CUConsts::CONST_ERROR_FAILED_COPY_FILE;
						}
					}
					else
					{
						$nRet = constants\CUConsts::CONST_ERROR_FILE_NOT_EXIST;
					}
				}
				else if ( 0 == strcasecmp( 'ssh', $sType ) )
				{
					//
					//	todo
					//	copy files via ssh
					//
				}
			}
			else
			{
				$nRet = constants\CUConsts::CONST_ERROR_CONFIG;
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "comment", "\t\t  # error in arrSrvConfig" );
				}
			}
		}
		else
		{
			$nRet = constants\CUConsts::CONST_ERROR_CONFIG;
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "comment", "\t\t  # error in name" );
			}
		}

		return $nRet;
	}

	private function _SetupHttpErrorsPage( $sReleaseDir, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		$arrErrorFiles	= libs\Config::Get( 'path_http_error_files', null );
		if ( is_array( $arrErrorFiles ) && count( $arrErrorFiles ) > 0 )
		{
			//	...
			$bRet = true;

			foreach ( $arrErrorFiles as $sSrcSubFile => $sDstSubFile )
			{
				$sSrcFFN	= libs\Lib::GetFullPath( $sSrcSubFile );
				$sDstFFN	= sprintf( "%s/%s", libs\Lib::RTrimPath( $sReleaseDir ), $sDstSubFile );

				if ( ! is_file( $sDstFFN ) )
				{
					if ( copy( $sSrcFFN, $sDstFFN ) )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "info", "Create error page successfully: " . $sDstSubFile );
						}
					}
					else
					{
						//
						//	an error occurred while copying file
						//
						$bRet = false;

						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "error", "Create error page unsuccessfully: " . $sDstSubFile );
						}
					}
				}
				else
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( "comment", "error page already exists : " . $sDstSubFile );
					}
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Failed to load the list of http error pages." );
			}
		}

		return $bRet;
	}

}