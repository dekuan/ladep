<?php

namespace xscn\xsdep\models\classes;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process;

use xscn\xsdep\libs;



class CSSHCmdPullLogs extends CSSHCmd
{
	public function PullLogs( $arrSrvList, $sTargetDir, callable $pfnCbFunc = null )
	{
		return $this->_PullLogs( $arrSrvList, $sTargetDir, $pfnCbFunc );
	}



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _PullLogs( $arrSrvList, $sTargetDir, callable $pfnCbFunc )
	{
		//
		//	arrSrvList	- [in] array	server list
		//			[
		//				[
		//					"host"	=> "101.200.161.46",
		//					"user"	=> "worker",
		//					"pwd"	=> "",
		//					"path"	=> "/var/www/account.xs.cn/"
		//				],
		//				...
		//	sTargetDir	- [in] string	target dir
		//	pfnCbFunc	- [in] callable
		//	RETURN		- true / false
		//

		if ( ! is_array( $arrSrvList ) || 0 == count( $arrSrvList ) || ! is_callable( $pfnCbFunc ) )
		{
			$pfnCbFunc( 'error', "Invalid server list." );
			return false;
		}

		//	...
		$bRet = false;

		//	...
		$arrCmdList	= [];
		foreach ( $arrSrvList as $arrItem )
		{
			if ( $this->_IsValidItem( $arrItem ) )
			{
				$arrCmdList[]	= $this->_GetCommandPullLogRSync( $arrItem, $sTargetDir );
			}
		}

		if ( is_array( $arrCmdList ) && count( $arrCmdList ) > 0 )
		{
			foreach ( $arrCmdList as $sCmdLine )
			{
				$pfnCbFunc( 'info', "execute command: " . $sCmdLine );

				$cProcess = new Process\Process( $sCmdLine );
				$cProcess
					->setTimeout( libs\Config::Get( 'cmd_timeout' ) )
					->enableOutput()
					->run()
				;

				if ( $cProcess->isSuccessful() )
				{
					$bRet = true;
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'info', "successfully!" );
					}
				}
				else
				{

					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( 'error', $cProcess->getErrorOutput() );
					}

					//	throw new \RuntimeException( $cProcess->getErrorOutput() );
				}
			}
		}

		return $bRet;
	}

	private function _GetCommandPullLogRSync( $arrItem, $sTargetDir )
	{
		if ( ! $this->_IsValidItem( $arrItem ) )
		{
			return '';
		}
		if ( ! is_string( $sTargetDir ) || 0 == strlen( $sTargetDir ) )
		{
			return '';
		}

		return sprintf
		(
			"rsync --verbose -avz %s@%s:\"%s/%s\" \"%s/laravel_%s.log\"",
			trim( $arrItem['user'] ),
			trim( $arrItem['host'] ),
			libs\Lib::RTrimPath( $arrItem['path'] ),
			"wwwroot/storage/logs/laravel.log",
			libs\Lib::RTrimPath( $sTargetDir ),
			trim( array_key_exists( 'dm', $arrItem ) ? $arrItem['dm'] : $arrItem['host'] )
		);
	}
}