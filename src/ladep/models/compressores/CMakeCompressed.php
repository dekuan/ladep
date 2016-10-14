<?php

namespace dekuan\ladep\models\compressores;


use dekuan\vdata\CConst;
use dekuan\ladep\libs\Lib;
use dekuan\delib\CLib;

/**
 * Created by PhpStorm.
 * User: xing
 * Date: 4/17/16
 * Time: 12:21 AM
 */
class CMakeCompressed
{
	//
	//	allowed extensions
	//
	const ARR_ALLOWED_EXTENSION	= [ 'js', 'css' ];

	//
	//	error codes
	//
	const ERROR_NO_ORIGINAL_FILE				= CConst::ERROR_USER_START + 1;
	const ERROR_CREATE_ALL_IN_ONE_FILE			= CConst::ERROR_USER_START + 5;
	const ERROR_CREATE_TEMP_FILE				= CConst::ERROR_USER_START + 6;
	const ERROR_FILE_APPEND_CONTENT				= CConst::ERROR_USER_START + 7;
	const ERROR_INVALID_EXTENSION				= CConst::ERROR_USER_START + 10;
	const ERROR_CREATE_COMPRESSED_FILE			= CConst::ERROR_USER_START + 20;
	const ERROR_INJECT_COMPRESSED_INTO_VIEW			= CConst::ERROR_USER_START + 30;
	const ERROR_VIEW_EMPTY_CONTENT				= CConst::ERROR_USER_START + 40;
	const ERROR_VIEW_NOT_MATCHED				= CConst::ERROR_USER_START + 41;
	const ERROR_EXTRACT_ORIGINAL_FILE_LIST_FROM_VIEW	= CConst::ERROR_USER_START + 50;


	//
	//	RegEx list
	//
	//	ladep="1"
	//
	const CONST_REGX_LADEP		= "/ladep[ ]*=[ ]*[\"']{0,1}[ ]*1[ ]*[\"']{0,1}/i";

	//	<script src="{{asset('/js/xslib.js')}}" ladep="1"></script>
	const CONST_REGX_SCRIPT		= "/<script.*?" .
					"src[ ]*=[ ]*[\"']{0,1}[\{\{asset\(']*([^'\"]+)['\)\}\}]*[ ]*[\"']{0,1}.*?>[ ]*" .
					"<\/[ ]*script[ ]*>/i";

	//	<link rel="stylesheet" href="{{asset('/css/bootstrap.css')}}">
	const CONST_REGX_CSS		= "/<link.*?" .
					"href[ ]*=[ ]*[\"']{0,1}[\{\{asset\(']*([^'\"]+)['\)\}\}]*[ ]*[\"']{0,1}.*?" .
					"[\/]*>/i";

	//
	//	todo
	//	todo
	//	to match ladep and xsdep
	//
	const CONST_LABEL_PROJECTVER_SCRIPT	= "<script project=\"%s\" version=\"%s\"></script>";
	const CONST_LABEL_PROJECTVER_STYLE	= "<style project=\"%s\" version=\"%s\"></style>";

	const CONST_LABEL_COMPRESSED_SCRIPT	= "<script compressed=\"ladep\">";
	const CONST_LABEL_COMPRESSED_STYLE	= "<style compressed=\"ladep\">";


	public function __construct()
	{
	}
	public function __destruct()
	{
	}



	//
	//	@ public
	//	make compressed view
	//
	public function MakeCompressedView( $sProjectName, $sVer, $sViewFFN, $sWebRootDir, $bTrimLine, $arrOptions, Array & $arrReturn, callable $pfnCbFunc )
	{
		//
		//	sProjectName	- [in] string	the project name
		//	sVer		- [in] string	the version of project
		//	sViewFFN	- [in] string	the full filename of view page
		//	sWebRootDir	- [in] string	the full directory name of web root
		//	sExtension	- [in] string	extension
		//	bTrimLine	- [in] bool	if trim line
		//	arrOptions	- [in] array
		//				[
		//					'no-compress-js',
		//					'no-compress-css',
		//				]
		//	arrReturn	- [out] array
		//				[
		//					'js'	=> [ 'all_in_one_ffn', 'compressed_ffn' ]
		//					'css'	=> [ 'all_in_one_ffn', 'compressed_ffn' ]
		//				]
		//
		if ( ! is_string( $sProjectName ) || 0 == strlen( $sProjectName ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sVer ) || 0 == strlen( $sVer ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sViewFFN ) || ! is_file( $sViewFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sWebRootDir ) || ! is_dir( $sWebRootDir ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_bool( $bTrimLine ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_callable( $pfnCbFunc ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$nMakeCompressedJs	= CConst::ERROR_UNKNOWN;
		$nMakeCompressedCss	= CConst::ERROR_UNKNOWN;

		$arrReturn		= [ 'js' => [], 'css' => [] ];
		$arrReturnJs		= [];
		$arrReturnCss		= [];

		$sCompressedFFNJs	= '';
		$sCompressedFFNCss	= '';


		//	...
		$pfnCbFunc( 'info', "" );
		$pfnCbFunc( 'info', "Compressing\t: $sViewFFN" );

		//
		//	...
		//
		$bCompressJs	= true;
		$bCompressCss	= true;

		if ( CLib::IsArrayWithKeys( $arrOptions, 'no-compress-js' ) &&
			is_bool( $arrOptions[ 'no-compress-js' ] ) &&
			$arrOptions[ 'no-compress-js' ] )
		{
			$bCompressJs	= false;
		}
		if ( CLib::IsArrayWithKeys( $arrOptions, 'no-compress-css' ) &&
			is_bool( $arrOptions[ 'no-compress-css' ] ) &&
			$arrOptions[ 'no-compress-css' ] )
		{
			$bCompressCss	= false;
		}

		if ( $bCompressJs )
		{
			$nMakeCompressedJs = $this->_MakeCompressedFileByExtension( 'js', $sViewFFN, $sWebRootDir, $bTrimLine, $arrReturnJs, $pfnCbFunc );
		}
		if ( $bCompressCss )
		{
			$nMakeCompressedCss = $this->_MakeCompressedFileByExtension( 'css', $sViewFFN, $sWebRootDir, $bTrimLine, $arrReturnCss, $pfnCbFunc );
		}

		$arrReturn['js']	= $arrReturnJs;
		if ( CConst::ERROR_SUCCESS == $nMakeCompressedJs &&
			is_array( $arrReturnJs ) &&
			array_key_exists( 'all_in_one_ffn', $arrReturnJs ) &&
			array_key_exists( 'compressed_ffn', $arrReturnJs ) &&
			is_string( $arrReturnJs[ 'compressed_ffn' ] ) &&
			is_file( $arrReturnJs[ 'compressed_ffn' ] ) )
		{
			$sCompressedFFNJs = $arrReturnJs[ 'compressed_ffn' ];
		}

		$arrReturn['css']	= $arrReturnCss;
		if ( CConst::ERROR_SUCCESS == $nMakeCompressedCss &&
			is_array( $arrReturnCss ) &&
			array_key_exists( 'all_in_one_ffn', $arrReturnCss ) &&
			array_key_exists( 'compressed_ffn', $arrReturnCss ) &&
			is_string( $arrReturnCss[ 'compressed_ffn' ] ) &&
			is_file( $arrReturnCss[ 'compressed_ffn' ] ) )
		{
			$sCompressedFFNCss = $arrReturnCss[ 'compressed_ffn' ];
		}


		//
		//	injection
		//
		$arrCompressedFFN =
			[
				'js'	=> $sCompressedFFNJs,
				'css'	=> $sCompressedFFNCss,
			];
		$nCall	= $this->_InjectCompressedIntoView
		(
			$sProjectName,
			$sVer,
			$sViewFFN,
			$bTrimLine,
			$arrCompressedFFN,
			$pfnCbFunc
		);
		if ( CConst::ERROR_SUCCESS == $nCall )
		{
			$nRet = CConst::ERROR_SUCCESS;

			//	...
			$pfnCbFunc( 'info', "\t\t  Inject compressed file content into view successfully." );
		}
		else if ( self::ERROR_VIEW_NOT_MATCHED == $nCall )
		{
			$nRet = self::ERROR_VIEW_NOT_MATCHED;
			$pfnCbFunc( 'info', "\t\t  No JavaScript/CSS file matched in view." );
		}
		else
		{
			$nRet = self::ERROR_INJECT_COMPRESSED_INTO_VIEW;
			$pfnCbFunc( 'comment', "\t\t  Failed to inject compressed file content into view." );
		}

		return $nRet;
	}



	////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _MakeCompressedFileByExtension( $sExtension, $sViewFFN, $sWebRootDir, $bTrimLine, Array & $arrReturn, callable $pfnCbFunc )
	{
		//
		//	sExtension	- [in]	string, 'js', 'css'
		//	sViewFFN	- [in]	string, full filename of view
		//	sWebRootDir	- [in]	string, the root directory of web
		//	bTrimLine	- [in]	bool,	boolean value if we trim every line of view while creating new view
		//	arrReturn	- [out]	[ 'all_in_one_ffn' => '', 'compressed_ffn' => '' ]
		//	pfnCbFunc	- [in]	callback function
		//	RETURN		- error code
		//
		if ( ! $this->_IsValidExtension( $sExtension ) )
		{
			return self::ERROR_INVALID_EXTENSION;
		}
		if ( ! is_string( $sViewFFN ) || ! is_file( $sViewFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sWebRootDir ) || ! is_dir( $sWebRootDir ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_bool( $bTrimLine ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_callable( $pfnCbFunc ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

		$nExtract	= CConst::ERROR_UNKNOWN;
		$arrFFNList	= [];
		$sAllInOneFFN	= '';
		$sCompressedFFN	= '';
		$arrReturn	= [];

		//	...
		$nExtract	= $this->_ExtractOriginalFileListFromView( $sExtension, $sViewFFN, $sWebRootDir, $arrFFNList );
		$pfnCbFunc( 'info', "\t\t  Extracting [$sExtension] files from view.(error id = $nExtract)" );

		if ( CConst::ERROR_SUCCESS == $nExtract )
		{
			if ( is_array( $arrFFNList ) && count( $arrFFNList ) > 0 )
			{
				$pfnCbFunc( 'info', "\t\t  " . count( $arrFFNList ) . " [$sExtension] files found :" );
				foreach ( $arrFFNList as $nIndex => $sFullFilename )
				{
					$pfnCbFunc( 'info', sprintf( "\t\t  %02d - %s", $nIndex, $sFullFilename ) );
				}

				if ( CConst::ERROR_SUCCESS ==
					$this->_CreateAllInOneFile( $sExtension, $arrFFNList, $sAllInOneFFN ) )
				{
					$pfnCbFunc( 'info', "\t\t  Create [$sExtension] all-in-one file successfully." );
					$pfnCbFunc( 'info', "\t\t  $sAllInOneFFN" );

					$arrReturn[ 'all_in_one_ffn' ]	= $sAllInOneFFN;
					if ( CConst::ERROR_SUCCESS ==
						$this->_CreateCompressedFile( $sAllInOneFFN, $sCompressedFFN, $pfnCbFunc ) )
					{
						//
						//	Create compressed file successfully
						//
						$nRet = CConst::ERROR_SUCCESS;

						//	...
						$nFilesizeBefore	= filesize( $sAllInOneFFN );
						$nFilesizeAfter		= filesize( $sCompressedFFN );
						$nCompressedPercent	= ceil( ( $nFilesizeAfter * 100 ) / $nFilesizeBefore );
						$pfnCbFunc( 'info', "\t\t  compressed successfully, $nCompressedPercent PERCENT OF ORIGINAL." );
						$pfnCbFunc( 'info', "\t\t  $sCompressedFFN" );

						//	...
						$arrReturn[ 'compressed_ffn' ]	= $sCompressedFFN;
					}
					else
					{
						$nRet = self::ERROR_CREATE_COMPRESSED_FILE;
						$pfnCbFunc( 'comment', "\t\t  Failed to create [$sExtension] compressed file." );
					}
				}
				else
				{
					$nRet = self::ERROR_CREATE_ALL_IN_ONE_FILE;
					$pfnCbFunc( 'comment', "\t\t  Failed to create [$sExtension] all-in-one file." );
				}
			}
			else
			{
				//	ERROR_NO_ORIGINAL_FILE
				$nRet = CConst::ERROR_SUCCESS;
				$pfnCbFunc( 'comment', "\t\t  That's OK! No [$sExtension] file found." );
			}
		}
		else if ( self::ERROR_VIEW_NOT_MATCHED == $nExtract )
		{
			//	there is no scripts/css in view
			//	this issue is OK
			$nRet = CConst::ERROR_SUCCESS;
			$pfnCbFunc( 'comment', "\t\t  That's OK! No [$sExtension] file matched in view." );
		}
		else if ( self::ERROR_VIEW_EMPTY_CONTENT == $nExtract )
		{
			//	the view is empty
			//	we should stop deploying and tell this things to caller
			$nRet = self::ERROR_VIEW_EMPTY_CONTENT;
			$pfnCbFunc( 'comment', "\t\t  View is empty for searching [$sExtension] files." );
		}
		else
		{
			$nRet = self::ERROR_EXTRACT_ORIGINAL_FILE_LIST_FROM_VIEW;
			$pfnCbFunc( 'comment', "\t\t  Failed to extract [$sExtension] file list from view." );
		}

		//	...
		return $nRet;
	}


	private function _CreateCompressedFile( $sAllInOneFFN, & $sCompressedFFN = null, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sAllInOneFFN ) || ! is_file( $sAllInOneFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$cCompressor = new CCompressorYUI();
		if ( $cCompressor->CreateCompressedFile( $sAllInOneFFN, $sCompressedFFN, $pfnCbFunc ) )
		{
			$nRet = CConst::ERROR_SUCCESS;
		}

		return $nRet;
	}

	private function _CreateCompressedHtml( $sAllInOneFFN, & $sCompressedFFN = null, callable $pfnCbFunc = null )
	{
		if ( ! is_string( $sAllInOneFFN ) || ! is_file( $sAllInOneFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$cCompressor = new CCompressorHtml();
		if ( $cCompressor->CreateCompressedFile( $sAllInOneFFN, $sCompressedFFN, $pfnCbFunc ) )
		{
			$nRet = CConst::ERROR_SUCCESS;
		}

		return $nRet;
	}

	private function _CreateAllInOneFile( $sExtension, $arrFFNList, & $sAllInOneFFN = null )
	{
		//
		//	sExtension	= [in] string	file extension
		//	arrFFNList	- [in] array	list of javascript files
		//	(
		//		[0] => /Users/xing/wwwroot/websites/account/public/js/jquery-1.11.3.js
		//		[1] => /Users/xing/wwwroot/websites/account/public/js/bootstrap.js
		//		[2] => /Users/xing/wwwroot/websites/account/public/js/xslib.js
		//		[6] => /Users/xing/wwwroot/websites/account/public/js/purl.js
		//	)
		//	sAllInOneFFN	- [out] string	the compressed full filename
		//	RETURN		- true / false
		//
		if ( ! is_array( $arrFFNList ) ||
			0 == count( $arrFFNList ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! $this->_IsValidExtension( $sExtension ) )
		{
			return self::ERROR_INVALID_EXTENSION;
		}

		//	...
		$nRet	= CConst::ERROR_UNKNOWN;

		//	...
		$sTempFFN	= tempnam( sys_get_temp_dir(), 'ladep_' );
		$sAllInOneFFN	= sprintf( "%s.%s", $sTempFFN, $sExtension );
		$arrFileHash	= [];

		//	...
		@ unlink( $sTempFFN );
		@ unlink( $sAllInOneFFN );
		@ touch( $sAllInOneFFN );

		if ( is_file( $sAllInOneFFN ) )
		{
			//	...
			file_put_contents( $sAllInOneFFN, '' );

			foreach ( $arrFFNList as $sFullFilename )
			{
				if ( is_string( $sFullFilename ) &&
					( is_file( $sFullFilename ) || Lib::IsValidUrl( $sFullFilename ) ) )
				{
					$sCnt = @ file_get_contents( $sFullFilename );
					if ( is_string( $sCnt ) && strlen( $sCnt ) > 0 )
					{
						//	...
						$sMd5 = md5( $sCnt );

						if ( array_key_exists( $sMd5, $arrFileHash ) )
						{
							//	already exists, skip the file
							continue;
						}

						//	...
						$arrFileHash[ $sMd5 ]	= 1;

						//
						//	Removes multi-line comments and does not create
						//	a blank line, also treats white spaces/tabs
						//
						if ( 0 == strcasecmp( 'js', $sExtension ) )
						{
							$sCnt = $this->_StripJavaScriptComment( $sCnt );
						}
						else if ( 0 == strcasecmp( 'css', $sExtension ) )
						{
							$sCnt = $this->_StripCSSComment( $sCnt );
						}

						if ( false !== file_put_contents( $sAllInOneFFN, "\n\n", FILE_APPEND ) &&
							false !== file_put_contents( $sAllInOneFFN, $sCnt, FILE_APPEND ) )
						{
							$nRet = CConst::ERROR_SUCCESS;
						}
						else
						{
							@unlink( $sAllInOneFFN );
							unset( $sAllInOneFFN );
							$sAllInOneFFN = null;

							//	...
							$nRet = self::ERROR_FILE_APPEND_CONTENT;
							break;
						}
					}
				}
			}
		}
		else
		{
			$nRet = self::ERROR_CREATE_TEMP_FILE;
		}

		return $nRet;
	}

	private function _InjectCompressedIntoView( $sProjectName, $sVer, $sViewFFN, $bTrimLine, $arrCompressedFFN, callable $pfnCbFunc )
	{
		if ( ! is_string( $sProjectName ) || 0 == strlen( $sProjectName ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sVer ) || 0 == strlen( $sVer ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sViewFFN ) || ! is_file( $sViewFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_bool( $bTrimLine ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_array( $arrCompressedFFN ) ||
			! array_key_exists( 'js', $arrCompressedFFN ) ||
			! array_key_exists( 'css', $arrCompressedFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ( ! is_string( $arrCompressedFFN[ 'js' ] ) || ! is_file( $arrCompressedFFN[ 'js' ] ) ) &&
			( ! is_string( $arrCompressedFFN[ 'css' ] ) || ! is_file( $arrCompressedFFN[ 'css' ] ) ) )
		{
			//	no file matched
			return self::ERROR_VIEW_NOT_MATCHED;
		}
		if ( ! is_callable( $pfnCbFunc ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//
		//	self::CONST_REGX_SCRIPT
		//	self::CONST_REGX_CSS
		//
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$sCompressedFFNJs	= $arrCompressedFFN[ 'js' ];
		$sCompressedFFNCss	= $arrCompressedFFN[ 'css' ];

		//
		//	find scripts like these:
		//	<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" ladep="1"></script>
		//	<script    src = "{{asset('/wap/js/userinfo_loaddata.js')}}" ladep = "1"></script>
		//	<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" ladep=1></script>
		//
		$bInjectedJs	= false;
		$bInjectedCss	= false;
		$sLineNew	= '';


		//
		//	load html contents and
		//	remove HTML comments from content
		//
		$sContent	= $this->_LoadPureHTMLContents( $sViewFFN );
		if ( is_string( $sContent ) && strlen( $sContent ) > 0 )
		{
			$arrLine = explode( "\n", $sContent );
			if ( is_array( $arrLine ) && count( $arrLine ) )
			{
				foreach ( $arrLine as $nLineIndex => $sLine )
				{
					$sLine		= rtrim( $sLine, "\r\n\t " );

					//
					//	search <script ...>
					//	search <link href ...>
					//
					$bMatchedJs	= $this->_IsMatchedLine( self::CONST_REGX_SCRIPT, $sLine, $sCompressedFFNJs );
					$bMatchedCss	= $this->_IsMatchedLine( self::CONST_REGX_CSS, $sLine, $sCompressedFFNCss );

					if ( ! $this->_IsBladeScriptLine( $sLine ) )
					{
						if ( $bMatchedJs )
						{
							$pfnCbFunc( "info", sprintf( "\t\t  Injection > js.tag was matched @%d(%s)", $nLineIndex, trim( $sLine ) ) );

							//
							//	matched js <script ...
							//
							if ( ! $bInjectedJs )
							{
								$bInjectedJs = true;

								//	inject ...
								//	get file content
								$sCnt	= @ file_get_contents( $sCompressedFFNJs );

								//	...
								$sLineNew .= ( $this->_GetScriptProjectVersion( $sProjectName, $sVer ) .
										self::CONST_LABEL_COMPRESSED_SCRIPT . $sCnt . "</script>\n" );
							}
						}
						else if ( $bMatchedCss )
						{
							$pfnCbFunc( "info", sprintf( "\t\t  Injection > css.tag was matched @%d(%s)", $nLineIndex, trim( $sLine ) ) );

							//
							//	matched css <link href...
							//
							if ( ! $bInjectedCss )
							{
								$bInjectedCss = true;

								//	inject ...
								//	get file content
								$sCnt	= @ file_get_contents( $sCompressedFFNCss );

								//	...
								$sLineNew .= ( $this->_GetStyleProjectVersion( $sProjectName, $sVer ) .
										self::CONST_LABEL_COMPRESSED_STYLE . $sCnt . "</style>\n" );
							}
						}
						else
						{
							//
							//	append other lines
							//
							if ( $bTrimLine )
							{
								$sLineNew .= trim( $sLine );
							}
							else
							{
								$sLineNew .= ( $sLine . "\n" );
							}
						}
					}
					else
					{
						//
						//	do not trim the line for blade script line
						//
						$sLineNew .= ( "\n" . $sLine . "\n" );
					}
				}

				//	...
				if ( false !== file_put_contents( $sViewFFN, $sLineNew ) )
				{
					$nRet = CConst::ERROR_SUCCESS;
				}
			}
		}

		return $nRet;
	}
	private function _IsMatchedLine( $sRegx, $sLine, $sCompressedFFN )
	{
		if ( ! is_string( $sRegx ) || 0 == strlen( $sRegx ) )
		{
			return false;
		}
		if ( ! is_string( $sLine ) || 0 == strlen( $sLine ) )
		{
			return false;
		}
		if ( ! is_string( $sCompressedFFN ) || ! is_file( $sCompressedFFN ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		$arrMatches	= null;
		$nMatchCount	= preg_match( $sRegx, $sLine, $arrMatches );

		if ( false !== $nMatchCount &&
			$nMatchCount > 0 &&
			is_array( $arrMatches ) &&
			count( $arrMatches ) > 1 &&
			( ! strstr( $sLine, self::CONST_LABEL_COMPRESSED_SCRIPT ) &&
				! strstr( $sLine, self::CONST_LABEL_COMPRESSED_STYLE ) ) )
		{
			//
			//	matched "ladep=1"
			//
			if ( 1 == preg_match( self::CONST_REGX_LADEP, $sLine ) )
			{
				$bRet = true;
			}
		}

		//	...
		return $bRet;
	}



	private function _ExtractOriginalFileListFromView( $sExtension, $sViewFFN, $sWebRootDir, & $arrListReturn = null )
	{
		if ( ! $this->_IsValidExtension( $sExtension ) )
		{
			return self::ERROR_INVALID_EXTENSION;
		}
		if ( ! is_string( $sViewFFN ) || ! is_file( $sViewFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sWebRootDir ) || ! is_dir( $sWebRootDir ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		if ( 0 == strcasecmp( 'js', $sExtension ) )
		{
			$nRet = $this->_ExtractFileListFromView( $sViewFFN, $sWebRootDir, self::CONST_REGX_SCRIPT, $arrListReturn );
		}
		else if ( 0 == strcasecmp( 'css', $sExtension ) )
		{
			$nRet = $this->_ExtractFileListFromView( $sViewFFN, $sWebRootDir, self::CONST_REGX_CSS, $arrListReturn );
		}

		return $nRet;
	}
	private function _ExtractFileListFromView( $sViewFFN, $sWebRootDir, $sRegx, & $arrListReturn = null )
	{
		if ( ! is_string( $sViewFFN ) ||
			0 == strlen( $sViewFFN ) ||
			! is_file( $sViewFFN ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sWebRootDir ) ||
			0 == strlen( $sWebRootDir ) ||
			! is_dir( $sWebRootDir ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_string( $sRegx ) || 0 == strlen( $sRegx ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//
		//	find scripts like these:
		//	<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" ladep="1"></script>
		//	<script    src = "{{asset('/wap/js/userinfo_loaddata.js')}}" ladep = "1"></script>
		//	<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" ladep=1></script>
		//
		//	find css like these:
		//	<link rel="stylesheet" href="{{asset('/css/mail_test.css')}}" ladep="1" />
		//	<link rel="stylesheet" href="{{asset('/css/mail_test.css')}}" ladep=1 >
		//
		$nRet		= CConst::ERROR_UNKNOWN;
		$arrListReturn	= null;
		$sContent	= $this->_LoadPureHTMLContents( $sViewFFN );
		$arrMatches	= null;

		if ( is_string( $sContent ) && strlen( $sContent ) > 0 )
		{
			$nMatchCount	= preg_match_all( $sRegx, $sContent, $arrMatches );

			if ( false !== $nMatchCount &&
				$nMatchCount > 0 &&
				is_array( $arrMatches ) &&
				count( $arrMatches ) > 1 &&
				is_array( $arrMatches[ 0 ] ) && is_array( $arrMatches[ 1 ] ) &&
				$nMatchCount == count( $arrMatches[ 0 ] ) &&
				count( $arrMatches[ 0 ] ) == count( $arrMatches[ 1 ] ) )
			{
				$arrMd5Map	= [];
				$arrListReturn	= [];
				for ( $i = 0; $i < $nMatchCount; $i ++ )
				{
					$sMatchedLine	= trim( $arrMatches[ 0 ][ $i ] );
					$sFilename	= trim( $arrMatches[ 1 ][ $i ] );

					if ( is_string( $sMatchedLine ) && strlen( $sMatchedLine ) > 0 &&
						is_string( $sFilename ) && strlen( $sFilename ) > 0 )
					{
						//
						//	check if filename already exists
						//
						$sMd5 = md5( $sFilename );
						if ( ! array_key_exists( $sMd5, $arrMd5Map ) )
						{
							$arrMd5Map[ $sMd5 ]	= 1;

							//
							//	matched "ladep=1"
							//
							if ( 1 == preg_match( self::CONST_REGX_LADEP, $sMatchedLine ) )
							{
								//	Array
								//	(
								//		[scheme] => https
								//		[host] => fonts.googleapis.com
								//		[path] => /css
								//		[query] => family=Lato:100
								//	)
								$arrUrl	= parse_url( $sFilename );
								if ( is_array( $arrUrl ) &&
									array_key_exists( 'scheme', $arrUrl ) &&
									array_key_exists( 'host', $arrUrl ) &&
									array_key_exists( 'path', $arrUrl ) )
								{
									//
									//	it's a url
									//
									$arrListReturn[] = $sFilename;
								}
								else
								{
									//
									//	it's only a relative path
									//
									$sFilename = $this->_GetPureUri( $sFilename );
									if ( is_string( $sFilename ) && strlen( $sFilename ) > 0 )
									{
										$arrListReturn[] = sprintf
										(
											"%s/%s",
											rtrim( $sWebRootDir, "\r\n\t \\/" ),
											ltrim( $sFilename, "\r\n\t \\/" )
										);
									}
									else
									{
										//	this file was canceled from list
									}
								}
							}
						}
					}
				}

				if ( is_array( $arrListReturn ) &&
					count( $arrListReturn ) > 0 )
				{
					$nRet = CConst::ERROR_SUCCESS;
				}
				else
				{
					$nRet = self::ERROR_VIEW_NOT_MATCHED;
				}
			}
			else
			{
				$nRet = self::ERROR_VIEW_NOT_MATCHED;
			}
		}
		else
		{
			$nRet = self::ERROR_VIEW_EMPTY_CONTENT;
		}

		return $nRet;
	}


	//
	//	check if the content of line is a blade script line
	//
	private function _IsBladeScriptLine( $sLine )
	{
		//
		//	sLine	- [in] line content
		//	RETURN	- true / false
		//
		if ( ! is_string( $sLine ) || 0 == strlen( $sLine ) )
		{
			return false;
		}

		//	...
		$bRet = false;

		//	...
		$sTrimedLine	= trim( $sLine, "\r\n\t " );
		if ( is_string( $sTrimedLine ) && strlen( $sTrimedLine ) > 0 )
		{
			$sFirstChar = substr( $sTrimedLine, 0, 1 );
			if ( is_string( $sFirstChar ) &&
				1 == strlen( $sFirstChar ) &&
				'@' == $sFirstChar )
			{
				$bRet = true;
			}
		}

		return $bRet;
	}

	//
	//	get pure path from original url
	//
	private function _GetPureUri( $sUrlOrg )
	{
		//
		//	sUrlOrg	- [in] original url, e.g.:
		//			/js/pay_wap.js?ver=1.0.2
		//	RETURN	-	/js/pay_wap.js while sucessfully,
		//			or original while occurred an error.
		//
		if ( ! is_string( $sUrlOrg ) || 0 == strlen( $sUrlOrg ) )
		{
			return '';
		}

		$sRet	= $sUrlOrg;
		$arrUrl	= @ parse_url( $sUrlOrg );
		if ( is_array( $arrUrl ) &&
			array_key_exists( 'path', $arrUrl ) &&
			is_string( $arrUrl[ 'path' ] ) &&
			strlen( $arrUrl[ 'path' ] ) > 0 )
		{
			$sRet = $arrUrl[ 'path' ];
		}

		return $sRet;
	}


	//
	//	Strip comment
	//
	private function _StripJavaScriptComment( $sCnt )
	{
		if ( ! is_string( $sCnt ) || 0 == strlen( $sCnt ) )
		{
			return '';
		}

		//	...
		$sCnt	= ( "\n" . $sCnt . "\n" );

		//	...
//		$sCnt	= preg_replace( "/\/\*(.*)\*\//", "", $sCnt );
//	//	$sCnt	= preg_replace( "/\/\*([\s\S]*)\*\//g", "", $sCnt );
//		$sCnt	= preg_replace( "/\/\*([\s\S]*?)\*\//s", "", $sCnt );
//		$sCnt	= preg_replace( "/\/\/(.*)$/sm", "", $sCnt );
//		$sCnt	= preg_replace( "/(\/\*([\s\S]*?)\*\/)|(\/\/(.*)$)/sm", "", $sCnt );


		//	 Removes single line '//' comments, treats blank characters
		//$sCnt = preg_replace( '![ \t]*//.*[ \t]*[\r\n]!', '', $sCnt );
		//	$sCnt = preg_replace( "/\\/\\/[^\\n]+\\n/m", "", $sCnt );

		//	Removes multi-line comments and does not create
	//	$sCnt	= preg_replace( "/\\/\\*(.*?)\\*\\/[\\n]?/s", "", $sCnt );
	//	$sCnt	= preg_replace( "/[\r\n\t ]+\\/\\*(.*?)\\*\\/[\\n]?/s", "", $sCnt );

		//	...
		return trim( $sCnt, "\r\n\t " );
	}
	private function _StripCSSComment( $sCnt )
	{
		if ( ! is_string( $sCnt ) || 0 == strlen( $sCnt ) )
		{
			return '';
		}

		//	...
		$sCnt	= ( "\n" . $sCnt . "\n" );

		//	Removes multi-line comments and does not create
	//	$sCnt	= preg_replace( "/\\/\\*\\!(.*?)\\*\\/[\\n]?/s", "", $sCnt );
	//	$sCnt	= preg_replace( "/\\/\\*(.*?)\\*\\/[\\n]?/s", "", $sCnt );

		//	...
		return trim( $sCnt, "\r\n\t " );
	}


	//
	//	load html contents and
	//	remove HTML comments from it
	//
	private function _LoadPureHTMLContents( $sFullFilename )
	{
		if ( ! is_string( $sFullFilename ) || ! is_file( $sFullFilename ) )
		{
			return '';
		}

		//	...
		$sRet	= '';

		//	...
		$sContent = @ file_get_contents( $sFullFilename );
		if ( is_string( $sContent ) && strlen( $sContent ) > 0 )
		{
			$sRet = $sContent;
			//
			//	remove HTML comments from content
			//
			//$sRet = preg_replace( '/<!--(.|\s)*?-->/' , '' , $sContent );
		}

		return $sRet;
	}
	private function _IsValidExtension( $sStr )
	{
		$bRet = false;

		if ( is_string( $sStr ) || is_numeric( $sStr ) )
		{
			if ( strlen( $sStr ) > 0 )
			{
				if ( in_array( $sStr, self::ARR_ALLOWED_EXTENSION ) )
				{
					$bRet = true;
				}
			}
		}

		return $bRet;
	}

	private function _GetScriptProjectVersion( $sProjectName, $sVer )
	{
		return sprintf
		(
			self::CONST_LABEL_PROJECTVER_SCRIPT,
			( is_string( $sProjectName ) ? trim( $sProjectName ) : '' ),
			( is_string( $sVer ) ? trim( $sVer ) : '' )
		);
	}
	private function _GetStyleProjectVersion( $sProjectName, $sVer )
	{
		return sprintf
		(
			self::CONST_LABEL_PROJECTVER_STYLE,
			( is_string( $sProjectName ) ? trim( $sProjectName ) : '' ),
			( is_string( $sVer ) ? trim( $sVer ) : '' )
		);
	}

}