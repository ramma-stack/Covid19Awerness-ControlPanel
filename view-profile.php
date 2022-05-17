<?php
ob_start();
include './includes/header.php';

//user in time login
$a = validate($_COOKIE['name']);
$b = validate($_COOKIE['password']);

$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);


$error_update = array();

if (isset($_POST['change'])) {
    $id = $user['id'];
    $tagename;
    $fullname;
    $email;
    $phone;
    $ex;
    $addr;
    $gender = $_POST['gender'];

    if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{7,31}$/', preg_replace('/\s+/', '', validate($_POST['tagename'])))) {
        $error_update[] = "Wrong Tagename Format!";
    } else {
        $tagename = preg_replace('/\s+/', '', validate($_POST['tagename']));
    }

    if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9 ]{8,31}$/', validate($_POST['fullname']))) {
        $error_update[] = "Wrong Fullname Format!";
    } else {
        $fullname = validate($_POST['fullname']);
    }

    if (!filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $error_update[] = "Wrong Email Format!";
    } else {
        $email = validate($_POST['email']);
    }

    if (!preg_match('/^[0-9]{13}+$/', validate($_POST['phone']))) {
        $error_update[] = "Wrong Phone Number Format!";
    } else {
        $phone = validate($_POST['phone']);
    }

    $ex = validate($_POST['ex']);
    $addr = validate($_POST['addr']);

    if (empty($error_update)) {
        $sql = "UPDATE $database.`user` SET `fullname`='$fullname',`tagname`='$tagename',`phone`='$phone',`gender`='$gender',`ex`='$ex',`addr`='$addr',`email`='$email' WHERE `id`=$id";
        $alert = '';
        if ($conn->query($sql) === TRUE) {
            $alert = "Record updated successfully, Please Refresh That Page!";
        } else {
            $alert = "Error updating record!";
        }
    }
}

$errors = array();
if (isset($_POST['upload'])) {

    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];

    $file_ext_part = explode('.', $file_name);
    $file_ext_end = end($file_ext_part);
    $file_ext = strtolower($file_ext_end);

    $newfilename = rand() . '.' . $file_ext;

    $expensions = array("jpeg", "jpg", "png");
    if (empty($file_name)) {
        echo "errors";
        $errors[] = "Sorry, file already exists.";
    } else {

        if (in_array($file_ext, $expensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }

        if ($file_size > 3097152) {
            $errors[] = 'File size must be excately 3 MB';
        }

        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, "images/" . $newfilename);

            $id = $user['id'];
            $sql = "UPDATE $database.`user` SET `image`='$newfilename' WHERE `id`=$id";
            if ($conn->query($sql) === TRUE) {
                $alert = "Record updated successfully, Please Refresh That Page!";
            }
        }
    }
}
?>
<div>

    <div class="page-heading d-flex justify-content-between">
        <h3>View Profile</h3>
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

    <?php if (!empty($errors)) {
        foreach ($errors as $er) {
    ?>
            <div class="alert alert-danger">
                <i class="bi bi-check-circle"></i>
                <?php echo $er; ?>
            </div>
    <?php
        }
    }
    ?>

    <?php if (!empty($error_update)) {
        foreach ($error_update as $er) {
    ?>
            <div class="alert alert-danger">
                <i class="bi bi-check-circle"></i>
                <?php echo $er; ?>
            </div>
        <?php
        }
    } elseif (!empty($alert)) { ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?php echo $alert; ?>
        </div>
    <?php } ?>

    <div class="mx-3">
        <div class="row">
            <div class="card mb-4 col">
                <div class="card-body text-center">
                    <img src="<?= 'images/' . $user['image']; ?>" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                    <h5 class="my-3 text-capitalize">@<?= $user['tagname']; ?></h5>
                    <p class="text-muted mb-1 text-capitalize"><?= $user['ex']; ?></p>
                    <p class="text-muted mb-4 text-capitalize"><?= $user['addr']; ?></p>
                    <!-- Button trigger for dark modal -->
                    <button type="button" class="btn btn-outline-primary ms-1 text-capitalize" data-bs-toggle="modal" data-bs-target="#dark<?= $user['id']; ?>">
                        change profile
                    </button>
                    <!--Dark theme Modal -->
                    <div class="modal fade text-left" id="dark<?= $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel150" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                                <div class="modal-header bg-dark white">
                                    <span class="modal-title" id="myModalLabel150">
                                        Upload Profile Picture
                                    </span>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                                <div class="modal-body d-flex justify-content-center">
                                    <div class="card mb-1">
                                        <div class="card-header py-1">
                                            <h5 class="card-title">Image Preview</h5>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body p-0">
                                                <p class="card-text"></p>
                                                <!-- File uploader with image preview -->
                                                <img class="img-thumbnail rounded-circle" style="width: 200px;height: 200px;" id="pic" alt="100x100" src="<?= 'images/' . $user['image']; ?>" data-holder-rendered="true">
                                                <div class="d-flex justify-content-center py-2 pt-3">
                                                    <input type="file" class="form-control" style="width: 110px;" name="image" oninput="pic.src=window.URL.createObjectURL(this.files[0])">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-light-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Close</span>
                                    </button>
                                    <button name="upload" class="btn btn-dark ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Accept</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4 mx-4 col-6 text-capitalize">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card-body">
                    <div class="row">
                        <div class="col d-flex align-items-center border-end px-0">
                            <div class="d-flex align-items-center col-sm-4">
                                <p class="mb-0">Tage Name</p>
                            </div>
                            <div class="col-sm-8 ps-0">
                                <input type="text" name="tagename" class="form-control border-0 text-muted text-capitalize" value="<?= $user['tagname']; ?>" id="basicInput" placeholder="Enter Tage Name" required>
                            </div>
                        </div>
                        <div class="col d-flex align-items-center">
                            <div class="d-flex align-items-center col-sm-4">
                                <p class="mb-0">Full Name</p>
                            </div>
                            <div class="col-sm-8 px-0">
                                <input type="text" name="fullname" class="form-control border-0 text-muted text-capitalize" value="<?= $user['fullname']; ?>" id="basicInput" placeholder="Enter Full Name" required>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="d-flex align-items-center col-sm-3">
                            <p class="mb-0">Email</p>
                        </div>
                        <div class="col-sm-9">
                            <input type="Email" name="email" class="form-control border-0 text-muted" value="<?= $user['email']; ?>" id="basicInput" placeholder="Enter Email" required>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="d-flex align-items-center col-sm-3">
                            <p class="mb-0">Phone</p>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" name="phone" class="form-control border-0 text-muted" value="<?= $user['phone']; ?>" id="basicInput" placeholder="Enter number" required>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="d-flex align-items-center col-sm-3">
                            <p class="mb-0">Experience</p>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="ex" class="form-control border-0 text-muted text-capitalize" value="<?= $user['ex']; ?>" id="basicInput" placeholder="Enter Experience" required>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="d-flex align-items-center col-sm-3">
                            <p class="mb-0">Address</p>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="addr" class="form-control border-0 text-muted text-capitalize" value="<?= $user['addr']; ?>" id="basicInput" placeholder="Enter Address" required>
                        </div>
                    </div>
                    <hr class="">
                    <div class="form-group d-flex gap-4">
                        <label for="formFile" class="form-label text-capitalize">select Gender</label>
                        <div class="d-flex gap-4">
                            <div class="form-check form-check-primary d-flex gap-1">
                                <input class="form-check-input" type="radio" name="gender" value="male" id="Primary" <?= $user['gender'] == 'male' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="Primary">
                                    Male
                                </label>
                            </div>
                            <div class="form-check form-check-info d-flex gap-1">
                                <input class="form-check-input" type="radio" name="gender" value="female" id="Danger" <?= $user['gender'] == 'female' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="Danger">
                                    Female
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <button type="submit" name="change" class="btn btn-primary text-capitalize mt-1">save changes</button>
                    </div>
                </form>
            </div>
            <div class="card mb-4 col">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fas fa-globe fa-lg text-warning"></i>
                            <p class="mb-0">https://mdbootstrap.com</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fab fa-github fa-lg" style="color: #333333;"></i>
                            <p class="mb-0">mdbootstrap</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                            <p class="mb-0">@mdbootstrap</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                            <p class="mb-0">mdbootstrap</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                            <p class="mb-0">mdbootstrap</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row pb-5 text-capitalize">
            <div class="col-md-6 ps-0">
                <div class="card mb-4 mb-md-0">
                    <div class="card-body">
                        <p class="mb-4"><span class="text-primary font-italic me-1">assigment</span> Project Status
                        </p>
                        <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                        <div class="progress rounded mb-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pe-0">
                <div class="card mb-4 mb-md-0">
                    <div class="card-body">
                        <p class="mb-4"><span class="text-primary font-italic me-1">assigment</span> Project Status
                        </p>
                        <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                        <div class="progress rounded" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                        <div class="progress rounded mb-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<?php
include './includes/footer.php';
?>