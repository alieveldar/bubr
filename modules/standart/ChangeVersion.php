<?
session_start();
$_SESSION[$_GET['to']] = 1;
header('Location: '.str_replace('m.', '', $_SERVER['HTTP_REFERER']));
?>