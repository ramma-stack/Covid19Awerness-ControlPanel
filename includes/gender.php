<?php
include 'config.php';

//user in time login
$a = $_COOKIE['name'];
$b = $_COOKIE['password'];
$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);
$id = $user['id'];

$result = array();

$morePost = mysqli_query($conn, "SELECT round(COUNT(Gender) / (SELECT COUNT(*) FROM $database.`user`) * 100, 1) '%' FROM $database.`user` GROUP BY Gender ORDER BY '%' DESC;");
foreach ($morePost as $value) {
    $result[] = $value['%'];
}

echo json_encode($result);
