<?php

//run this script to create tables

$db_settings = include dirname(__DIR__) . '/config/db.php';

$dsn = $db_settings['type'] . ':host=' . $db_settings['host'] . ';dbname=' . $db_settings['dbname'];

try {

	$db =  new \PDO($dsn, $db_settings['user'], $db_settings['password']);

	$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

	$db->exec('SET NAMES UTF8');

	$sql_users = "CREATE TABLE users (
     id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
     name VARCHAR(60) NOT NULL,
     password VARCHAR(255) NOT NULL,
     email VARCHAR(255) NOT NULL,
     salt VARCHAR(10) NOT NULL
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	$db->exec($sql_users);

	$sql_messages = "CREATE TABLE messages (
     id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT ,
     user_id INT(11) NOT NULL,
     parent_id INT(11) DEFAULT NULL,
     text TEXT NOT NULL,
     created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;" ;

	$db->exec($sql_messages);

} catch (\PDOException $e) {

	echo $e->getMessage();

}



