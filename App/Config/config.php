<?php


$dotenv = new Dotenv\Dotenv('../../../env/myPortal/');
$dotenv->load();

$env = ['URLROOT','DEVELOPMENTSTATUS','DB_HOST','DB_NAME','DB_PORT','MAILHOST','MAILPORT','MAILUSER','MAILPASS','CSRFKEY'];
unsetServerAndEnv($env);

$urlroot = getenv('URLROOT');


define('APPROOT',dirname(dirname(__FILE__)));
define('URLROOT',$urlroot);
define('SITENAME','myPortal');
define('VIEWINCLUDE',APPROOT.DS.'Views'.DS.'inc'.DS);
define('HELPERS',APPROOT.DS.'Helpers');
define('APPLIB',APPROOT .DS.'Applib');
define('RBAC',APPROOT .DS.'RBAC');
define('MODELS',APPROOT .DS . 'Models');
define('MIDDLEWARE',APPROOT . DS.'Middleware' .DS);
define('CONTOLLER',APPROOT.DS.'Controller');

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$charset = 'utf8mb4';
$port = getenv('DB_PORT');


$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

define('DBCONFIG',['dbname' => $db, 'dsn'=> $dsn,'dbuser' => getenv('DB_USER'), 'dbpass' => getenv('DB_PASS')]);



define('AUTOR_LIMIT',3); 

define('DEVELOPMENTSTATUS',getenv('DEVELOPMENTSTATUS'));


$mailhost = getenv('MAILHOST');
$mailport = getenv('MAILPORT');
$mailuser = getenv('MAILUSER');
$mailpass = getenv('MAILPASS');
define('PORTALMAIL',['host' => $mailhost,'username' => $mailuser,'port' => $mailport,'pass' => $mailpass]);

$csrfkey =  getenv('CSRFKEY');
define('CSRFKEY',$csrfkey);

setEnv($env);

//unset $_ENV and $_SERVER
function unsetServerAndEnv($env)
{
    $len = count($env);
    for($i = 0; $i < $len; $i++)
    {
        unset($_SERVER[$env[$i]]);
        unset($_ENV[$i]);
    }
}

//clear getenv<name>
function setEnv($env)
{
    $len = count($env);
    for($i = 0; $i < $len; $i++)
    {
        putenv($env[$i]);
    }
}
