# ladep deploy

Ladep is the world's best deploy tool of Laravel framework.


# Install

    composer install -vvv


# Launch

	$ php bin/ladep
	$ ./bin/ladep
	$ ./ladep.phar

# Build

Install the [box](https://github.com/box-project/box2) tool, then run `box build` to build
a PHAR file. Then you can launch the app: `./ladep.phar`. It's the easiest way to build a phar without any line of PHP code.

### install box

	$ curl -LSs https://box-project.github.io/box2/installer.php | php


### build phar via box

	$ box build


### Settings for php.ini while errors occurred

```
[Phar]
; http://php.net/phar.readonly
phar.readonly = Off

; http://php.net/phar.require-hash
phar.require_hash = Off

```

### Settings of MySQL

```
create database homestead;
grant ALL privileges on homestead.* to 'homestead'@'localhost' identified by 'secret';
FLUSH PRIVILEGES;
```


### Resources

[https://moquet.net/blog/distributing-php-cli/](https://moquet.net/blog/distributing-php-cli/)



### Auto update

manifest.json
[
    {
        "name": "cliph.phar",
        "sha1": "fbcded58df0ea838c17d56a5e3cdace56127d538",
        "url": "http://mattketmo.github.io/cliph/downloads/my-cli-1.1.0.phar",
        "version": "1.1.0"
    }
]

; create sha1 value
openssl sha1 <file>






