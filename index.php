<?php
ob_start();
include './includes/header.php';

//user in time login
$a = validate($_COOKIE['name']);
$b = validate($_COOKIE['password']);

$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);
$userid = $user['id'];
$rule = $user['rule'];

$posts = mysqli_query($conn, "SELECT * FROM $database.`post` WHERE `userid` = '$userid'");
$num_row = mysqli_num_rows($posts);

$saveposts = mysqli_query($conn, "SELECT * FROM $database.`savepost` WHERE `userid` = '$userid'");
$num_row_save = mysqli_num_rows($saveposts);

// top 3 news users
$users = mysqli_query($conn, "select * from $database.`user` order by reg_date desc limit 3");

// top 3 news users
$morePost = mysqli_query($conn, "SELECT *, COUNT(*) `published` FROM $database.`post` INNER JOIN $database.`user` ON `user`.`id` = `post`.`userid` GROUP BY `post`.`userid` ORDER BY COUNT(*) desc LIMIT 3;");


?>


<div>

    <div class="page-heading d-flex justify-content-between">
        <h3>Profile Statistics</h3>
        <div class="d-flex align-items-center gap-3">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <button name="reload" class="btn btn-sm btn-lg btn-danger rounded-lg">
                    <i class="fa fa-refresh"></i>
                </button>
            </form>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <button name="logout" class="btn btn-lg btn-danger rounded-lg">Logout</button>
            </form>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Profile Views</h6>
                                        <h6 class="font-extrabold mb-0">112.000</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Followers</h6>
                                        <h6 class="font-extrabold mb-0">183.000</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="bi bi-collection-fill fs-4 w-auto h-50"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">User Publication</h6>
                                        <h6 class="font-extrabold mb-0"><?= $num_row ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Saved Post</h6>
                                        <h6 class="font-extrabold mb-0"><?= $num_row_save ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-capitalize">Post of the months</h4>
                            </div>
                            <div class="card-body">
                                <div id="post-of-months"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-capitalize">most publication</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th class="myTable">Fullname</th>
                                                <th class="myTable">Experience</th>
                                                <th class="myTable">Published</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($morePostUser = $morePost->fetch_assoc()) { ?>
                                                <tr>
                                                    <td class="col-3 myTable">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-md">
                                                                <img src="<?= 'images/' . $morePostUser['image'] ?>">
                                                            </div>
                                                            <p class="font-bold ms-3 mb-0 text-capitalize"><?= $morePostUser['fullname'] ?></p>
                                                        </div>
                                                    </td>
                                                    <td class="col-auto myTable">
                                                        <p class=" mb-0"><?= $morePostUser['ex'] ?></p>
                                                    </td>
                                                    <td class="col-auto myTable">
                                                        <p class=" mb-0"><?= $morePostUser['published'] ?></p>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-5">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="<?= 'images/' . $user['image']; ?>" alt="Face 1">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold text-capitalize"><?= $user['fullname']; ?></h5>
                                <h6 class="text-muted mb-0">@<?= $user['tagname']; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Users</h4>
                    </div>
                    <div class="card-content pb-4">
                        <?php while ($user = $users->fetch_assoc()) { ?>
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg">
                                    <img src="<?= 'images/' . $user['image']; ?>">
                                </div>
                                <div class="name ms-4">
                                    <h5 class="mb-1 text-capitalize"><?= $user['fullname'] ?></h5>
                                    <h6 class="text-muted mb-0">@<?= $user['tagname']; ?></h6>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="px-4">
                            <?= $rule == 'admin' ? '<a href="view-users.php" class="btn btn-block btn-xl btn-light-primary font-bold mt-3">Show More</a>' : ''; ?>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">percentage gender of users</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-gender"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>

<?php
include './includes/footer.php';
?>