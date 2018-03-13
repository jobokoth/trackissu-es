<?
include('../configERP.inc.php');

extract ($_POST);
extract ($GLOBALS);
extract ($_GET);
session_start();

spl_autoload_register(function ($class_name) {
    include '../classes/class.' . $class_name . '.php';
});
?>