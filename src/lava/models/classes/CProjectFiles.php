<?php

namespace dekuan\lava\models\classes;

use xscn\xsconst;
use dekuan\lava\libs\Lib;


class CProjectFiles
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function ScanAll( $sDir, $sExtName = '.lava' )
	{
		return $this->_ScanAllProjectFiles( $sDir, $sExtName );
	}

	////////////////////////////////////////////////////////////
	//	Private
	//
	private function _ScanAllProjectFiles( $sDir, $sExtName = '.lava' )
	{
		//
		//	sDir		- path of directory
		//	sExtName	- extension name of project file
		//	RETURN		- Array() / null
		//
		if ( ! is_string( $sDir ) || ! is_dir( $sDir ) )
		{
			return null;
		}
		if ( ! is_string( $sExtName ) || 0 == strlen( $sExtName ) )
		{
			return null;
		}

		//	...
		$arrRet = [];

		try
		{
			$oDir = opendir( $sDir );
			if ( false !== $oDir )
			{
				while( false !== ( $sFile = readdir( $oDir ) ) )
				{
					if ( '.' == $sFile || '..' == $sFile )
					{
						continue;
					}

					$sFFN = sprintf( "%s/%s", Lib::RTrimPath( $sDir ), $sFile );
					if ( is_dir( $sFFN ) )
					{
						$arrSubFiles = $this->_ScanAllProjectFiles( $sFFN, $sExtName );
						if ( is_array( $arrSubFiles ) && count( $arrSubFiles ) )
						{
							$arrRet = array_merge( $arrRet, $arrSubFiles );
						}
					}
					else
					{
						$nFileLen	= strlen( $sFile );
						$nExtLen	= strlen( $sExtName );
						$nRPos		= strrpos( $sFile, $sExtName );
						if ( $nFileLen == $nRPos + $nExtLen )
						{
							$arrRet[] = $sFFN;
						}
					}
				}

				closedir( $oDir );
				$oDir = null;
			}
		}
		catch ( Exception $e )
		{
			//	throw
		}

		//	...
		return $arrRet;
	}
}