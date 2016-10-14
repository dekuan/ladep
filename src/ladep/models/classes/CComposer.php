<?php

namespace dekuan\ladep\models\classes;

use dekuan\ladep\libs;


class CComposer
{
	public function __construct()
	{
	}
	public function __destruct()
	{
	}

	public function CleanUpComposer( $sReleaseDir, callable $pfnCbFunc = null )
	{
		return $this->_CleanUpComposer( $sReleaseDir, $pfnCbFunc );
	}

	////////////////////////////////////////////////////////////
	//	Private
	//
	private function _CleanUpComposer( $sReleaseDir, callable $pfnCbFunc = null )
	{
		$bRet = false;

		if ( is_callable( $pfnCbFunc ) )
		{
			$pfnCbFunc( "info", "try to clean up composer.json( remove packagist=false, scripts.pre-update-cmd ... )" );
		}

		try
		{
			$sComposerJsonFFN = $this->_GetComposerJsonFilename( $sReleaseDir );
			if ( is_file( $sComposerJsonFFN ) )
			{
				$bModified = false;
				$arrJson = @ json_decode( @ file_get_contents( $sComposerJsonFFN ), true );
				if ( is_array( $arrJson ) )
				{
					//
					//	remove [ 'repositories' ][ n ][ 'packagist' ]
					//
					$arrRepositories = array_key_exists( 'repositories', $arrJson ) ? $arrJson['repositories'] : null;
					if ( is_array( $arrRepositories ) && count( $arrRepositories ) > 0 )
					{
						foreach ( $arrRepositories as $nIndex => $arrItem )
						{
							if ( is_array( $arrItem ) && array_key_exists( 'packagist', $arrItem ) )
							{
								if ( is_callable( $pfnCbFunc ) )
								{
									$pfnCbFunc( "comment", "Key packagist=false was found." );
								}

								//	...
								$bModified = true;
								unset( $arrJson[ 'repositories' ][ $nIndex ] );
								break;
							}
						}
					}

					//
					//	remove [ 'scripts' ][ 'pre-update-cmd' ]
					//
					if ( array_key_exists( 'scripts', $arrJson ) &&
						is_array( $arrJson[ 'scripts' ] ) &&
						array_key_exists( 'pre-update-cmd', $arrJson[ 'scripts' ] ) )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "comment", "Key scripts.pre-update-cmd was found." );
						}

						//	...
						$bModified = true;
						unset( $arrJson[ 'scripts' ][ 'pre-update-cmd' ] );
					}
				}

				if ( $bModified )
				{
					$sJsonNew = @ json_encode( $arrJson, JSON_PRETTY_PRINT );
					if ( file_put_contents( $sComposerJsonFFN, $sJsonNew ) > 0 )
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "info", "Clean up composer.json successfully." );
						}

						//	...
						$bRet = true;
					}
					else
					{
						if ( is_callable( $pfnCbFunc ) )
						{
							$pfnCbFunc( "error", "Failed to clean up composer.json" );
						}
					}
				}
				else
				{
					if ( is_callable( $pfnCbFunc ) )
					{
						$pfnCbFunc( "info", "### No key need to be cleaned up." );
					}
					$bRet = true;
				}
			}
		}
		catch ( \Exception $e )
		{
			//	throw
			if ( is_callable( $pfnCbFunc ) )
			{
				$pfnCbFunc( "error", "Exception in " . __FUNCTION__ . ", " . $e->getMessage() );
			}
		}

		return $bRet;
	}

	private function _GetComposerJsonFilename( $sReleaseDir )
	{
		if ( ! is_dir( $sReleaseDir ) )
		{
			return '';
		}

		$sRet = sprintf( "%s/composer.json", libs\Lib::RTrimPath( $sReleaseDir ) );
		return $sRet;
	}
}