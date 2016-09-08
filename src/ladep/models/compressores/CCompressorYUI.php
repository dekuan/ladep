<?php

namespace dekuan\ladep\models\compressores;


use dekuan\ladep\libs;


class CCompressorYUI extends CCompressor
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

		//	--nomunge --preserve-semi --disable-optimizations
		return sprintf
		(
			"java -jar \"%s\" \"%s\" -o \"%s\" --charset utf-8",
			$this->GetCompressorFullFilename(),
			$sAllInOneJsFFN,
			$sOutputFullFilename
		);
	}
	public function GetCompressorFullFilename()
	{
		return $this->GetCompressorFullFilenameByName( "yuicompressor-2.4.8.jar" );
	}
}