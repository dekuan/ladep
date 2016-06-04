<?php

namespace dekuan\lava\models\classes;

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use xscn\xsconst;
use dekuan\lava\libs;
use dekuan\lava\models\classes;


/**
 *	class of CGit
 */
class CGit
{
	public function CloneCode( $sRepositoryUrl, $sVer, $sReleaseDir, callable $pfnCbFunc )
	{
		if ( ! is_string( $sRepositoryUrl ) || 0 == strlen( $sRepositoryUrl ) )
		{
			return false;
		}
		if ( ! is_string( $sVer ) || 0 == strlen( $sVer ) )
		{
			return false;
		}
		if ( ! is_string( $sReleaseDir ) || 0 == strlen( $sReleaseDir ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		//	git checkout -b 1.0.0
		$sCommand = sprintf( "git clone --branch \"%s\" \"%s\" \"%s\"", $sVer, $sRepositoryUrl, $sReleaseDir );
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
			$sOutputStr	= $cProcess->getErrorOutput();
			if ( strstr( $sOutputStr, "git clone successfully" ) )
			{
				$bRet = true;
			}
			else if ( strstr( $sOutputStr, "You are in 'detached HEAD' state." ) &&
				strstr( $sOutputStr, "git checkout -b <new-branch-name>" ) )
			{
				$bRet = true;
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", $cProcess->getErrorOutput() );
			}
		}

		return $bRet;
	}


	public function GetLastTag( $sRepositoryUrl, $sReleaseDir, callable $pfnCbFunc )
	{
		//
		//	1,
		//	If you don't see latest tag, make sure of fetching origin before running that:
		//	git remote update
		//
		//	3,
		//	git describe --abbrev=0 --tags
		//
		//	error of no tags:
		//		fatal: No names found, cannot describe anything.
		//
		if ( ! is_string( $sRepositoryUrl ) || 0 == strlen( $sRepositoryUrl ) )
		{
			return '';
		}
		if ( ! is_string( $sReleaseDir ) || 0 == strlen( $sReleaseDir ) )
		{
			return '';
		}

		$sRet	= '';

		//
		//	...
		//
		$sCommand = sprintf( "git remote update \"%s\" \"%s\"", $sRepositoryUrl, $sReleaseDir );
		if ( is_callable( $pfnCbFunc ) )
		{
			$pfnCbFunc( "info", $sCommand );
		}

		$cProcessUpdate = new Process\Process( $sCommand );
		$cProcessUpdate
			->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
			->enableOutput()
			->run();
		if ( $cProcessUpdate->isSuccessful() )
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "info", "\t\t  git remote update successfully." );
			}

			//
			//
			//
			$sCommand = sprintf( "git describe --abbrev=0 --tags \"%s\" \"%s\"", $sRepositoryUrl, $sReleaseDir );
			$cProcessGetLastTag = new Process\Process( $sCommand );
			$cProcessGetLastTag
				->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
				->enableOutput()
				->run();
			if ( $cProcessGetLastTag->isSuccessful() )
			{
				//	...
				$sRet = trim( $cProcessGetLastTag->getOutput() );
			}
			else
			{
				if ( is_callable( $pfnCbFunc ) )
				{
					$pfnCbFunc( "comment", "\t\t  #Failed to execute git describe --abbrev=0 --tags" );
				}
			}
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "comment", "\t\t  #Failed to execute git remote update." );
			}
		}

		return $sRet;
	}

}