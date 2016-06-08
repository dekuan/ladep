<?php

namespace dekuan\lava\command;

use Herrera\Phar\Update\Manager;
use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use dekuan\lava\libs;
use dekuan\lava\models;
use dekuan\lava\models\classes;



class CCommandPulllog extends Command
{
	protected function configure()
	{
		$this
			->setName('pulllog')
			->setDescription('Pull laravel logs of all projects to local.')
			->setDefinition
			([
				new Input\InputArgument( 'project_config', Input\InputArgument::OPTIONAL, 'Required full path of project configuration file, e.g. /var/www/pay/pay.xs.cn.json' ),
			])
			->setHelp(<<<EOT
Pull laravel logs of all projects to local.

Steps:

1, By default, we will pull logs of all projects to local.
2, we will pull log of specified project.

EOT
			)
		;
	}
	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		$cRollback	= new models\CPulllog();

		//	...
		$this->_Init();
		$this->_PintHeader( $cOutput );

		$nErrorId	= $cRollback->Run
		(
			[
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
				else if ( 0 == strcasecmp( 'array', $sType ) )
				{
					if ( is_array( $cOutput ) )
					{
						print_r( $cOutput );
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
	private function _PintHeader( OutputInterface $cOutput )
	{
		if ( ! $cOutput instanceof OutputInterface )
		{
			return false;
		}

		//	...
		$cOutput->writeln( "<info>----------------------------------------------------------------------</info>" );
		$cOutput->writeln( "<info>Pulling log files, please wait for a while ...</info>" );
		$cOutput->writeln( "" );

		return true;
	}
}