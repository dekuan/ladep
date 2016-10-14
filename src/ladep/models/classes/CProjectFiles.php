<?php

namespace dekuan\ladep\models\classes;

use xscn\xsconst;
use dekuan\ladep\libs\Lib;


class CProjectFiles
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	//
	//	scan files with specified extension from a directory recursively
	//
	public function ScanAll( $sDir, $arrExtName = [ 'ladep' ] )
	{
		return $this->_ScanAllProjectFiles( $sDir, $arrExtName );
	}

	////////////////////////////////////////////////////////////
	//	Private
	//

	//
	//	scan files with specified extension from a directory recursively
	//
	private function _ScanAllProjectFiles( $sDir, $arrExtName = [ 'ladep' ] )
	{
		//
		//	sDir		- [in] string,	path of directory
		//	arrExtName	- [in] array,	extension name of project files
		//	RETURN		- Array() / null
		//
		if ( ! is_string( $sDir ) || ! is_dir( $sDir ) )
		{
			return null;
		}

		if ( is_string( $arrExtName ) )
		{
			if ( strlen( $arrExtName ) > 0 )
			{
				//	convent it to array
				$arrExtName = [ $arrExtName ];
			}
			else
			{
				return null;
			}
		}
		else if ( is_array( $arrExtName ) )
		{
			if ( 0 == count( $arrExtName ) )
			{
				return null;
			}
		}
		else
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
						//
						//	continue to scan the sub directory
						//
						$arrSubFiles = $this->_ScanAllProjectFiles( $sFFN, $arrExtName );
						if ( is_array( $arrSubFiles ) && count( $arrSubFiles ) )
						{
							$arrRet = array_merge( $arrRet, $arrSubFiles );
						}
					}
					else
					{
						//
						//	picked up the files with corrected extension
						//
						$sExt = $this->_GetExtensionName( $sFile );
						if ( is_string( $sExt ) &&
							strlen( $sExt ) > 0 &&
							in_array( $sExt, $arrExtName ) )
						{
							$arrRet[] = $sFFN;
						}
					}
				}

				closedir( $oDir );
				$oDir = null;
			}
		}
		catch ( \Exception $e )
		{
			throw $e;
		}

		//	...
		return $arrRet;
	}

	private function _GetExtensionName( $sFilename )
	{
		if ( ! is_string( $sFilename ) || 0 == strlen( $sFilename ) )
		{
			return '';
		}

		//	...
		$sRet = '';

		$arrPath = @ pathinfo( $sFilename );
		if ( is_array( $arrPath ) &&
			array_key_exists( 'extension', $arrPath ) &&
			is_string( $arrPath[ 'extension' ] ) &&
			strlen( $arrPath[ 'extension' ] ) > 0 )
		{
			$sRet = trim( $arrPath[ 'extension' ] );
		}

		return $sRet;
	}

}