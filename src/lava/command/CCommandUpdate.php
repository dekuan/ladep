<?php

namespace dekuan\lava\command;


use Herrera\Phar\Update\Manager;
use Symfony\Component\Console\Input\InputOption;
use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CCommandUpdate extends Command
{
	const MANIFEST_FILE = 'https://github.com/dekuan/lava/raw/master/manifest.json';

	protected function configure()
	{
		$this
			->setName( 'update' )
			->setDescription( 'Updates me to the latest version' )
			->addOption( 'major', null, InputOption::VALUE_NONE, 'Allow major version update' )
		;
	}
	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		$cManager	= null;

		//	...
		$cOutput->writeln( 'Looking for updates...' );

		try
		{
			$cManager = new Manager( Manifest::loadFile( self::MANIFEST_FILE ) );
		}
		catch ( FileException $e )
		{
			$cOutput->writeln( '<error>Unable to search for updates</error>' );
			return 1;
		}

		$sCurrentVersion	= $this->getApplication()->getVersion();
		$sAllowMajor		= $cInput->getOption( 'major' );
		if ( $cManager->update( $sCurrentVersion, $sAllowMajor ) )
		{
			$cOutput->writeln( '<info>Updated to latest version</info>' );
		}
		else
		{
			$cOutput->writeln( '<comment>Already up-to-date</comment>' );
		}
	}
}