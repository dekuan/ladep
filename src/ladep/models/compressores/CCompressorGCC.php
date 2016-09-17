<?php
/**
 *	Google Closure Compiler
 *
 *	Documentation:
 * 	https://developers.google.com/closure/compiler/
 *
 */

namespace dekuan\ladep\models\compressores;


use dekuan\ladep\libs;


class CCompressorGCC extends CCompressor
{
	public function GetCommandLine( $sAllInOneJsFFN, $sOutputFullFilename )
	{
		if ( ! is_string( $sAllInOneJsFFN ) || ! is_file( $sAllInOneJsFFN ) )
		{
			return '';
		}
		if ( ! is_string( $sOutputFullFilename ) )
		{
			return '';
		}

		//	...
		return sprintf
		(
			"java -jar \"%s\" --js \"%s\" --js_output_file \"%s\" --charset utf-8",
			$this->GetCompressorFullFilename(),
			$sAllInOneJsFFN,
			$sOutputFullFilename
		);
	}
	public function GetCompressorFullFilename()
	{
		return $this->GetCompressorFullFilenameByName( "google_closure_compiler.jar" );
	}
}