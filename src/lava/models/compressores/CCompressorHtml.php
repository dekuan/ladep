<?php

namespace dekuan\lava\models\compressores;


use dekuan\lava\libs;


class CCompressorHtml extends CCompressor
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

		//	java -jar htmlcompressor-1.5.3.jar -t html -c UTF-8 -o "bbb.php" "./blank.blade.php"
		return sprintf
		(
			"java -jar \"%s\" -t html -c UTF-8 -o \"%s\" \"%s\"",
			$this->GetCompressorFullFilename(),
			$sOutputFullFilename,
			$sAllInOneJsFFN
		);
	}
	public function GetCompressorFullFilename()
	{
		return $this->GetCompressorFullFilenameByName( "htmlcompressor-1.5.3.jar" );
	}
}