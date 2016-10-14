<?php

namespace dekuan\ladep\libs;


class MainApp
{
	const COMPANY_NAME	= 'DeKuan, Inc.';
	const APP_NAME		= 'Ladep';
	const FILE_NAME		= 'ladep';
	const URL_MANIFEST	= 'https://raw.githubusercontent.com/dekuan/ladep/master/manifest.json';


	//
	//	print header
	//
	static function PrintHeader()
	{
		//echo self::APP_NAME . " by " . self::COMPANY_NAME . " Installer", PHP_EOL;
		echo "======================================================================", PHP_EOL, PHP_EOL;
		echo "        _______ ______  _______  _____" . PHP_EOL,
			" |      |_____| |     \ |______ |_____]" . PHP_EOL,
			" |_____ |     | |_____/ |______ |" . PHP_EOL,
			"                                       " . PHP_EOL,
			"                                       " . PHP_EOL,
			"                                                          " . self::COMPANY_NAME . PHP_EOL,
		PHP_EOL,
		PHP_EOL,
		PHP_EOL;
	}
}