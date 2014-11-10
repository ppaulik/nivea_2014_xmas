Nivea 2014 Christmas
====================

Install application:
-------

download .zip

    cd Nivea2014Christmas
    php composer.phar self-update
    php composer.phar install

Database:
--------

	config database → /config/config.(development|production).neon

	install → php www/index.php orm:schema-tool:create
	or sql  → /app/sql/install.sql
