<?php

namespace dekuan\ladep\models;

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use dekuan\vdata;
use dekuan\ladep\libs;
use dekuan\ladep\models\classes;



/**
 *	class of CBuild
 */
class CBuild
{
	private $m_cProject	= null;


	public function __construct()
	{
		$this->m_cProject	= new classes\CProject();
	}
	public function __destruct()
	{
	}

	public function Run( $arrParameter, callable $pfnCbFunc )
	{
		$nRet		= -1;	//	dekuan\vdata\CConst::ERROR_UNKNOWN;
		$sErrorDesc	= '';
		$sErrorPath	= '';
		$cGit		= new classes\CGit();


		if ( ! is_callable( $pfnCbFunc ) )
		{
			throw new \RuntimeException( sprintf( "%s::%s pfnCbFunc is not callable function.", __CLASS__, __FUNCTION__ ) );
		}

		//	...
		$sProjectConfig	= array_key_exists( 'project_config', $arrParameter ) ? $arrParameter['project_config'] : '';
		$bObtainLastTag	= array_key_exists( 'last', $arrParameter ) ? boolval( $arrParameter['last'] ) : false;
		$bNoCompressJs	= array_key_exists( 'no-compress-js', $arrParameter ) ? boolval( $arrParameter['no-compress-js'] ) : false;
		$bNoCompressCss	= array_key_exists( 'no-compress-css', $arrParameter ) ? boolval( $arrParameter['no-compress-css'] ) : false;

		//	...
		$nErrorId = $this->m_cProject->Load( $sProjectConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$pfnCbFunc( 'info', sprintf( "Read project configurations successfully" ) );

			//	...
			$sProjectName		= $this->m_cProject->GetName();
			$sRepoUrl		= $this->m_cProject->GetRepoUrl();
			$sRepoVer		= $this->m_cProject->GetRepoVer();
			$arrSrvConfig		= $this->m_cProject->GetServerConfig();
			$arrSrvList		= $this->m_cProject->GetServerList();

			if ( $bObtainLastTag )
			{
				$pfnCbFunc( "info", "Try to obtain the last tag from remote repository" );
				$sRemoteLastTag	= $cGit->GetLastTagFromRemoteRepository( $sRepoUrl, $pfnCbFunc );
				if ( is_string( $sRemoteLastTag ) && strlen( $sRemoteLastTag ) )
				{
					$pfnCbFunc( "info", "\t\t  obtain the last tag [$sRemoteLastTag] successfully." );
					$sRepoVer = $sRemoteLastTag;
				}
				else
				{
					$pfnCbFunc( "comment", "\t\t  #Failed to obtain the last tag, still use the tag [$sRepoVer] from config." );
				}
			}

			//	...
			$bContinue	= false;
			$sErrorDesc	= '';
			$sDirNew	= libs\Lib::GetLocalReleasedVersionDir( $sProjectName, $sRepoVer );


			//
			//	make directory by version for release
			//
			//$pfnCbFunc( 'info', sprintf( "%s::%s _MakeDir", __CLASS__, __FUNCTION__ ) );
			if ( $this->_MakeDir( $sProjectName, $sRepoVer, $sDirNew, $pfnCbFunc ) )
			{
				//$pfnCbFunc( "info", "Run::_MakeDir successfully." );
				$bContinue = true;
			}
			else
			{
				$sFormat	= libs\Lang::Get( "error_create_dir" );
				$sErrorDesc	= sprintf( $sFormat, $sDirNew );
				$pfnCbFunc( "error", $sErrorDesc );
			}
			$pfnCbFunc( "info", "" );
			$pfnCbFunc( "info", "" );
			$pfnCbFunc( "info", "" );

			//
			//	Clone code from repository
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Cloning project from git repository" ) );
				if ( $this->_CloneCode( $sRepoUrl, $sRepoVer, $sDirNew, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "The project was cloned successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_clone_code" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	clean up files before composer install
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Cleaning up unnecessary files" ) );
				if ( $this->_CleanUpFilesBeforeComposerInstall( $sDirNew, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "unnecessary files were cleaned up successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_cleanup_files" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	create .env for new release
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Creating new .env file." ) );
				if ( $this->_CreateNewEnv( $sDirNew, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", ".evn file was created successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_create_env_file" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	setup config app
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Setting up config/app.php" ) );
				if ( $this->_SetupConfigApp( $sDirNew, $this->m_cProject, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "config/app.php was set up successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_setup_app" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}


			//
			//	setup config database as local default
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Setting up config/database.php as local" ) );
				if ( $this->_SetupConfigDatabaseAsLocal( $sDirNew, $this->m_cProject, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "config/database.php was set up as local successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_setup_database" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}


			//
			//	setup config session as local default
			//
//			if ( $bContinue )
//			{
//				$bContinue = false;
//				$pfnCbFunc( 'info', sprintf( "Setting up config/session.php as local" ) );
//				if ( $this->_SetupConfigSessionAsLocal( $sDirNew, $this->m_cProject, $pfnCbFunc ) )
//				{
//					$pfnCbFunc( "info", "config/session.php was set up as local successfully." );
//					$bContinue = true;
//				}
//				else
//				{
//					$sFormat	= libs\Lang::Get( "error_setup_session" );
//					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
//					$pfnCbFunc( "error", $sErrorDesc );
//				}
//				$pfnCbFunc( "info", "" );
//				$pfnCbFunc( "info", "" );
//				$pfnCbFunc( "info", "" );
//			}


			//
			//	composer install
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Run composer install -vvv" ) );
				if ( $this->_ComposerInstall( $sDirNew, $pfnCbFunc ) )
				{
					//$pfnCbFunc( "info", "_ComposerInstall successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_composer_install" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	composer update
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Run composer update -vvv" ) );
				if ( $this->_ComposerUpdate( $sDirNew, $pfnCbFunc ) )
				{
					//$pfnCbFunc( "info", "_ComposerUpdate successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_composer_update" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}


			//
			//	setup config database as production server
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Setting up config/database.php", __CLASS__, __FUNCTION__ ) );
				if ( $this->_SetupConfigDatabase( $sDirNew, $this->m_cProject, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "config/database.php was set up successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_setup_database" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	setup config session as production server
			//
			if ( $bContinue )
			{
				$bContinue = true;
				$pfnCbFunc( 'info', sprintf( "Setting up config/session.php", __CLASS__, __FUNCTION__ ) );
				if ( $this->_SetupConfigSession( $sDirNew, $this->m_cProject, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "config/session.php was set up successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_setup_session" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}


			//
			//	setup http error pages
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Setting up HTTP error pages." ) );
				if ( $this->_SetupHttpErrorsPage( $sDirNew, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "HTTP error pages were set up successfully." );
					$bContinue = true;
				}
				else
				{
					$pfnCbFunc( "error", "Failed to setup error pages, error in _SetupHttpErrorsPage." );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	compress js/css and inject into view automatically
			//
			if ( $bContinue )
			{
				$arrOptions	=
					[
						'no-compress-js'	=> $bNoCompressJs,
						'no-compress-css'	=> $bNoCompressCss,
					];
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Compressing all Javascript/css files and injecting them into views" ) );
				if ( $this->_CompressAndInjectFilesIntoView( $sProjectName, $sRepoVer, $arrOptions, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "Javascript/css files were compressed successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_compress_files" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}



		//	$bContinue	= true;
		//	$sSrcDir	= libs\Lib::GetLocalReleaseDir( $sProjectName ) . "/1.0.3-bak/";
		//	$sDstDir	= libs\Lib::GetLocalReleaseDir( $sProjectName ) . "/1.0.3/";
		//	system( "rm -rf \"$sDstDir\" && cp -r \"$sSrcDir\" \"$sDstDir\"" );

			//
			//	clean up files
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Cleaning up unnecessary files" ) );
				if ( $this->_CleanUpFilesAfterComposerInstall( $sDirNew, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "unnecessary files were cleaned up successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_cleanup_files" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	chmod files
			//
//			if ( $bContinue )
//			{
//				$bContinue = false;
//				$pfnCbFunc( 'info', sprintf( "Changing file modes" ) );
//				if ( $this->_ChangeFileModes( $sDirNew, $pfnCbFunc ) )
//				{
//					$pfnCbFunc( "info", "File modes were changed successfully." );
//					$bContinue = true;
//				}
//				else
//				{
//					$sFormat	= libs\Lang::Get( "error_cleanup_files" );
//					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
//					$pfnCbFunc( "error", $sErrorDesc );
//				}
//				$pfnCbFunc( "info", "" );
//				$pfnCbFunc( "info", "" );
//				$pfnCbFunc( "info", "" );
//			}

			//
			//	save status
			//
			if ( $bContinue )
			{
				$bContinue = false;
				$pfnCbFunc( 'info', sprintf( "Saving building status" ) );
				if ( $this->_SaveStatus( $sDirNew, 1, $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "Building status was saved successfully." );
					$bContinue = true;
				}
				else
				{
					$sFormat	= libs\Lang::Get( "error_save_status" );
					$sErrorDesc	= sprintf( $sFormat, $sRepoUrl );
					$pfnCbFunc( "error", $sErrorDesc );
				}
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
			}

			//
			//	everything is done
			//
			if ( $bContinue )
			{
				//	...
				$nRet = 0;
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", "" );
				$pfnCbFunc( "info", sprintf( "%s[%s] was built successfully!", $sProjectName, $sRepoVer ) );
			}
		}
		else if ( -100002 == $nErrorId )
		{
			$pfnCbFunc( 'error', sprintf( "Failed to load project, error : %s", libs\Lang::Get( "error_file_not_exists" ) ) );
		}
		else
		{
			$sFormat = libs\Lang::Get( "error_load_config" );
			$pfnCbFunc( 'error', sprintf( "Failed to load project, error : %s", sprintf( $sFormat, $sErrorPath ) ) );
		}

		//	...
		$pfnCbFunc( "info", "" );
		$pfnCbFunc( "info", "" );

		return $nRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	private function _MakeDir( $sProjectName, $sVer, & $sDirNew = null, callable $pfnCbFunc )
	{
		if ( ! is_string( $sProjectName ) || 0 == strlen( $sProjectName ) )
		{
			return false;
		}
		if ( ! is_string( $sVer ) || 0 == strlen( $sVer ) )
		{
			return false;
		}

		//	...
		$bRet		= false;
		$bMkDirRelease	= false;
		$bMkDirProject	= false;
		$sDirNew	= libs\Lib::GetLocalReleaseDir();
		if ( is_dir( $sDirNew ) )
		{
			$bMkDirRelease	= true;
		}
		else
		{
			$bMkDirRelease	= mkdir( $sDirNew );
		}

		if ( $bMkDirRelease )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", "@ Make dir : $sDirNew" );
			}

			//	...
			$sDirNew = libs\Lib::GetLocalReleasedProjectDir( $sProjectName );
			if ( is_dir( $sDirNew ) )
			{
				$bMkDirProject	= true;
			}
			else
			{
				$bMkDirProject	= mkdir( $sDirNew );
			}

			//	...
			if ( $bMkDirProject )
			{
				//	...
				$sDirNew = libs\Lib::GetLocalReleasedVersionDir( $sProjectName, $sVer );
				if ( is_dir( $sDirNew ) )
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( "info", "@ Remove dir : $sDirNew" );
					}
				}

				//	...
				if ( ! is_dir( $sDirNew ) || libs\Lib::RRmDir( $sDirNew ) )
				{
					if ( mkdir( $sDirNew ) )
					{
						$bRet = true;

						//	...
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "info", "@ Make dir successfully: $sDirNew" );
						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "error", "Failed to create dir: $sDirNew" );
						}
					}
				}
				else
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( "error", "Remove dir recursively failed : $sDirNew" );
					}
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "error", "The target dir already exists or mkdir failed: $sDirNew" );
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Failed to create dir or mkdir failed : $sDirNew" );
			}
		}

		//	...
		return $bRet;
	}
	private function _CloneCode( $sUrl, $sVer, $sReleaseDir, callable $pfnCbFunc )
	{
		$cGit = new classes\CGit();
		return $cGit->CloneCode( $sUrl, $sVer, $sReleaseDir, $pfnCbFunc );
	}


	private function _CreateNewEnv( $sReleaseDir, callable $pfnCbFunc )
	{
		$bRet	= false;
		$cEnv	= new classes\CEnv();

		//	...
		if ( $cEnv->CleanUpEnv( $sReleaseDir, $pfnCbFunc ) )
		{
			$bRet = true;
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", "Create new .env successfully." );
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Failed to create .env file." );
			}
		}

		return $bRet;
	}
	private function _ComposerInstall( $sReleaseDir, callable $pfnCbFunc )
	{
		if ( ! is_string( $sReleaseDir ) || ! is_dir( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$bRet		= false;
		$cComposer	= new classes\CComposer();

		$bCleanUp	= $cComposer->CleanUpComposer($sReleaseDir, $pfnCbFunc );
		if ( $bCleanUp )
		{
			//	...
			$sCommand = sprintf( "composer install -d %s -vvv", $sReleaseDir );
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", $sCommand );
			}

			$cProcess	= new Process\Process( $sCommand );
			$cProcess
				->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
				->enableOutput()
				->run( function( $sType, $sBuffer ) use ( $pfnCbFunc )
				{
					if ( Process\Process::OUT === $sType )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "info", trim( $sBuffer ) );
						}
					}
					else if ( Process\Process::ERR == $sType )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "comment", trim( $sBuffer ) );
						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "comment", trim( $sBuffer ) );
						}
					}

					return true;
				})
			;

			if ( $cProcess->isSuccessful() )
			{
				$bRet = true;
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "composer install successfully." );
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "error", $cProcess->getErrorOutput() );
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Falied to clean up composer.json" );
			}
		}

		return $bRet;
	}
	private function _ComposerUpdate( $sReleaseDir, callable $pfnCbFunc )
	{
		$bRet		= false;
		$cComposer	= new classes\CComposer();

		$bCleanUp	= $cComposer->CleanUpComposer( $sReleaseDir, $pfnCbFunc );
		if ( $bCleanUp )
		{
			//	...
			$sCommand	= sprintf( "composer update -d %s -vvv", $sReleaseDir );
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", $sCommand );
			}

			$cProcess	= new Process\Process( $sCommand );
			$cProcess
				->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
				->enableOutput()
				->run( function( $sType, $sBuffer ) use ( $pfnCbFunc )
				{
					if ( Process\Process::OUT === $sType )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "info", trim( $sBuffer ) );
						}
					}
					else if ( Process\Process::ERR == $sType )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "comment", trim( $sBuffer ) );
						}
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "comment", trim( $sBuffer ) );
						}
					}

					return true;
				})
			;

			if ( $cProcess->isSuccessful() )
			{
				$bRet = true;
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "info", "composer update successfully." );
				}
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "error", $cProcess->getErrorOutput() );
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Falied to clean up composer.json" );
			}
		}

		return $bRet;
	}

	private function _SetupConfigApp( $sReleaseDir, classes\CProject $cProject, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();

		return $cSetup->SetupConfigApp( $sReleaseDir, $cProject, $pfnCbFunc );
	}

	private function _SetupConfigDatabaseAsLocal( $sReleaseDir, classes\CProject $cProject, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();
		return $cSetup->SetupConfigDatabaseAsLocal( $sReleaseDir, $cProject, $pfnCbFunc );
	}

	private function _SetupConfigSessionAsLocal( $sReleaseDir, classes\CProject $cProject, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();
		return $cSetup->SetupConfigSessionAsLocal( $sReleaseDir, $cProject, $pfnCbFunc );
	}

	private function _SetupConfigDatabase( $sReleaseDir, classes\CProject $cProject, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();
		return $cSetup->SetupConfigDatabase( $sReleaseDir, $cProject, $pfnCbFunc );
	}
	private function _SetupConfigSession( $sReleaseDir, classes\CProject $cProject, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();
		return $cSetup->SetupConfigSession( $sReleaseDir, $cProject, $pfnCbFunc );
	}

	private function _SetupHttpErrorsPage( $sReleaseDir, callable $pfnCbFunc )
	{
		$cSetup	= new classes\CSetup();
		return $cSetup->SetupHttpErrorsPage( $sReleaseDir, $pfnCbFunc );
	}

	private function _CleanUpFilesBeforeComposerInstall( $sReleaseDir, callable $pfnCbFunc )
	{
		$cFile	= new classes\CFile();
		return $cFile->CleanUpFilesBeforeComposerInstall( $sReleaseDir, $pfnCbFunc );
	}
	private function _CleanUpFilesAfterComposerInstall( $sReleaseDir, callable $pfnCbFunc )
	{
		$cFile	= new classes\CFile();
		return $cFile->CleanUpFilesAfterComposerInstall( $sReleaseDir, $pfnCbFunc );
	}
	private function _ChangeFileModes( $sReleaseDir, callable $pfnCbFunc )
	{
		$cFile	= new classes\CFile();
		return $cFile->ChangeLocalFileModes( $sReleaseDir, $pfnCbFunc );
	}

	private function _SaveStatus( $sReleaseDir, $bReady, callable $pfnCbFunc )
	{
		$cStatus = new classes\CStatus();

		//	...
		return $cStatus->SaveStatus( $sReleaseDir, $bReady );
	}



	private function _CompressAndInjectFilesIntoView( $sProjectName, $sVer, $arrOptions, callable $pfnCbFunc )
	{
		$cCompressor = new classes\CCompressAndInject();

		//	...
		return $cCompressor->CompressAllViews( $sProjectName, $sVer, $arrOptions, $pfnCbFunc );
	}

}