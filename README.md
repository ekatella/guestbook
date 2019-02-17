## Installation

* git clone https://github.com/ekatella/guestbook.git
* cd guestbook 
* composer.phar install (or composer install depends on your settings)
* set domain name in constant DOMAIN_NAME in /guestbook/index.php
* create database for project (default in settings - guestbook)
* put your database settings to /guestbook/config/db.php
* run php script /guestbook/scripts/db_init.php to create tables 

## Description

Directory /guestbook/engine contains core functionality and it could be reused theoretically. 
I wrote simplified version of own framework which would need to be developed more properly if more time was given. 

Folder /guestbook/app contains application modules that are  needed for bussiness logic of guestbook

Javascript code is in /guestbook/template/js folder and bundled javascript file is put to /guestbook/assets folder.

If you modify javascript files, you need to rebuild javascript bundle and you can do this by excecuting the following commands:

* cd template/js
* npm install
* npm run build

 Note that this requires Node.js to be installed.
 
## TODO

I have not implemented: 

* It should be developed ORM - now its just simulation of Active record in some cases, using simple PDO factory
* Implement Request and Response classes
* Implement Registry pattern for global configs
* Develop pagination for messages
* Error handler and normally proccess errors
* Cover with tests
* so on :slightly_smiling_face:

