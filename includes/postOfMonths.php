<?php
include 'config.php';

//user in time login
$a = $_COOKIE['name'];
$b = $_COOKIE['password'];
$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);
$id = $user['id'];

$result = array();
$year = date("Y");

for ($i = 1; $i <= 12; $i++) {
    $start = str_pad($i, 2, '0', STR_PAD_LEFT);
    if ($i === 12) {
        $i = 0;
        $end = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
        // echo '<br>' . $year . '-' . $start . '-01' . ' ' . '' . $year + 1 . '-' . $end . '-01';
        $plusYear = $year + 1;
        $morePost = mysqli_query($conn, "SELECT COUNT(*) `published` FROM $database.`post` INNER JOIN $database.`user` ON `user`.`id` = `post`.`userid` WHERE (`create_date` BETWEEN '$year-$start-01' AND '$plusYear-$end-01') AND (`user`.`id` = $id) GROUP BY `post`.`userid`;");
        $mostPostUser = mysqli_fetch_assoc($morePost);
        if ($mostPostUser) {
            $result[] = $mostPostUser['published'];
        } else {
            $result[] = 0;
        }
        break;
    } else {
        $end = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
        // echo '<br>' . $year . '-' . $start . '-01' . ' ' . $year . '-' . $end . '-01';
        $morePost = mysqli_query($conn, "SELECT COUNT(*) `published` FROM $database.`post` INNER JOIN $database.`user` ON `user`.`id` = `post`.`userid` WHERE (`create_date` BETWEEN '$year-$start-01' AND '$year-$end-01') AND (`user`.`id` = $id) GROUP BY `post`.`userid`;");
        $mostPostUser = mysqli_fetch_assoc($morePost);
        if ($mostPostUser) {
            $result[] = $mostPostUser['published'];
        } else {
            $result[] = 0;
        }
    }
}

echo json_encode($result);
