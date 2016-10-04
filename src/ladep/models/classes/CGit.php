<?php

namespace dekuan\ladep\models\classes;

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
			$sOutputStr	= strtolower( trim( $sOutputStr ) );
			if ( strstr( $sOutputStr, "git clone successfully" ) )
			{
				$bRet = true;
			}
			else if ( strstr( $sOutputStr, "you are in 'detached head' state." ) &&
				strstr( $sOutputStr, "git checkout -b <new-branch-name>" ) )
			{
				$bRet = true;
			}
			else if ( strstr( $sOutputStr, "you are in 'detached head' state." ) &&
				strstr( $sOutputStr, "git checkout -b new_branch_name" ) )
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


	//
	//	@ Public
	//	get last tag from remote repository
	//
	public function GetLastTagFromRemoteRepository( $sRepositoryUrl, $pfnCbFunc = null )
	{
		//
		//	sRepositoryUrl	- [in] the address of remote repository
		//	pfnCbFunc	- [in] callback function address
		//	RETURN		- string	the last tag
		//
		if ( ! is_string( $sRepositoryUrl ) || 0 == strlen( $sRepositoryUrl ) )
		{
			return '';
		}

		$sRet	= '';

		//
		//	...
		//
		$sFormat = "git ls-remote --tags \"%s\" | " .
				"awk -F '/' '{ print( length($3) \"/\" $3 ) }' | " .
				"grep -v '{}' | " .
				"sort -t '/' -gr | " .
				"sed -n '1p' | " .
				"awk -F '/' '{print($2)}'";
		$sCommand = sprintf( $sFormat, $sRepositoryUrl );
//		if ( is_callable( $pfnCbFunc ) )
//		{
//			$pfnCbFunc( "info", $sCommand );
//		}

		$cProcess = new Process\Process( $sCommand );
		$cProcess
			->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
			->enableOutput()
			->run();
		if ( $cProcess->isSuccessful() )
		{
			//	...
			$sRet = trim( $cProcess->getOutput() );
		}
		else
		{
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "comment", "\t\t  #Failed to obtain last tag from remote repository." );
			}
		}

		return $sRet;
	}

}