<?php

namespace dekuan\ladep\command;

use Herrera\Phar\Update\Manager;
use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use dekuan\ladep\libs;
use dekuan\ladep\models;
use dekuan\ladep\models\classes;



class CCommandCheck extends Command
{
	protected function configure()
	{
		$this
			->setName('check')
			->setDescription('Check a website is available')
			->setDefinition
			([
				new InputOption('last', null, InputOption::VALUE_NONE, 'Automatically obtain the last tag from remote repository.'),
				new Input\InputArgument( 'project_config', Input\InputArgument::REQUIRED, 'Required full path of project configuration file, e.g. /var/www/pay/pay.xs.cn.json' )
			])
			->setHelp(<<<EOT
Check a website is available

EOT
			)
		;

	}
	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		$cCheck		= new models\CCheck();
		$sErrorDesc	= '';

		//
		//	Print header
		//
		libs\MainApp::PrintHeader();


		//	...
		$this->_Init();
		$this->_PintStartInfo( $cOutput );


		$nErrorId	= $cCheck->Run
		(
			[
				'last'			=> $cInput->getOption( 'last' ),
				'project_config'	=> $cInput->getArgument( 'project_config' ),
			],
			function( $sType, $vBuffer ) use ( $cOutput )
			{
				if ( 0 == strcasecmp( 'info', $sType ) ||
					0 == strcasecmp( 'comment', $sType ) ||
					0 == strcasecmp( 'error', $sType ) )
				{
					if ( 0 == strcasecmp( 'error', $sType ) )
					{
						$sType	= 'comment';
					}
					$cOutput->writeln( "<$sType>$vBuffer</$sType>" );
				}
				else if ( 0 == strcasecmp( 'sinfo', $sType ) ||
					0 == strcasecmp( 'scomment', $sType ) ||
					0 == strcasecmp( 'serror', $sType ) )
				{
					if ( 0 == strcasecmp( 'serror', $sType ) )
					{
						$sType	= 'scomment';
					}

					//	...
					$sType	= substr( $sType, 1 );
					$cOutput->write( "<$sType>$vBuffer</$sType>" );
				}
				else if ( 0 == strcasecmp( 'array', $sType ) )
				{
					if ( is_array( $vBuffer ) )
					{
						print_r( $vBuffer );
					}
					else
					{
						var_dump( $vBuffer );
					}
				}
			}
		);
		if ( 0 == $nErrorId )
		{
			$cOutput->writeln( "<info>" . __CLASS__ . "::" . __FUNCTION__ . " executed successfully!</info>" );
		}
		else
		{
			$cOutput->writeln( "<error>" . __CLASS__ . "::" . __FUNCTION__ . " executed unsuccessfully! errorid=" . $nErrorId . "</error>" );
		}

		//	...
		$cOutput->writeln("");
		$cOutput->writeln("");
		$cOutput->writeln("");
		$cOutput->writeln("");
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _Init()
	{
		$this->m_sDirCurrent	= getcwd();
	}
	private function _PintStartInfo( OutputInterface $cOutput )
	{
		if ( ! $cOutput instanceof OutputInterface )
		{
			return false;
		}

		//	...
		//$cOutput->writeln( "<info>----------------------------------------------------------------------</info>" );
		$cOutput->writeln( "<info>Try to list all released versions on server(s), please wait for a while ...</info>" );
		$cOutput->writeln( "" );

		return true;
	}
}