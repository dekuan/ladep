<?php

namespace dekuan\ladep\models\classes;

use dekuan\vdata\CConst;
use dekuan\ladep\libs;
use dekuan\ladep\constants;


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
		return ( CConst::ERROR_SUCCESS == $this->_SetupConfigFile( 'app.php', $sReleaseDir, $cProject, $pfnCbFunc ) );
	}
	public function SetupConfigDatabase( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return ( CConst::ERROR_SUCCESS == $this->_SetupConfigFile( 'database.php', $sReleaseDir, $cProject, $pfnCbFunc ) );
	}
	public function SetupConfigXc( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		return ( CConst::ERROR_SUCCESS == $this->_SetupConfigFile( 'xc.php', $sReleaseDir, $cProject, $pfnCbFunc ) );
	}
	public function SetupConfigSession( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		$bRet	= false;
		$nCall	= $this->_SetupConfigFile( 'session.php', $sReleaseDir, $cProject, $pfnCbFunc );
		if ( CConst::ERROR_SUCCESS == $nCall ||
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


	public function SetupPublicHTAccess( $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		//
		//	copy file .htaccess to /public/.htaccess
		//
		return ( CConst::ERROR_SUCCESS == $this->_SetupPublicFile( '.htaccess', $sReleaseDir, $cProject, $pfnCbFunc ) );
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
		return $this->_SetupFile( 'config', $sConfigFilename, $sReleaseDir, $cProject, $pfnCbFunc );
	}
	private function _SetupPublicFile( $sPublicFilename, $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		//	.htaccess
		return $this->_SetupFile( 'public', $sPublicFilename, $sReleaseDir, $cProject, $pfnCbFunc );
	}
	private function _SetupFile( $sDestDirName, $sFilename, $sReleaseDir, CProject $cProject, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sFilename ) || 0 == strlen( $sFilename ) )
		{
			libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # Invalid filename : " . $sFilename );
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # Invalid Release Dir : " . $sReleaseDir );
			return CConst::ERROR_PARAMETER;
		}
		if ( ! $cProject instanceof CProject )
		{
			libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # Invalid instanceof of [cProject]" );
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

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
					$sSrc	= sprintf( "%s/%s", $sUrl, $sFilename );
					$sDst	= sprintf
					(
						"%s/%s%s",
						$sReleaseDir,
						( ( is_string( $sDestDirName ) && strlen( $sDestDirName ) > 0 ) ? ( trim( $sDestDirName ) . "/" ) : "" ),
						$sFilename
					);
					if ( is_file( $sSrc ) )
					{
						if ( copy( $sSrc, $sDst ) )
						{
							//
							//	copy successfully
							//
							$nRet = CConst::ERROR_SUCCESS;
						}
						else
						{
							$nRet = constants\CUConsts::CONST_ERROR_FAILED_COPY_FILE;
							libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # failed in copying file." );
						}
					}
					else
					{
						$nRet = constants\CUConsts::CONST_ERROR_FILE_NOT_EXIST;
						libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # source file does not exists : " . $sSrc );
					}
				}
				else if ( 0 == strcasecmp( 'ssh', $sType ) )
				{
					//
					//	todo
					//	copy files via ssh
					//
					libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # copying file via SSH is now not supported." );
				}
			}
			else
			{
				$nRet = constants\CUConsts::CONST_ERROR_CONFIG;
				libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # error in arrSrvConfig" );
			}
		}
		else
		{
			$nRet = constants\CUConsts::CONST_ERROR_CONFIG;
			libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # error in name" );
		}

		return $nRet;
	}

	private function _SetupHttpErrorsPage( $sReleaseDir, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "\t\t  # invalid ReleaseDir : " . __FUNCTION__ );
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
						libs\Lib::PrintByCallback( $pfnCbFunc, "info", "Create error page successfully: " . $sDstSubFile );
					}
					else
					{
						//
						//	an error occurred while copying file
						//
						$bRet = false;

						libs\Lib::PrintByCallback( $pfnCbFunc, "error", "Create error page unsuccessfully: " . $sDstSubFile );
					}
				}
				else
				{
					libs\Lib::PrintByCallback( $pfnCbFunc, "comment", "error page already exists : " . $sDstSubFile );
				}
			}
		}
		else
		{
			libs\Lib::PrintByCallback( $pfnCbFunc, "error", "Failed to load the list of http error pages." );
		}

		return $bRet;
	}
}