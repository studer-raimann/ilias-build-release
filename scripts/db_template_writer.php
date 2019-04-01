<?php
require_once("libs/composer/vendor/autoload.php");
include_once("Services/Init/classes/class.ilErrorHandling.php");
include_once("Services/Database/classes/class.ilDBWrapperFactory.php");
require_once("./Services/Utilities/classes/class.ilBenchmark.php");

function getArg($args, $short, $long, $default) {
	$value = $default;
	$value = isset($args[$short]) ? $args[$short] : $value;
	$value = isset($args[$long]) ? $args[$long] : $value;
	return $value;
}

$args = getopt("u::p::h::P::", array("user::", "password::", "host::", "port::"));

$dbHost = getArg($args, "h", "host", "127.0.0.1");
$dbPort = getArg($args, "P", "port", "3306");
$dbUser = getArg($args, "u", "user", "root");
$dbPassword = getArg($args, "p", "password", "");
$dbName = end($argv);

global $DIC, $ilDB;

$DIC = new \ILIAS\DI\Container();
$DIC['ilBench'] = new ilBenchmark();

$ilDB = ilDBWrapperFactory::getWrapper("mysql");

$ilDB->setDbHost($dbHost);
$ilDB->setDbPort($dbPort);
$ilDB->setDbName($dbName);
$ilDB->setDbUser($dbUser);
$ilDB->setDbPassword($dbPassword);
$ilDB->connect();

$DIC["ilDB"] = $ilDB;

include_once("Services/Database/classes/class.ilDBAnalyzer.php");
include_once("Services/Database/classes/class.ilMySQLAbstraction.php");
include_once("Services/Database/classes/class.ilDBGenerator.php");

$analyzer = $ilDB;
$abstraction = new ilMySQLAbstraction();
$generator = new ilDBGenerator();

$generator->setBlackList(array("abstraction_progress"));
$generator->buildDBGenerationScript("./setup/sql/ilDBTemplate.php");
