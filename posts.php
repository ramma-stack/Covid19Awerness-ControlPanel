<?php
ob_start();
include './includes/header.php';

//user in time login
$a = validate($_COOKIE['name']);
$b = validate($_COOKIE['password']);

$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);
$userid = $user['id'];

$alert = '';
$search = '';
if (isset($_GET['search_post'])) {
    $search = $_GET['search_post'];
}

// pagination
$results_per_page  = 8;


if ($user['rule'] == 'editor') {
    $users = mysqli_query($conn, "SELECT * FROM $database.`post` WHERE `title` LIKE '%$search%' AND `userid` = '$userid' order by `id` desc");
    $num_row = mysqli_num_rows($users);
    if ($num_row === 0) {
        $alert = "Data Not Found!";
    }
} else {
    $users = mysqli_query($conn, "SELECT * FROM $database.`post` WHERE `title` LIKE '%$search%' order by `id` desc");
    $num_row = mysqli_num_rows($users);
    if ($num_row === 0) {
        $alert = "Data Not Found!, Please Refresh That Page!";
    }
}

$number_of_page = ceil($num_row / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$number_of_post_on_page = ($page - 1) * $results_per_page;

$users = "SELECT * FROM $database.`post` WHERE `title` LIKE '%$search%' order by `id` desc limit $number_of_post_on_page , $results_per_page";

$result = mysqli_query($conn, $users);

// sql to delete a record
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $userid = $user['id'];
    if ($user['rule'] === 'admin') {
        $conn->query("DELETE FROM $database.`post` WHERE `id` = '$id'");
        if ($conn->affected_rows === 1) {
            $alert = "Record deleted successfully, Please Refresh That Page!";
        } elseif ($conn->affected_rows === 0) {
            $alert = "Error deleting record!";
        }
    } else {
        $conn->query("DELETE FROM $database.`post` WHERE `id` = '$id' AND `userid` = '$userid'");
        if ($conn->affected_rows === 1) {
            $alert = "Record deleted successfully, Please Refresh That Page!";
        } elseif ($conn->affected_rows === 0) {
            $alert = "Error deleting record, unauthorized access!";
        }
    }
}

// sql to update a record
if (isset($_POST['updateImage'])) {

    $id = $_POST['id'];
    $userid = $user['id'];

    $title = validate($_POST['title']);
    $details = validate($_POST['details']);
    $privilege = validate($_POST['privilege']);
    $create_date = date('Y-m-d h:i:s', strtotime($_POST['create_date']));
    // echo '2022-03-15 17:54:00' . ' ' . $create_date;

    $errors = array();
    // echo (!$_FILES['postImage']['error'] == 0) . 'klshdjhfsjbkf';
    if ($_FILES['postImage']['error'] == 0) {
        $file_name = $_FILES['postImage']['name'];
        $file_size = $_FILES['postImage']['size'];
        $file_tmp = $_FILES['postImage']['tmp_name'];
        $file_type = $_FILES['postImage']['type'];

        $file_ext_part = explode('.', $file_name);
        $file_ext_end = end($file_ext_part);
        $file_ext = strtolower($file_ext_end);

        $newfilename = rand() . '.' . $file_ext;

        $expensions = array("jpeg", "jpg", "png");

        if (in_array($file_ext, $expensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG, JPG or PNG file.";
        }

        if ($file_size > 7097152) {
            $errors[] = 'File size must be excately 7 MB';
        }
        if (empty($errors) == true) {

            if ($user['rule'] === 'admin') {
                $sql = "UPDATE $database.`post` SET `title`='$title',`details`='$details',`privilege`='$privilege',`image`='$newfilename',`create_date`='$create_date' WHERE `id` = $id";
                $alert = '';
                if ($conn->query($sql) === TRUE) {
                    move_uploaded_file($file_tmp, "images/post/" . $newfilename);
                    $alert = "Record updated successfully, Please Refresh That Page!";
                } else {
                    $alert = "Error updating record!";
                }
            } else {
                $user_id = $user['id'];
                if ($user_id == $_POST['userid']) {
                    $sql = "UPDATE $database.`post` SET `title`='$title',`details`='$details',`privilege`='$privilege',`image`='$newfilename',`create_date`='$create_date' WHERE `id` = '$id' AND `userid` = '$user_id'";
                    $alert = '';
                    if ($conn->query($sql) === TRUE) {
                        move_uploaded_file($file_tmp, "images/post/" . $newfilename);
                        $alert = "Record updated successfully, Please Refresh That Page!";
                    } else {
                        $alert = "Error updating record!";
                    }
                } else {
                    $alert = "Error updating record, unauthorized access!";
                }
            }
        }
    } else {
        if (empty($errors) == true) {

            if ($user['rule'] === 'admin') {
                $sql = "UPDATE $database.`post` SET `title`='$title',`details`='$details',`privilege`='$privilege',`create_date`='$create_date' WHERE `id` = $id";
                $alert = '';
                if ($conn->query($sql) === TRUE) {
                    $alert = "Record updated successfully, Please Refresh That Page!";
                } else {
                    $alert = "Error updating record!";
                }
            } else {
                $user_id = $user['id'];
                if ($user_id == $_POST['userid']) {
                    $sql = "UPDATE $database.`post` SET `title`='$title',`details`='$details',`privilege`='$privilege',,`create_date`='$create_date' WHERE `id` = '$id' AND `userid` = '$user_id'";
                    $alert = '';
                    if ($conn->query($sql) === TRUE) {
                        $alert = "Record updated successfully, Please Refresh That Page!";
                    } else {
                        $alert = "Error updating record!";
                    }
                } else {
                    $alert = "Error updating record, unauthorized access!";
                }
            }
        }
    }
}

?>

<div>

    <div class="page-heading d-flex justify-content-between">
        <div class="d-flex align-items-center gap-4">
            <h3>View Users</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="form-group has-icon-left d-flex gap-1">
                <div class="position-relative">
                    <input type="text" id="search-box" name="search_post" value="" class="form-control py-2 search-box" placeholder="Search...">
                    <div class="form-control-icon">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>
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

    <?php if (!empty($alert)) { ?>
        <div class="alert alert-primary text-capitalize">
            <i class="bi bi-check-circle"></i>
            <?= $alert ?>
        </div>
    <?php } ?>
    <?php if (!empty($errors)) {
        foreach ($errors as $er) {
    ?>
            <div class="alert alert-danger text-capitalize">
                <i class="bi bi-check-circle"></i>
                <?php echo $er; ?>
            </div>
    <?php
        }
    }
    ?>

    <div class="page-content">
        <section class="row">
            <?php while ($user = mysqli_fetch_array($result)) { ?>
                <div class="col-12 col-lg-3">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title text-uppercase"><?= mb_strimwidth($user['title'], 0, 25, "..."); ?></h4>
                                <p class="card-text text-capitalize">
                                    <?= mb_strimwidth($user['details'], 0, 70, "..."); ?>
                                </p>
                            </div>
                            <img class="img-fluid w-100" style="height: 250px;" src="<?= 'images/post/' . $user['image']; ?>" alt="Card image cap">
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span class="text-uppercase"><?= date("d/m/Y h:i a", strtotime($user['create_date'])) ?></span>
                            <div class="modal-primary me-1 mb-1 d-inline-block">
                                <!-- Button trigger for primary themes modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary<?= $user['id'] ?>">
                                    Details
                                </button>
                                <!--primary theme Modal -->
                                <div class="modal fade text-left" id="primary<?= $user['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title white" id="myModalLabel160">
                                                    Form Update Post
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                    <i data-feather="x"></i>
                                                </button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-body pb-0">
                                                    <div class="card-content">
                                                        <div class="card-body p-2 pb-0">
                                                            <div class="form form-vertical">
                                                                <div class="form-body">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="first-name-vertical">Title</label>
                                                                                <input type="text" id="first-name-vertical" class="form-control" name="title" value="<?= $user['title'] ?>" placeholder="Title" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="email-id-vertical">Time Create</label>
                                                                                <input type="datetime-local" id="email-id-vertical" class="form-control" name="create_date" value="<?= date("Y-m-d\TH:i:s", strtotime($user['create_date'])) ?>" placeholder="Time Create" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="contact-info-vertical">Detail</label>
                                                                                <textarea class="form-control" id="exampleFormControlTextarea1" name="details" rows="10" placeholder="Detail" required><?= $user['details'] ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="formFile" class="form-label text-capitalize">Default file input example</label>
                                                                                <input type="file" name="postImage" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 mt-2 mb-1">
                                                                            <div class="form-group d-flex gap-4">
                                                                                <label for="formFile" class="form-label text-capitalize">select type of privilege</label>
                                                                                <div class="form-check form-check-primary d-flex gap-4">
                                                                                    <input class="form-check-input" type="radio" name="privilege" value="public" id="Primary" <?= $user['privilege'] == 'public' ? 'checked' : ''; ?>>
                                                                                    <label class="form-check-label" for="Primary">
                                                                                        Public
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-danger d-flex gap-4">
                                                                                    <input class="form-check-input" type="radio" name="privilege" value="private" id="Danger" <?= $user['privilege'] == 'private' ? 'checked' : ''; ?>>
                                                                                    <label class="form-check-label" for="Danger">
                                                                                        Private
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                                        <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <div class="d-flex gap-2">
                                                        <button name="updateImage" class="btn btn-success ml-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Update</span>
                                                        </button>
                                                        <button name="delete" class="btn btn-danger ml-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Delete</span>
                                                        </button>
                                                    </div>
                                                    <button class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                        <i class="bx bx-x d-block d-sm-none"></i>
                                                        <span class="d-none d-sm-block">Close</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>

    <nav aria-label="Page navigation example" class="mb-5">
        <ul class="pagination pagination-primary">
            <?php if ($page > 1) { ?>
                <li class="page-item">
                    <?php echo '<a class="page-link" href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?page=' . $page - 1 . '">Prev</a> '; ?>
                </li>
            <?php } else { ?>
                <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
            <?php } ?>
            <?php for ($i = 1; $i <= $number_of_page; $i++) { ?>
                <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                    <?php echo '<a class="page-link" href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?page=' . $i . '">' . $i . '</a> '; ?>
                </li>
            <?php } ?>
            <?php if ($i - 1 > $page) { ?>
                <li class="page-item">
                    <?php echo '<a class="page-link" href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?page=' . $page + 1 . '">Next</a> '; ?>
                </li>
            <?php } else { ?>
                <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
            <?php } ?>
        </ul>
    </nav>

</div>

<?php
include './includes/footer.php';
?>