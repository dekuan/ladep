<?php

require_once( '../vendor/autoload.php' );

use Symfony\Component\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use xscn\xsconst;
use dekuan\lava\libs;
use dekuan\lava\models\classes;



class TestForCRSync extends PHPUnit_Framework_TestCase
{
	public function testForAAA()
	{
		$sFFNConfig	= '../configs/lava-pay.xs.cn.json';
		$sDirRelease	= '../releases/1.0.0/';
		$cProject	= new classes\CProject();
		$cRSync		= new classes\CRSync();
		$sErrorDesc	= '';

		//	...
		$sErrorPath	= '';
		$nErrorId	= $cProject->Load( $sFFNConfig, $sErrorPath );
		if ( 0 == $nErrorId )
		{
			$sRepoUrl		= $cProject->GetRepoUrl();
			$sRepoVer		= $cProject->GetRepoVer();
			$arrSrvConfig		= $cProject->GetServerConfig();
			$arrSrvList		= $cProject->GetServerList();

			$cRSync->SyncLocalToRemote( $sDirRelease, $arrSrvList );
		}
		else if ( -100002 == $nErrorId )
		{
			$sErrorDesc	= libs\Lang::Get( "error_file_not_exists" );
		}
		else
		{
			$sFormat	= libs\Lang::Get( "error_load_config" );
			$sErrorDesc	= sprintf( $sFormat, $sErrorPath );
		}

		echo "\r\n";
		echo $sErrorDesc;
	}





}