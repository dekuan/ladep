<?php

namespace dekuan\lava;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CXSDep extends Command
{
	protected function configure()
	{
		$this->setName( 'hello' )
			->setDescription( 'Say hello' );
	}

	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		// Set the deployment timezone
	//	if (!date_default_timezone_set(env('timezone'))) {
	//		date_default_timezone_set('UTC');
	//	}

		$cOutput->writeln( 'Hello World' );
	}
}


