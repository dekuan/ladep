<?php

namespace dekuan\ladep\models\classes;

use dekuan\vdata\CConst;
use dekuan\ladep\libs\Lib;


/**
 *	class of configuration
 */
class CProject
{
	private $m_arrData;

	public function __construct()
	{
		$this->m_arrData	= null;
	}
	public function __destruct()
	{
	}

	public function Load( $sFullFilename, & $sErrorPath = null )
	{
		return $this->_Load( $sFullFilename, $sErrorPath );
	}

	public function GetConfig()
	{
		return $this->m_arrData;
	}

	public function GetName()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'name' ] : '';
	}
	public function GetRepoUrl()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'repo' ][ 'url' ] : '';
	}
	public function GetRepoVer()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'repo' ][ 'ver' ] : '';
	}
	public function GetServerConfig()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'server' ][ 'config' ] : null;
	}
	public function GetServerList()
	{
		return is_array( $this->m_arrData ) ? $this->m_arrData[ 'server' ][ 'list' ] : null;
	}
	public function GetServerListWithKey()
	{
		$arrRet	= null;

		//	...
		$arrServerList	= $this->GetServerList();
		if ( is_array( $arrServerList ) && count( $arrServerList ) )
		{
			$arrRet	= [];
			foreach ( $arrServerList as $arrNode )
			{
				if ( array_key_exists( 'host', $arrNode ) &&
					is_string( $arrNode[ 'host' ] ) )
				{
					$sHost	= trim( $arrNode[ 'host' ] );
					if ( strlen( $sHost ) > 0 )
					{
						$arrRet[ $sHost ] = $arrNode;
					}
				}
			}
		}

		return $arrRet;
	}
	public function GetServerHostList()
	{
		$arrRet		= null;
		$arrSrvList	= $this->GetServerList();

		if ( is_array( $arrSrvList ) && count( $arrSrvList ) > 0 )
		{
			foreach ( $arrSrvList as $arrNode )
			{
				if ( array_key_exists( 'host', $arrNode ) &&
					is_string( $arrNode[ 'host' ] ) )
				{
					$sHost	= trim( $arrNode[ 'host' ] );
					if ( strlen( $sHost ) > 0 )
					{
						$arrRet[] = $sHost;
					}
				}
			}
		}

		return $arrRet;
	}
	public function GetServerHostListString( $sGlue = ', ' )
	{
		//	...
		$sRet			= '';
		$arrSrvListArray	= $this->GetServerHostList();

		if ( is_array( $arrSrvListArray ) && count( $arrSrvListArray ) > 0 )
		{
			$sRet = implode( $sGlue, $arrSrvListArray );
		}

		return $sRet;
	}


	public function IsValidRepo( $arrRepo, & $sErrorSubPath = null )
	{
		return $this->_IsValidRepo( $arrRepo, $sErrorSubPath );
	}
	public function IsValidServer( $arrServer, $sErrorSubPath )
	{
		return $this->_IsValidServer( $arrServer, $sErrorSubPath );
	}
	public function IsValidServerList( $arrSrvConfig, & $sErrorSubPath = null )
	{
		return $this->_IsValidServerList( $arrSrvConfig, $sErrorSubPath );
	}
	public function IsValidServerItem( $arrItem, & $sErrorSubPath = null )
	{
		return $this->_IsValidServerItem( $arrItem, 0, $sErrorSubPath );
	}


	////////////////////////////////////////////////////////////
	//	Private
	//

	private function _Load( $sFullFilename, & $sErrorPath = null )
	{
		//
		//	sFullFilename	- [in] full filename of configuration file
		//	arrData		- [out] data loaded from configuration file
		//	sErrorPath	- [out/opt] the path while a error occurred
		//	RETURN		- error id based dekuan\vdata\CConst
		//
		if ( ! is_string( $sFullFilename ) || 0 == strlen( $sFullFilename ) || ! is_file( $sFullFilename ) )
		{
			return -100002;	//	dekuan\vdata\CConst::ERROR_PARAMETER;
		}

		/*
		{
			"name"	: "pay",
			"repo"	:
			{
				"url"	: "git@gitlab.corp.xs.cn:xscn/pay.git",
				"ver"	: "1.0.0"
			},
			"server" :
			{
				"config" :
				{
					"url"	: "/Users/xing/wwwroot/configuration/",
					"type"	: "file"
				},
				"list" :
				[
					{
						"host"	: "123.56.168.190",
						"user"	: "lqx",
						"pwd"	: "lqx",
						"path"	: "/home/lqx/www/"
					}
				]
			}
		}
		 */
		$nRet = -1;	//	dekuan\vdata\CConst::ERROR_UNKNOWN;

		//	...
		$arrData = @ json_decode( @ file_get_contents( $sFullFilename ), true );
		if ( is_array( $arrData ) && count( $arrData ) > 0 )
		{
			$sName		= array_key_exists( 'name', $arrData ) ? $arrData['name'] : null;
			$arrRepo	= array_key_exists( 'repo', $arrData ) ? $arrData['repo'] : null;
			$arrServer	= array_key_exists( 'server', $arrData ) ? $arrData['server'] : null;

			if ( is_string( $sName ) && strlen( $sName ) > 0 )
			{
				$sErrorSubPath	= '';
				if ( $this->_IsValidRepo( $arrRepo, $sErrorSubPath ) )
				{
					$sErrorSubPath	= '';
					if ( $this->_IsValidServer( $arrServer, $sErrorSubPath ) )
					{
						$nRet = 0;	//	dekuan\vdata\CConst::ERROR_SUCCESS;

						//	...
						$this->m_arrData = $arrData;
					}
					else
					{
						$sErrorPath = sprintf( "['server']%s", $sErrorSubPath );
					}
				}
				else
				{
					$sErrorPath = sprintf( "['repo']%s", $sErrorSubPath );
				}
			}
			else
			{
				$sErrorPath = "['name']";
			}
		}
		else
		{
			$sErrorPath = "";
			$nRet = -100301;	//	dekuan\vdata\CConst::ERROR_JSON;
		}

		return $nRet;
	}

	private function _IsValidRepo( $arrRepo, & $sErrorSubPath = null )
	{
		if ( ! is_array( $arrRepo ) || 0 == count( $arrRepo ) )
		{
			$sErrorSubPath = "";
			return false;
		}

		//	...
		$bRet = false;

		$sUrl	= array_key_exists( 'url', $arrRepo ) ? $arrRepo['url'] : null;
		$sVer	= array_key_exists( 'ver', $arrRepo ) ? $arrRepo['ver'] : null;

		if ( is_string( $sUrl ) && strlen( $sUrl ) > 0 )
		{
			//	...
			if ( is_string( $sVer ) && strlen( $sVer ) > 0 )
			{
				$bRet = true;
			}
			else
			{
				$sErrorSubPath = "['ver']";
			}
		}
		else
		{
			$sErrorSubPath = "['url']";
		}

		return $bRet;
	}

	private function _IsValidServer( $arrServer, & $sErrorSubPath = null )
	{
		if ( ! is_array( $arrServer ) || 0 == count( $arrServer ) )
		{
			$sErrorSubPath = "";
			return false;
		}

		//	...
		$bRet = false;

		//	...
		$arrConfig	= array_key_exists( 'config', $arrServer ) ? $arrServer['config'] : null;
		$arrList	= array_key_exists( 'list', $arrServer ) ? $arrServer['list'] : null;

		if ( is_array( $arrConfig ) &&
			array_key_exists( 'url', $arrConfig ) &&
			array_key_exists( 'type', $arrConfig ) )
		{
			$sConfigUrl	= array_key_exists( 'url', $arrConfig ) ? $arrConfig['url'] : null;
			$sConfigType	= array_key_exists( 'type', $arrConfig ) ? $arrConfig['type'] : null;

			if ( is_string( $sConfigUrl ) && strlen( $sConfigUrl ) > 0 &&
				is_string( $sConfigType ) && strlen( $sConfigType ) > 0 )
			{
				//	...
				$sErrorSubPath		= '';
				$sErrorSubSubPath	= '';
				if ( $this->_IsValidServerList( $arrList, $sErrorSubSubPath ) )
				{
					$bRet = true;
				}
				else
				{
					$sErrorSubPath = sprintf( "['list']%s", $sErrorSubSubPath );
				}
			}
			else
			{
				$sErrorSubPath = "['config']";
			}
		}
		else
		{
			$sErrorSubPath = "[config]";
		}

		return $bRet;
	}
	private function _IsValidServerList( $arrSrvConfig, & $sErrorSubPath = null )
	{
		if ( ! is_array( $arrSrvConfig ) || 0 == count( $arrSrvConfig ) )
		{
			$sErrorSubPath = "";
			return false;
		}

		//	...
		$bRet = true;

		foreach ( $arrSrvConfig as $nIndex => $arrHost )
		{
			$sErrorSubPath	= '';
			$bRet &= $this->_IsValidServerItem( $arrHost, $nIndex, $sErrorSubPath );
			if ( ! $bRet )
			{
				break;
			}
		}

		return $bRet;
	}
	private function _IsValidServerItem( $arrItem, $nIndex = 0, & $sErrorSubPath = null )
	{
		//
		//	arrItem		- [in] array	server item
		//			[
		//				"host"	=> "101.200.161.46",
		//				"user"	=> "worker",
		//				"pwd"	=> "",
		//				"path"	=> "/var/www/account.xs.cn/"
		//			]
		//	nIndex		- [in] int	index of server list
		//	sErrorSubPath	- [out] string	error info
		//	RETURN		- true / false
		//
		if ( ! is_numeric( $nIndex ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		if ( is_array( $arrItem ) && count( $arrItem ) > 0 )
		{
			if ( array_key_exists( 'host', $arrItem ) &&
				is_string( $arrItem['host'] ) && strlen( $arrItem['host'] ) > 0 )
			{
				if ( array_key_exists( 'user', $arrItem ) &&
					is_string( $arrItem['user'] ) && strlen( $arrItem['user'] ) > 0 )
				{
					//if ( array_key_exists( 'pwd', $arrItem ) && is_string( $arrItem['pwd'] ) )
					//{
						if ( array_key_exists( 'path', $arrItem ) &&
							is_string( $arrItem['path'] ) && strlen( $arrItem['path'] ) > 0 )
						{
							$bRet = true;
						}
						else
						{
							$sErrorSubPath = sprintf( "[%d]['path']", $nIndex );
						}
					//}
					//else
					//{
					//	$sErrorSubPath = sprintf( "[%d]['pwd']", $nIndex );
					//}
				}
				else
				{
					$sErrorSubPath = sprintf( "[%d]['user']", $nIndex );
				}
			}
			else
			{
				$sErrorSubPath = sprintf( "[%d]['host']", $nIndex );
			}
		}
		else
		{
			$sErrorSubPath = sprintf( "[%d]", $nIndex );
		}

		return $bRet;
	}

}