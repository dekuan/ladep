<?php

namespace

{
	@ ini_set( 'date.timezone', 'Etc/GMT+0' );
	if ( PHP_VERSION > '5.1' )
	{
		@ date_default_timezone_set( 'Etc/GMT+0' );
	}

	//	...
	use Dekuan\Ladep\Installer;

	//	...
	set_error_handler
	(
		function( $nCode, $sMessage, $sFile, $nLine )
		{
			if ( $nCode & error_reporting() )
			{
				printf( " x Error: %s in file %s line %d", $sMessage, $sFile, $nLine );
				echo PHP_EOL . PHP_EOL;
				exit( 1 );
			}
		}
	);

	$cInstaller	= new Installer();
	$cInstaller->Install();

}

namespace Dekuan\Ladep
{
	use Dekuan\Version;
	use Dekuan\Version\Comparator;
	use Dekuan\Version\Dumper;
	use Dekuan\Version\Parser;

	/**
	 *	class of Installer
	 */
	class Installer
	{
		const COMPANY_NAME	= 'DeKuan, Inc.';
		const APP_NAME		= 'Ladep';
		const FILE_NAME		= 'ladep';
		const URL_MANIFEST	= 'https://raw.githubusercontent.com/dekuan/ladep/master/manifest.json';
		const DIR_CONFIG	= '/etc/ladep/';
		const DIR_BIN		= '/usr/local/bin/';
		const FILE_BIN_F_NAME	= self::DIR_BIN . self::FILE_NAME;

		private $m_sCurrentUserName;
		private $m_sCurrentGroupName;


		public function __construct()
		{
			$this->_Init();
		}

		//
		//	install
		//
		public function Install()
		{
			$this->_PrintHeader();

			//
			//	...
			//
			echo "Environment Check", PHP_EOL;
			echo "--------------------------------------------------", PHP_EOL, PHP_EOL;

			echo " - indicates success.", PHP_EOL;
			echo " x indicates error.", PHP_EOL, PHP_EOL;
			echo PHP_EOL;

			//
			//	check version
			//
			$this->_Check
			(
				'You are running this script as root.',
				'You must run this script as root or with sudo.',
				function()
				{
					return ( 0 == posix_getuid() );
				}
			);
			$this->_Check
			(
				'You have a supported version of PHP (>= 5.3.3).',
				'You need PHP 5.3.3 or greater.',
				function()
				{
					return version_compare( PHP_VERSION, '5.3.3', '>=' );
				}
			);



			echo " - Everything seems good!" . PHP_EOL . PHP_EOL;
			echo PHP_EOL . PHP_EOL;

			echo "Download" . PHP_EOL;
			echo "--------------------------------------------------" . PHP_EOL . PHP_EOL;

			//	Retrieve manifest
			echo " - Reading manifest ..." . PHP_EOL;
			$arrDlObject	= $this->_GetDownloadObjectByManifest();
			if ( null == $arrDlObject )
			{
				echo " x No application download was found." . PHP_EOL;
				exit();
			}

			//
			//	download file
			//
			echo " - Downloading " . self::APP_NAME . " v", Dumper::toString( $arrDlObject['version'] ), " ..." . PHP_EOL;
			if ( ! $this->_DownloadFile( $arrDlObject ) )
			{
				echo " x Failed to download file." . PHP_EOL;
				exit();
			}

			echo " - Checking file checksum ..." . PHP_EOL;
			if ( ! $this->_VerifyFileBySha( $arrDlObject ) )
			{
				unlink( $arrDlObject[ 'name' ] );
				echo " x The download was corrupted." . PHP_EOL;
				exit();
			}


			//
			//	check file
			//
			echo " - Checking if valid Phar ..." . PHP_EOL;
			if ( ! $this->_VerifyFileByPhar( $arrDlObject ) )
			{
				unlink( $arrDlObject[ 'name' ] );
				echo " x The Phar is not valid.\n\n";
				exit();
			}

			//
			//	fetch configuration files to /etc/ladep/
			//
			if ( ! $this->_FetchConfigurationFiles() )
			{
				unlink( $arrDlObject[ 'name' ] );
				echo " x Failed to fetch configuration files.\n\n";
				exit();
			}


			//
			//	make executable
			//
			echo " - Installing " . self::APP_NAME . " to bin directory ..." . PHP_EOL;
			if ( ! $this->_InstallToBin( $arrDlObject ) )
			{
				@ unlink( $arrDlObject[ 'name' ] );
				echo " x Failed to install.\n\n";
				exit();
			}


			//
			//	done
			//
			echo " - " . self::APP_NAME . " was installed successfully!" . PHP_EOL;
			echo " - " . "bin file placed in " . self::FILE_BIN_F_NAME . PHP_EOL;
			echo " - " . "configuration files placed in " . self::DIR_CONFIG . PHP_EOL;
		}



		//
		//	init
		//
		private function _Init()
		{
			$arrUserInfo	= @ posix_getpwuid( @ posix_getuid() );
			$arrGroupInfo	= @ posix_getgrgid( @ posix_getgid() );

			if ( is_array( $arrUserInfo ) && array_key_exists( 'name', $arrUserInfo ) )
			{
				$this->m_sCurrentUserName	= $arrUserInfo[ 'name' ];
			}
			if ( is_array( $arrGroupInfo ) && array_key_exists( 'name', $arrGroupInfo ) )
			{
				$this->m_sCurrentGroupName	= $arrGroupInfo[ 'name' ];
			}
		}


		//
		//	print header
		//
		private function _PrintHeader()
		{
			//echo self::APP_NAME . " by " . self::COMPANY_NAME . " Installer", PHP_EOL;
			echo "==================================================", PHP_EOL, PHP_EOL;
			echo "        _______ ______  _______  _____" . PHP_EOL,
			" |      |_____| |     \ |______ |_____]" . PHP_EOL,
			" |_____ |     | |_____/ |______ |" . PHP_EOL,
			"                                       " . PHP_EOL,
			"                                      " . self::COMPANY_NAME . PHP_EOL,
			PHP_EOL,
			PHP_EOL,
			PHP_EOL,
			PHP_EOL;
		}

		//
		//	read download url by manifest
		//
		private function _GetDownloadObjectByManifest()
		{
			$arrRet		= null;
			$arrMaxItem	= null;

			//	...
			$sManifest	= @ file_get_contents( self::URL_MANIFEST );
			if ( is_string( $sManifest ) && strlen( $sManifest ) > 0 )
			{
				$arrJson = @ json_decode( $sManifest, true );
				if ( is_array( $arrJson ) && count( $arrJson ) > 0 )
				{
					foreach ( $arrJson as $arrItem )
					{
						if ( $this->_IsValidManifestItem( $arrItem ) )
						{
							$arrItem['version']	= Parser::toVersion( $arrItem['version'] );
							if ( null == $arrMaxItem ||
								Comparator::isGreaterThan( $arrItem['version'], $arrMaxItem['version'] ) )
							{
								$arrMaxItem = $arrItem;
							}
						}
					}

					//	...
					$arrRet = $arrMaxItem;
				}
			}

			return $arrRet;
		}


		//
		//	download file
		//
		private function _DownloadFile( $arrDlObject )
		{
			$bRet	= false;
			$nDl	= false;

			if ( $this->_IsValidManifestItem( $arrDlObject ) )
			{
				if ( is_file( $arrDlObject[ 'name' ] ) )
				{
					//echo " - Remove file " . $arrDlObject[ 'name' ] . "." . PHP_EOL;
					@ unlink( $arrDlObject[ 'name' ] );
				}
				if ( is_file( self::FILE_NAME ) )
				{
					//echo " - Remove file " . self::FILE_NAME . "." . PHP_EOL;
					@ unlink( self::FILE_NAME );
				}


				//	...
				$nDl = @ file_put_contents
				(
					$arrDlObject['name'],
					file_get_contents( $arrDlObject['url'] )
				);
				if ( false !== $nDl )
				{
					$bRet = true;
				}
			}

			return $bRet;
		}

		//
		//	install to bin and make it executable
		//
		private function _InstallToBin( $arrDlObject )
		{
			$bRet	= false;

			if ( $this->_IsValidManifestItem( $arrDlObject ) )
			{
				@ rename( $arrDlObject[ 'name' ], self::FILE_BIN_F_NAME );
				@ chmod( self::FILE_BIN_F_NAME, 0755 );
				@ unlink( 'installer.php' );

				//	...
				$bRet = true;
			}

			return $bRet;
		}
		private function _ChangeOwner()
		{
			if ( is_string( $this->m_sCurrentUserName ) && is_string( $this->m_sCurrentGroupName ) )
			{
				//
				//	chown
				//
				$sCmd = sprintf
				(
					"sudo chown -R %s:%s \"%s\" > /dev/null 2>&1",
					$this->m_sCurrentUserName,
					$this->m_sCurrentGroupName,
					self::DIR_CONFIG
				);
				@ exec( $sCmd );

				$sCmd = sprintf
				(
					"sudo chown %s:%s \"%s\" > /dev/null 2>&1",
					$this->m_sCurrentUserName,
					$this->m_sCurrentGroupName,
					self::FILE_BIN_F_NAME
				);
				@ exec( $sCmd );
			}
		}


		//
		//	fetch configuration files
		//
		private function _FetchConfigurationFiles()
		{
			$bRet = false;

			try
			{
				//
				//	create dir
				//
				if ( ! is_dir( self::DIR_CONFIG ) )
				{
					if ( mkdir( self::DIR_CONFIG ) )
					{
						//	...
						$sCommand	= sprintf
						(
							"sudo git clone \"%s\" \"%s\" > /dev/null 2>&1",
							"https://github.com/dekuan/ladep.config.git",
							self::DIR_CONFIG
						);
						$arrOutput	= null;
						$nReturnVar	= -1;
						$sCall		= '';

						//
						//	try to execute the clone command
						//
						$sCall	= exec( $sCommand, $arrOutput, $nReturnVar );
						if ( is_dir( sprintf( "%s%s", self::DIR_CONFIG, ".git" ) ) )
						{
							$bRet = true;
						}
					}
					else
					{
						//	failed to make dir
					}
				}
				else
				{
					//	already fetched
					$bRet = true;
				}

				if ( $bRet )
				{
					$arrRemoveCommands	=
					[
						sprintf( "sudo rm -rf %s%s > /dev/null 2>&1", self::DIR_CONFIG, ".git" ),
						sprintf( "sudo rm %s%s > /dev/null 2>&1", self::DIR_CONFIG, ".gitignore" ),
						sprintf( "sudo rm %s%s > /dev/null 2>&1", self::DIR_CONFIG, "LICENSE" ),
						sprintf( "sudo rm %s%s > /dev/null 2>&1", self::DIR_CONFIG, "README.md" ),
					];
					foreach ( $arrRemoveCommands as $sCommand )
					{
						@ system( $sCommand );
					}
				}
			}
			catch ( \Exception $e )
			{
			}

			return $bRet;
		}


		//
		//	verify file
		//
		private function _VerifyFileBySha( $arrDlObject )
		{
			$bRet	= false;

			if ( $this->_IsValidManifestItem( $arrDlObject ) )
			{
				if ( $arrDlObject[ 'sha1' ] === sha1_file( $arrDlObject[ 'name' ] ) )
				{
					$bRet = true;
				}
			}

			return $bRet;
		}

		//
		//	verify file by Phar
		//
		private function _VerifyFileByPhar( $arrDlObject )
		{
			$bRet	= false;

			if ( $this->_IsValidManifestItem( $arrDlObject ) )
			{
				try
				{
					new \Phar( $arrDlObject[ 'name' ] );

					//	...
					$bRet = true;
				}
				catch ( \Exception $e )
				{
					$bRet = false;
					//throw $e;
				}
			}

			return $bRet;
		}


		//
		//	Checks a condition, outputs a message, and exits if failed.
		//
		private function _Check( $sMsgSuccess, $sMsgFailure, $pfnCondition, $bExit = true )
		{
			//
			//	sMsgSuccess	the success message.
			//	sMsgFailure	the failure message.
			//	pfnCondition	the condition to check.
			//	bExit		exit on failure ?
			//
			if ( $pfnCondition() )
			{
				echo " - " . $sMsgSuccess . PHP_EOL;
			}
			else
			{
				echo " x " . $sMsgFailure . PHP_EOL;
				if ( $bExit )
				{
					exit( 1 );
				}
			}
		}

		private function _IsValidManifestItem( $arrItem )
		{
			$bRet = false;

			if ( is_array( $arrItem ) &&
				array_key_exists( 'name', $arrItem ) &&
				array_key_exists( 'sha1', $arrItem ) &&
				array_key_exists( 'url', $arrItem ) &&
				array_key_exists( 'version', $arrItem ) &&
				is_string( $arrItem[ 'name' ] ) &&
				is_string( $arrItem[ 'sha1' ] ) &&
				is_string( $arrItem[ 'url' ] ) &&
				strlen( $arrItem[ 'name' ] ) > 0 &&
				strlen( $arrItem[ 'sha1' ] ) > 0 &&
				strlen( $arrItem[ 'url' ] ) > 0 )
			{
				if ( is_string( $arrItem[ 'version' ] ) )
				{
					if ( strlen( $arrItem[ 'version' ] ) > 0 )
					{
						$bRet = true;
					}
				}
				else if ( is_object( $arrItem[ 'version' ] ) )
				{
					if ( $arrItem[ 'version' ] instanceof Version\Version )
					{
						$bRet = true;
					}
				}
			}

			return $bRet;
		}
	}
}



namespace Dekuan\Version\Exception
{
	use Exception;

	/**
	 *	Throw if an invalid version string representation is used.
	 */
	class InvalidStringRepresentationException extends VersionException
	{
		/**
		 *	The invalid string representation.
		 *	@var string
		 */
		private $version;

		/**
		 *	Sets the invalid string representation.
		 *	@param string $version The string representation.
		 */
		public function __construct($version)
		{
			parent::__construct
			(
				sprintf
				(
					'The version string representation "%s" is invalid.',
					$version
				)
			);

			$this->version = $version;
		}

		/**
		 *	Returns the invalid string representation.
		 *	@return string The invalid string representation.
		 */
		public function getVersion()
		{
			return $this->version;
		}
	}

	/**
	 *	The base library exception class.
	 */
	class VersionException extends Exception
	{
	}
}



namespace Dekuan\Version
{
	use Dekuan\Version\Exception\InvalidStringRepresentationException;

	/**
	 *	Compares two Version instances.
	 */
	class Comparator
	{
		//
		//	The version is equal to another.
		//
		const EQUAL_TO		= 0;

		//
		//	The version is greater than another.
		//
		const GREATER_THAN	= 1;

		//
		//	The version is less than another.
		//
		const LESS_THAN		= -1;

		//
		//	Compares one version with another.
		//
		//	@param Version $left The left version to compare.
		//	@param Version $right The right version to compare.
		//
		//	@return integer Returns Comparator::EQUAL_TO if the two versions are
		//	equal. If the left version is less than the right
		//	version, Comparator::LESS_THAN is returned. If the left
		//	version is greater than the right version,
		//	Comparator::GREATER_THAN is returned.
		//
		public static function compareTo( Version $cLeft, Version $cRight )
		{
			switch ( true )
			{
				case ( $cLeft->getMajor() < $cRight->getMajor() ):
					return self::LESS_THAN;
				case ( $cLeft->getMajor() > $cRight->getMajor() ):
					return self::GREATER_THAN;
				case ( $cLeft->getMinor() > $cRight->getMinor() ):
					return self::GREATER_THAN;
				case ( $cLeft->getMinor() < $cRight->getMinor() ):
					return self::LESS_THAN;
				case ( $cLeft->getPatch() > $cRight->getPatch() ):
					return self::GREATER_THAN;
				case ( $cLeft->getPatch() < $cRight->getPatch() ):
					return self::LESS_THAN;
				// @codeCoverageIgnoreStart
			}
			// @codeCoverageIgnoreEnd

			return self::compareIdentifiers
			(
				$cLeft->getPreRelease(),
				$cRight->getPreRelease()
			);
		}

		//
		//	Checks if the left version is equal to the right.
		//
		//	@param Version $left The left version to compare.
		//	@param Version $right The right version to compare.
		//	@return boolean TRUE if the left version is equal to the right, FALSE
		//	if not.
		//
		public static function isEqualTo( Version $cLeft, Version $cRight )
		{
			return ( self::EQUAL_TO === self::compareTo( $cLeft, $cRight ) );
		}

		//
		//	Checks if the left version is greater than the right.
		//
		//	@param Version $left The left version to compare.
		//	@param Version $right The right version to compare.
		//	@return boolean TRUE if the left version is greater than the right,
		//	FALSE if not.
		//
		public static function isGreaterThan( Version $cLeft, Version $cRight )
		{
			return ( self::GREATER_THAN === self::compareTo( $cLeft, $cRight ) );
		}

		//
		//	Checks if the left version is less than the right.
		//
		//	@param Version $left The left version to compare.
		//	@param Version $right The right version to compare.
		//	@return boolean TRUE if the left version is less than the right,
		//	FALSE if not.
		//
		public static function isLessThan( Version $cLeft, Version $cRight )
		{
			return ( self::LESS_THAN === self::compareTo( $cLeft, $cRight ) );
		}

		//
		//	Compares the identifier components of the left and right versions.
		//
		//	@param array $left The left identifiers.
		//	@param array $right The right identifiers.
		//	@return integer Returns Comparator::EQUAL_TO if the two identifiers are
		//	equal. If the left identifiers is less than the right
		//	identifiers, Comparator::LESS_THAN is returned. If the
		//	left identifiers is greater than the right identifiers,
		//	Comparator::GREATER_THAN is returned.
		//
		public static function compareIdentifiers( array $left, array $right )
		{
			if ( $left && empty( $right ) )
			{
				return self::LESS_THAN;
			}
			elseif ( empty( $left ) && $right )
			{
				return self::GREATER_THAN;
			}

			$l	= $left;
			$r	= $right;
			$x	= self::GREATER_THAN;
			$y	= self::LESS_THAN;

			if ( count( $l ) < count( $r ) )
			{
				$l	= $right;
				$r	= $left;
				$x	= self::LESS_THAN;
				$y	= self::GREATER_THAN;
			}

			foreach ( array_keys( $l ) as $i )
			{
				if ( ! isset( $r[ $i ] ) )
				{
					return $x;
				}

				if ( $l[ $i ] === $r[ $i ] )
				{
					continue;
				}

				if ( true === ( $li = ( false != preg_match( '/^\d+$/', $l[ $i ] ) ) ) )
				{
					$l[ $i ] = intval( $l[ $i ] );
				}

				if ( true === ( $ri = ( false != preg_match( '/^\d+$/', $r[ $i ] ) ) ) )
				{
					$r[ $i ] = intval( $r[ $i ] );
				}

				if ( $li && $ri )
				{
					return ( $l[ $i ] > $r[ $i ] ) ? $x : $y;
				}
				else if ( ! $li && $ri )
				{
					return $x;
				}
				elseif ( $li && ! $ri )
				{
					return $y;
				}

				return strcmp( $l[ $i ], $r[ $i ] );
			}

			return self::EQUAL_TO;
		}
	}



	/**
	 *	Dumps the Version instance to a variety of formats.
	 */
	class Dumper
	{
		//
		//	Returns the components of a Version instance.
		//
		//	@param Version $version A version.
		//	@return array The components.
		//
		public static function toComponents( Version $version )
		{
			return array
			(
				Parser::MAJOR		=> $version->getMajor(),
				Parser::MINOR		=> $version->getMinor(),
				Parser::PATCH		=> $version->getPatch(),
				Parser::PRE_RELEASE	=> $version->getPreRelease(),
				Parser::BUILD		=> $version->getBuild()
			);
		}

		//
		//	Returns the string representation of a Version instance.
		//
		//	@param Version $version A version.
		//	@return string The string representation.
		//
		public static function toString( Version $version )
		{
			return sprintf
			(
				'%d.%d.%d%s%s',
				$version->getMajor(),
				$version->getMinor(),
				$version->getPatch(),
				$version->getPreRelease()
					? '-' . join('.', $version->getPreRelease())
					: '',
				$version->getBuild()
					? '+' . join('.', $version->getBuild())
					: ''
			);
		}
	}




	/**
	 *	Parses the string representation of a version number.
	 */
	class Parser
	{
		//
		//	The build metadata component.
		//
		const BUILD	= 'build';

		//
		//	The major version number component.
		//
		const MAJOR	= 'major';

		//
		//	The minor version number component.
		//
		const MINOR	= 'minor';

		//
		//	The patch version number component.
		//
		const PATCH	= 'patch';

		//
		//	The pre-release version number component.
		//
		const PRE_RELEASE	= 'pre';


		//
		//	Returns the components of the string representation.
		//
		//	@param string $version The string representation.
		//	@return array The components of the version.
		//
		//	@throws InvalidStringRepresentationException If the string representation
		//	is invalid.
		//
		public static function toComponents( $version )
		{
			if ( ! Validator::isVersion( $version ) )
			{
				throw new InvalidStringRepresentationException( $version );
			}

			if ( false !== strpos( $version, '+' ) )
			{
				list( $version, $build ) = explode( '+', $version );
				$build = explode( '.', $build );
			}

			if ( false !== strpos( $version, '-' ) )
			{
				list( $version, $pre ) = explode( '-', $version );
				$pre = explode( '.', $pre );
			}

			list( $major, $minor, $patch ) = explode( '.', $version );

			return array
			(
				self::MAJOR		=> intval( $major ),
				self::MINOR		=> intval( $minor ),
				self::PATCH		=> intval( $patch ),
				self::PRE_RELEASE	=> isset( $pre ) ? $pre : array(),
				self::BUILD		=> isset( $build ) ? $build : array(),
			);
		}

		//
		//	Returns a Version instance for the string representation.
		//
		//	@param string $version The string representation.
		//	@return Version A Version instance.
		//
		public static function toVersion( $version )
		{
			$components = self::toComponents( $version );
			return new Version
			(
				$components[ 'major' ],
				$components[ 'minor' ],
				$components[ 'patch' ],
				$components[ 'pre' ],
				$components[ 'build' ]
			);
		}
	}


	/**
	 *	Validates version information.
	 */
	class Validator
	{
		//
		//	The regular expression for a valid identifier.
		//
		const IDENTIFIER_REGEX	= '/^[0-9A-Za-z\-]+$/';

		//
		//	The regular expression for a valid semantic version number.
		//
		const VERSION_REGEX	= '/^\d+\.\d+\.\d+(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';

		//
		//	Checks if a identifier is valid.
		//
		//	@param string $identifier A identifier.
		//	@return boolean TRUE if the identifier is valid, FALSE If not.
		//
		public static function isIdentifier( $identifier )
		{
			return ( true == preg_match( self::IDENTIFIER_REGEX, $identifier ) );
		}

		//
		//	Checks if a number is a valid version number.
		//
		//	@param integer $number A number.
		//	@return boolean TRUE if the number is valid, FALSE If not.
		//
		public static function isNumber( $number )
		{
			return ( true == preg_match( '/^\d+$/', $number ) );
		}

		//
		//	Checks if the string representation of a version number is valid.
		//
		//	@param string $version The string representation.
		//	@return boolean TRUE if the string representation is valid, FALSE if not.
		//
		public static function isVersion( $version )
		{
			return ( true == preg_match( self::VERSION_REGEX, $version ) );
		}
	}



	/**
	 *	Stores and returns the version information.
	 */
	class Version
	{
		/**
		 *	The build metadata identifiers.
		 *	@var array
		 */
		protected $build;

		/**
		 *	The major version number.
		 *	@var integer
		 */
		protected $major;

		/**
		 *	The minor version number.
		 *	@var integer
		 */
		protected $minor;

		/**
		 *	The patch version number.
		 *	@var integer
		 */
		protected $patch;

		/**
		 *	The pre-release version identifiers.
		 *	@var array
		 */
		protected $preRelease;

		/**
		 *	Sets the version information.
		 *
		 *	@param integer $major The major version number.
		 *	@param integer $minor The minor version number.
		 *	@param integer $patch The patch version number.
		 *	@param array $pre The pre-release version identifiers.
		 *	@param array $build The build metadata identifiers.
		 */
		public function __construct
		(
			$major		= 0,
			$minor		= 0,
			$patch		= 0,
			array $pre	= array(),
			array $build	= array()
		)
		{
			$this->build		= $build;
			$this->major		= $major;
			$this->minor		= $minor;
			$this->patch		= $patch;
			$this->preRelease	= $pre;
		}

		/**
		 *	Returns the build metadata identifiers.
		 *	@return array The build metadata identifiers.
		 */
		public function getBuild()
		{
			return $this->build;
		}

		/**
		 *	Returns the major version number.
		 *	@return integer The major version number.
		 */
		public function getMajor()
		{
			return $this->major;
		}

		/**
		 *	Returns the minor version number.
		 *	@return integer The minor version number.
		 */
		public function getMinor()
		{
			return $this->minor;
		}

		/**
		 *	Returns the patch version number.
		 *	@return integer The patch version number.
		 */
		public function getPatch()
		{
			return $this->patch;
		}

		/**
		 *	Returns the pre-release version identifiers.
		 *	@return array The pre-release version identifiers.
		 */
		public function getPreRelease()
		{
			return $this->preRelease;
		}
	}
}
