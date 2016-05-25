<?php

namespace dekuan\lava\models\classes;

use Symfony\Component\Process;

use dekuan\lava\libs;


/**
 *	class of CFile
 */
class CFile
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function CleanUpFilesBeforeComposerInstall( $sReleaseDir, callable $pfnCbFunc = null )
	{
		$this->_CleanupLaravelFilesBeforeComposerInstall
		(
			$sReleaseDir,
			function( $bRRmDirSucc, $sDir ) use ( $pfnCbFunc )
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					if ( $bRRmDirSucc )
					{
						$pfnCbFunc( 'info', "@ Clean dir : $sDir" );
					}
					else
					{
						$pfnCbFunc( 'comment', "# Clean dir : $sDir" );
					}
				}
			}
		);

		return true;
	}
	public function CleanUpFilesAfterComposerInstall( $sReleaseDir, callable $pfnCbFunc = null )
	{
		$this->_CleanupLaravelFilesAfterComposerInstall
		(
			$sReleaseDir,
			function( $bRRmDirSucc, $sDir ) use ( $pfnCbFunc )
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					if ( $bRRmDirSucc )
					{
						$pfnCbFunc( 'info', "@ Clean dir : $sDir" );
					}
					else
					{
						$pfnCbFunc( 'comment', "# Clean dir : $sDir" );
					}
				}
			}
		);

		$this->_CleanupOtherFiles
		(
			$sReleaseDir,
			function( $bRRmDirSucc, $sDir ) use ( $pfnCbFunc )
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					if ( $bRRmDirSucc )
					{
						$pfnCbFunc( 'info', "@ Clean files in dir : $sDir" );
					}
					else
					{
						$pfnCbFunc( 'comment', "# Clean files in dir : $sDir" );
					}
				}
			}
		);

		return true;
	}

	public function ChangeLocalFileModes( $sReleaseDir, callable $pfnCbFunc = null )
	{
		$this->_ChangeLocalFileModes
		(
			$sReleaseDir,
			function( $bSucc, $sDir ) use ( $pfnCbFunc )
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					if ( $bSucc )
					{
						$pfnCbFunc( 'info', "@ local chmod : $sDir" );
					}
					else
					{
						$pfnCbFunc( 'comment', "# local chmod : $sDir" );
					}
				}
			}
		);

		return true;
	}

	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _CleanupLaravelFilesBeforeComposerInstall( $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_CleanupLaravelFiles
		(
			'path_cleanup_full_before_composer_install',
			$sReleaseDir,
			$pfnCbFunc
		);
	}
	private function _CleanupLaravelFilesAfterComposerInstall( $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_CleanupLaravelFiles
		(
			'path_cleanup_full_after_composer_install',
			$sReleaseDir,
			$pfnCbFunc
		);
	}

	private function _CleanupLaravelFiles( $sAppCfgKey, $sReleaseDir, callable $pfnCbFunc = null )
	{
		//
		//	sTargetDir	- target dir
		//	pfnCbFunc	- callback function
		//	RETURN		- the number of directories has been cleaned up successfully.
		//			  -1 if a error was occurred
		//
		if ( ! is_string( $sAppCfgKey ) || 0 == strlen( $sAppCfgKey ) )
		{
			return -1;
		}
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return -1;
		}

		//	...
		$nRet = -1;
		$arrList = libs\Config::Get( $sAppCfgKey, null );

		if ( is_array( $arrList ) && count( $arrList ) > 0 )
		{
			$nRet = 0;
			foreach ( $arrList as $sSubDir => $bRemoveRootDir )
			{
				$bSucc	= false;
				$sFFN	= sprintf( "%s/%s", libs\Lib::RTrimPath( $sReleaseDir ), $sSubDir );

				//	...
				if ( is_dir( $sFFN ) )
				{
					$bSucc = libs\Lib::RRmDir( $sFFN, ( ! $bRemoveRootDir ) );
				}
				else
				{
					$bSucc = @ unlink( $sFFN );
				}

				$nRet += ( $bSucc ? 1 : 0 );
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( $bSucc, $sFFN );
				}
			}
		}

		return $nRet;
	}
	private function _CleanupOtherFiles( $sReleaseDir, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$arrDirs	= libs\Config::Get( 'path_cleanup_matched_dirs', null );
		$arrFiles	= libs\Config::Get( 'path_cleanup_matched_files', null );

		if ( is_array( $arrDirs ) && count( $arrDirs ) > 0 )
		{
			foreach ( $arrDirs as $sDirName => $bRemoveRootDir )
			{
				libs\Lib::RRmDirByName
				(
					$sReleaseDir,
					( ! $bRemoveRootDir ),
					$sDirName,
					0,
					function( $bRRmSucc, $sDir ) use ( $pfnCbFunc )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( $bRRmSucc, $sDir );
						}
					}
				);
			}
		}
		if ( is_array( $arrFiles ) && count( $arrFiles ) > 0 )
		{
			foreach ( $arrFiles as $sFileName => $bRemoveRootDir )
			{
				libs\Lib::RRmFileByName
				(
					$sReleaseDir,
					( ! $bRemoveRootDir ),
					$sFileName,
					0,
					function( $bRRmSucc, $sFFN ) use ( $pfnCbFunc )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( $bRRmSucc, $sFFN );
						}
					}
				);
			}
		}

		//	...
		return true;
	}

	private function _ChangeLocalFileModes( $sReleaseDir, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$arrDirs	= libs\Config::Get( 'path_chmod_dirs', null );

		if ( is_array( $arrDirs ) && count( $arrDirs ) > 0 )
		{
			foreach ( $arrDirs as $sDirName => $nAttrVal )
			{
				$sPath		= sprintf( "%s/%s", libs\Lib::RTrimPath( $sReleaseDir ), libs\Lib::LTrimPath( $sDirName ) );
				$sCommand	= sprintf( "chmod -R %d \"%s\"", $nAttrVal, $sPath );

				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( true, $sPath );
				}

				$cProcess = new Process\Process( $sCommand );
				$cProcess
					->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
					->enableOutput()
					->run( function( $sType, $sBuffer ) use ( $pfnCbFunc, $sPath )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( true, $sPath );
						}
						return true;
					})
				;
			}
		}

		//	...
		return true;
	}
}