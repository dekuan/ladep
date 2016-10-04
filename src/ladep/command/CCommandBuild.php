<?php

namespace dekuan\ladep\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Herrera\Phar\Update\Manager;
use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manifest;

use dekuan\ladep\libs;
use dekuan\ladep\models;
use dekuan\ladep\models\classes;



// green text
//$output->writeln('<info>foo</info>');

// yellow text
//$output->writeln('<comment>foo</comment>');

// black text on a cyan background
//$output->writeln('<question>foo</question>');

// white text on a red background
//$output->writeln('<error>foo</error>');



class CCommandBuild extends Command
{
	protected function configure()
	{
		$this
			->setName('build')
			->setDescription('Build application via configuration file.')
			->setDefinition
			([
				new InputOption('last', null, InputOption::VALUE_NONE, 'Automatically obtain the last tag from remote repository.'),
				new InputOption('no-compress-js', null, InputOption::VALUE_NONE, 'Do not compress javascript files'),
				new InputOption('no-compress-css', null, InputOption::VALUE_NONE, 'Do not compress CSS files'),
				new Input\InputArgument( 'project_config', Input\InputArgument::REQUIRED, 'Required full path of project configuration file, e.g. /var/www/pay/pay.xs.cn.json' )
			])
			->setHelp(<<<EOT
The build command will help you build a laravel app and make it ready to release.

Steps:

1, git clone --branch <version/tag_name> <repo_url>
2, composer install
3, composer update
4, compress and merge js/css
5, chmod for directories and files

EOT
			)
		;
	}
	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		$cBuild		= new models\CBuild();
		$sErrorDesc	= '';

		//
		//	Print header
		//
		libs\MainApp::PrintHeader();

		//	...
		$this->_Init();
		$this->_PintStartInfo( $cOutput );

		//	...
		$nErrorId	= $cBuild->Run
		(
			[
				'last'			=> $cInput->getOption( 'last' ),
				'no-compress-js'	=> $cInput->getOption( 'no-compress-js' ),
				'no-compress-css'	=> $cInput->getOption( 'no-compress-css' ),
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
		$cOutput->writeln( "<info>Your dear app is now been building, please wait for a while ...</info>" );
		$cOutput->writeln( "" );

		return true;
	}
}



