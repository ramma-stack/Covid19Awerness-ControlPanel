<?php

if (isset($_POST['logout'])) {
    setcookie("name", $a, time() - 86400, '/'); // second on page time 
    setcookie("password", $b, time() - 86400, '/'); // second on page time
    header("location:./auth/login.php");
}

if (isset($_POST['reload'])) {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    header("location: {$url}");
}

if (!isset($_COOKIE['name'])) {
    header("location:./auth/login.php");
}
