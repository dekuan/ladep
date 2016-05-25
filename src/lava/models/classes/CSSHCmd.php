<?php

namespace dekuan\lava\models\classes;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use dekuan\lava\libs;



class CSSHCmd
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Protected
	//

	protected function _IsValidItem( $arrItem )
	{
		$cPj	= new CProject();

		$bRet = false;

		if ( is_array( $arrItem ) )
		{
			if ( array_key_exists( 'host', $arrItem ) &&
				array_key_exists( 'user', $arrItem ) &&
				array_key_exists( 'pwd', $arrItem ) &&
				array_key_exists( 'path', $arrItem ) )
			{
				if ( is_string( $arrItem['host'] ) && strlen( $arrItem['host'] ) > 0 &&
					is_string( $arrItem['user'] ) && strlen( $arrItem['user'] ) > 0 &&
					is_string( $arrItem['pwd'] ) &&
					is_string( $arrItem['path'] ) && strlen( $arrItem['path'] ) > 0 )
				{
					$bRet = true;
				}
			}
		}

		return $bRet;
	}

	protected function _GetCommandSSH( $sUser, $sHost )
	{
		if ( ! is_string( $sUser ) || 0 == strlen( $sUser ) )
		{
			return '';
		}
		if ( ! is_string( $sHost ) || 0 == strlen( $sHost ) )
		{
			return '';
		}
		return sprintf( "ssh %s@%s", trim( $sUser ), trim( $sHost ) );
	}

	protected function _GetCommandMakeTarArchive( $sCmdSSH, $sSourceDir, $sTarFFN, $bRemoveSource = true )
	{
		//
		//	sCmdSSH		- [in] string,	ssh command line
		//	sSourceDir	- [in] string,	source directory
		//	sTarFFN		- [in] string,	the full filename of the new archived file
		//
		//	tar -zcf "1.0.30-_-20160510131644.tar" "1.0.30-_-20160510131644"
		//
		if ( ! is_string( $sCmdSSH ) || 0 == strlen( $sCmdSSH ) )
		{
			return '';
		}
		if ( ! is_string( $sSourceDir ) || 0 == strlen( $sSourceDir ) )
		{
			return '';
		}
		if ( ! is_string( $sTarFFN ) || 0 == strlen( $sTarFFN ) )
		{
			return '';
		}

		//	...
		return trim( sprintf
		(
			"%s tar -zcf \"%s\" -C \"%s\" . %s",
			$sCmdSSH,
			$sTarFFN,
			$sSourceDir,
			( $bRemoveSource ? "--remove-files" : "" )
		) );
	}

	protected function _GetCommandUnpackArchivedTar( $sCmdSSH, $sTarFFN, $sTargetDir, $bRemoveSource = true )
	{
		//
		//	sCmdSSH		- [in] string,	ssh command line
		//	sTarFFN		- [in] string,	the full filename of the new archived file
		//	sTargetDir	- [in] string,	target directory to store unpacked files
		//
		//	tar -xvf "/var/www/client.xs.cn/releases/aaa/1.0.30-_-20160510131644.tar" -C "/var/www/client.xs.cn/releases/aaa/"
		//
		if ( ! is_string( $sCmdSSH ) || 0 == strlen( $sCmdSSH ) )
		{
			return '';
		}
		if ( ! is_string( $sTarFFN ) || 0 == strlen( $sTarFFN ) )
		{
			return '';
		}
		if ( ! is_string( $sTargetDir ) || 0 == strlen( $sTargetDir ) )
		{
			return '';
		}

		//	...
		$arrCmdList	= [];

		//	...
		$arrCmdList[]	= sprintf
		(
			"%s mkdir -pv %s/",
			$sCmdSSH,
			libs\Lib::RTrimPath( $sTargetDir )
		);
		$arrCmdList[]	= sprintf
		(
			"%s tar -xvf \"%s\" -C \"%s/\"",
			$sCmdSSH,
			libs\Lib::RTrimPath( $sTarFFN ),
			libs\Lib::RTrimPath( $sTargetDir )
		);
		if ( $bRemoveSource )
		{
			//	remove the source file
			$arrCmdList[] = sprintf( "%s rm \"%s\"", $sCmdSSH, libs\Lib::RTrimPath( $sTarFFN ) );
		}

		return implode( " && ", $arrCmdList );
	}

	protected function _GetCommandRenew( $sCmdSSH, $sDirWwwroot, $sRepoVer, & $sLastVersionDirReturn = '' )
	{
		if ( ! is_string( $sCmdSSH ) || 0 == strlen( $sCmdSSH ) )
		{
			return '';
		}
		if ( ! is_string( $sDirWwwroot ) || 0 == strlen( $sDirWwwroot ) )
		{
			return '';
		}
		if ( ! is_string( $sRepoVer ) || 0 == strlen( $sRepoVer ) )
		{
			return '';
		}

		//	...
		$sRet	= '';

		//	...
		$arrCmdList	= [];
		$sTargetDir	= $this->_ReadSymbolLink( $sCmdSSH, $sDirWwwroot );

		if ( is_string( $sTargetDir ) &&
			strlen( $sTargetDir ) > 0 )
		{
			$sBaseName	= basename( trim( $sTargetDir ) );
			if ( 0 != strcmp( $sBaseName, $sRepoVer ) )
			{
				$sTargetDirWithDate	= $this->_GetVersionDirNameWithDate( $sTargetDir );
				$sLastVersionDirReturn	= $sTargetDirWithDate;

				//
				//	step 1:
				//	move current in service to off line
				//
				$arrCmdList[]	= sprintf
				(
					"mv -fv \"/%s\" \"/%s\"",
					libs\Lib::LTrimPath( $sTargetDir ),
					libs\Lib::LTrimPath( $sTargetDirWithDate )
				);
			}
		}

		//	...
		return implode( " && ", $arrCmdList );
	}

	protected function _GetInserviceWwwrootLink( $arrItem )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}

		//	obtain inservice dir
		$sCmdSSH	= $this->_GetCommandSSH( $arrItem['user'], $arrItem['host'] );
		$sDirWwwroot	= sprintf
		(
			"%s/%s",
			libs\Lib::RTrimPath( $arrItem['path'] ),
			libs\Config::Get( 'dir_wwwroot' )
		);

		return $this->_ReadSymbolLink( $sCmdSSH, $sDirWwwroot );
	}

	protected function _ReadSymbolLink( $sCmdSSH, $sFullPath )
	{
		if ( ! is_string( $sCmdSSH ) || 0 == strlen( $sCmdSSH ) )
		{
			return '';
		}
		if ( ! is_string( $sFullPath ) || 0 == strlen( $sFullPath ) )
		{
			return '';
		}

		//	...
		$sRet	= '';

		//
		//	try to read the link
		//
		$sCmdReadlink	= sprintf( "%s readlink \"%s\"", $sCmdSSH, $sFullPath );

		$cProcess = new Process\Process( $sCmdReadlink );
		$cProcess
			->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
			->enableOutput()
			->run();
		if ( $cProcess->isSuccessful() )
		{
			$sTarget = sprintf( "/%s", trim( $cProcess->getOutput(), "\r\n\t /\\" ) );
			if ( is_string( $sTarget ) && strlen( $sTarget ) > 0 )
			{
				$sRet = $sTarget;
			}
		}

		//	...
		return $sRet;
	}



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//




	private function _GetVersionDirNameWithDate( $sDir )
	{
		//
		//	sDir like these:
		//	/var/www/pay.xs.cn/releases/1.0.9
		//	/var/www/pay.xs.cn/releases/1.0.9/
		//	/var/www/pay.xs.cn/releases/1.0.9-((201602031115))/
		//
		if ( ! is_string( $sDir ) || 0 == strlen( $sDir ) )
		{
			return '';
		}

		$sRet		= '';

		$sSplitChars	= libs\Config::Get( 'split_char_of_version', '-_-' );
		$sDirName	= dirname( trim( $sDir ) );
		$sBaseName	= basename( trim( $sDir ) );
		if ( is_string( $sDirName ) && strlen( $sDirName ) > 0 &&
			is_string( $sBaseName ) && strlen( $sBaseName ) > 0 )
		{
			//
			//	sVersion like these:
			//	1.0.9
			//	1.0.9
			//	1.0.9-((201602031115))
			//
			$sVersion = trim( $sBaseName, "\r\n\t /\\" );
			if ( strstr( $sBaseName, $sSplitChars ) )
			{
				$arrTmp = explode( $sSplitChars, $sBaseName );
				if ( is_array( $arrTmp ) && count( $arrTmp ) > 0 )
				{
					$sVersion = $arrTmp[ 0 ];
				}
			}

			//	...
			$sRet = sprintf
			(
				"%s/%s%s%s/",
				libs\Lib::RTrimPath( $sDirName ),
				$sVersion,
				$sSplitChars,
				date( "YmdHis" )
			);
		}

		return $sRet;
	}


}