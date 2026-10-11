<?php

// Some DB functions 
if(!function_exists("dbconnect")) { // just in case.  Some people seemed to be having an issue and this was the easiest fix.

function dbconnect($dbhost, $dbuser, $dbpass, $dbname ) {
	$pgsql_access = pg_connect("host=$dbhost user=$dbuser password=$dbpass dbname=$dbname");

	try
	{
		$pgsql_access = pg_connect("host=$dbhost user=$dbuser password=$dbpass dbname=$dbname");
	}
	catch (exception $e)
	{
		if (pg_last_error())
		{
			include(_BASEDIR . "languages/en.php"); // Because we haven't got a language set yet.
			die(_FATALERROR . " " . _NOTCONNECTED);
		}
	}
	// Sadly, it does use a LOT of MySQL escaping, so standards_conforming_strings do need to be turned off.
	// That is, until the code could be cleaned up proper. Not too likely, though.
	pg_query($pgsql_access, "SET standard_conforming_strings TO off");
	return $pgsql_access;
}

function dbquery($query) {
	global $debug, $headerSent, $dbconnect;
	if($debug && $headerSent) echo "<!-- $query -->\n";
	$result = NULL;
	try
	{
			$result = pg_query($dbconnect, $query);
	}
	catch (exception $e)
	{
		if(function_exists('accessDenied')) {
			accessDenied(_FATALERROR . (isADMIN ? "Query: " . $query . "<br />Error: (" . pg_last_error($dbconnect) . ") " . error($dbconnect) : ""));
		}
		else {
			// this is very rare case. Uncomment this if you really lost what is doing. This is before pagesetup.php, so Admin is not set
			//	echo "Query: " . $query . "<br />Error: (" . pg_result_error($dbconnect) . ") " . error($dbconnect)  <br /> ;
			die(" <b>A fatal PgSQL error was encountered.</b><br /> Check tableprefix. ");
		}

	}
	pg_get_result($dbconnect);
	return $result;
}

function dbnumrows($query) {
	global $debug, $dbconnect;
	if ($query === false && $debug) {
		echo "<!-- dbnumrows ".pg_last_error($dbconnect)." -->\n";
	}
	$query = pg_num_rows($query);
	return $query;
}

function dbassoc($query) {
	global $debug, $dbconnect;
	if ($query === false && $debug) {
		echo "<!-- dbassoc ".pg_last_error($dbconnect)." -->\n";
	}
	$query = pg_fetch_assoc($query);
	return $query;
}

// TODO Stupid hack regarding retrieving data... (disabled)
//function dbinsertid($tablename = 0) {
//	global $dbconnect;
//	$result = intval($query);
//	return $result;
//}

function dbrow($query) {
	global $debug, $dbconnect;
	if ($query === false && $debug) {
		if(error($dbconnect)) echo "<!-- dbrow ".pg_last_error($dbconnect)." -->\n";
	}
	$query = pg_fetch_row($query);
	return $query;
}

// Used to escape text being put into the database.
function escapestring($str) {
	global $dbconnect;
   if (!is_array($str)) return pg_escape_string($str);
   return array_map('escapestring', $str);
}

function dbclose( ) {
	global $dbconnect;
	pg_close($dbconnect);
}
// End DB functions
}
?>
