<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

if (isset($_SERVER['HTTP_HOST'])){
	$active_group = $_SERVER['HTTP_HOST'];
}
else
{
	$active_group = 'localhost';
}

$active_record = TRUE;

$db['localhost']['hostname'] = 'localhost';
$db['localhost']['username'] = 'root';
$db['localhost']['password'] = 'dax';
$db['localhost']['database'] = 'twittracker';
$db['localhost']['dbdriver'] = 'mysql';
$db['localhost']['dbprefix'] = '';
$db['localhost']['pconnect'] = TRUE;
$db['localhost']['db_debug'] = TRUE;
$db['localhost']['cache_on'] = FALSE;
$db['localhost']['cachedir'] = '';
$db['localhost']['char_set'] = 'utf8';
$db['localhost']['dbcollat'] = 'utf8_general_ci';
$db['localhost']['swap_pre'] = '';
$db['localhost']['autoinit'] = TRUE;
$db['localhost']['stricton'] = FALSE;

$db['seodev.seo.com']['hostname'] = "localhost";
$db['seodev.seo.com']['username'] = "seodev_seo";
$db['seodev.seo.com']['password'] = "r1per";
$db['seodev.seo.com']['database'] = "seodev_twittracker";
$db['seodev.seo.com']['dbdriver'] = "mysql";
$db['seodev.seo.com']['dbprefix'] = "";
$db['seodev.seo.com']['pconnect'] = TRUE;
$db['seodev.seo.com']['db_debug'] = TRUE;
$db['seodev.seo.com']['cache_on'] = FALSE;
$db['seodev.seo.com']['cachedir'] = "";
$db['seodev.seo.com']['char_set'] = "utf8";
$db['seodev.seo.com']['dbcollat'] = "utf8_general_ci";

$db['216.83.154.58']['hostname'] = "localhost";
$db['216.83.154.58']['username'] = "seodev_seo";
$db['216.83.154.58']['password'] = "r1per";
$db['216.83.154.58']['database'] = "seodev_twittracker";
$db['216.83.154.58']['dbdriver'] = "mysql";
$db['216.83.154.58']['dbprefix'] = "";
$db['216.83.154.58']['pconnect'] = TRUE;
$db['216.83.154.58']['db_debug'] = TRUE;
$db['216.83.154.58']['cache_on'] = FALSE;
$db['216.83.154.58']['cachedir'] = "";
$db['216.83.154.58']['char_set'] = "utf8";
$db['216.83.154.58']['dbcollat'] = "utf8_general_ci";




/* End of file database.php */
/* Location: ./application/config/database.php */