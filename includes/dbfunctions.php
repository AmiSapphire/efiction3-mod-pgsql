<?php

if(function_exists("pg_connect")) {
	include_once(_BASEDIR."includes/pg_functions.php");
	$dbconnect = @dbconnect($dbhost, $dbuser, $dbpass, $dbname);
} else {
	include(_BASEDIR."languages/en.php"); // Because we haven't selected a language setting yet
	die(_FATALERROR._NODBFUNCTIONALITY);
}
 
