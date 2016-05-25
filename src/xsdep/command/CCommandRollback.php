<?php

namespace xscn\xsdep\command;

use Herrera\Phar\Update\Manager;
use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use xscn\xsdep\libs;
use xscn\xsdep\models;
use xscn\xsdep\models\classes;



class CCommandRollback extends Command
{
	protected function configure()
	{
		$this
			->setName('rollback')
			->setDescription('Rollback application to specified version.')
			->setDefinition
			([
				new Input\InputArgument( 'project_config', Input\InputArgument::REQUIRED, 'Required full path of project configuration file, e.g. /var/www/pay/pay.xs.cn.json' ),
				new Input\InputArgument( 'to_version', Input\InputArgument::REQUIRED, 'The version you want to roll back to.' )
			])
			->setHelp(<<<EOT
Upload application to server using rsync command

Steps:

1, Check if the version exists on server
2, Create new symlink wwwroot to specified version

EOT
			)
		;
	}
	protected function execute( InputInterface $cInput, OutputInterface $cOutput )
	{
		$cRollback	= new models\CRollback();

		//	...
		$this->_Init();
		$this->_PintHeader( $cOutput );

		$nErrorId	= $cRollback->Run
		(
			[
				'project_config'	=> $cInput->getArgument( 'project_config' ),
				'to_version'		=> $cInput->getArgument( 'to_version' ),
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
					if ( is_array( $cOutput ) )
					{
						print_r( $cOutput );
					}
					else if ( is_array( $vBuffer ) )
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
	private function _PintHeader( OutputInterface $cOutput )
	{
		if ( ! $cOutput instanceof OutputInterface )
		{
			return false;
		}

		//	...
		$cOutput->writeln( "<info>----------------------------------------------------------------------</info>" );
		$cOutput->writeln( "<info>Your dear app is now been uploading, please wait for a while ...</info>" );
		$cOutput->writeln( "" );

		return true;
	}
}