<?php

include '../includes/config.php';

if (isset($_COOKIE['name'])) {
    header("location:../index.php");
}

$error_insert = array();
if (isset($_POST['register'])) {
    $tagename;
    $fullname;
    $email;
    $password;
    $gender = $_POST['gender'];

    if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9 ]{7,31}$/', preg_replace('/\s+/', '', validate($_POST['tagename'])))) {
        $error_insert[] = "Wrong Tagename Format!";
    } else {
        $tagename = preg_replace('/\s+/', '', validate($_POST['tagename']));
    }

    if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9 ]{8,31}$/', validate($_POST['fullname']))) {
        $error_insert[] = "Wrong Fullname Format!";
    } else {
        $fullname = validate($_POST['fullname']);
    }

    if (!filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $error_insert[] = "Wrong Email Format!";
    } else {
        $email = validate($_POST['email']);
    }

    if (!preg_match('/^[A-Z]{1}[A-Za-z]{4,31}[0-9_@.]{2,10}$/', validate($_POST['password']))) {
        $error_insert[] = "Wrong Password Format!";
    } else {
        $password = hash('gost', preg_replace('/\s+/', '', validate($_POST['password'])));
    }

    if (empty($error_insert)) {

        $errors = array();
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
            $errors[] = "Sorry, file already exists.";
        } else {

            if (in_array($file_ext, $expensions) === false) {
                $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
            }

            if ($file_size > 3000000) {
                $errors[] = 'File size must be excately 3 MB';
            }

            if (empty($errors) == true) {

                $sql = "INSERT INTO $database.`user` ( `fullname`, `tagname`, `password`, `gender`, `phone`, `ex`, `addr`, `email`, `code`, `rule`, `image`) VALUES ('$fullname', '$tagename', '$password', '$gender', '964', '', '', '$email', '0', 'editor', '$newfilename');";
                // INSERT INTO `user`(`id`, `fullname`, `tagname`, `password`, `gender`, `phone`, `ex`, `addr`, `email`, `code`, `rule`, `image`) VALUES ()
                $alert = '';
                if ($conn->query($sql) === TRUE) {
                    move_uploaded_file($file_tmp, "../images/" . $newfilename);
                    $alert = "Record Inserted successfully";
                } else {
                    $alert = "Error Inserting record!";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mazer Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="stylesheet" href="../assets/css/pages/auth.css">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo mb-5">
                        <a href="index.html"><img src="../assets/images/logo/logo.png" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Sign Up</h1>
                    <p class="auth-subtitle mb-5">Input your data to register to our website.</p>

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

                    <?php if (!empty($error_insert)) {
                        foreach ($error_insert as $er) {
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

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="tagename" class="form-control form-control-xl" value="<?php echo isset($_POST['tagename']) ? htmlspecialchars($_POST['tagename'], ENT_QUOTES) : ''; ?>" placeholder="Tagename" required>
                            <div class="form-control-icon">
                                <i class="bi bi-at"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="fullname" class="form-control form-control-xl" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname'], ENT_QUOTES) : ''; ?>" placeholder="Fullname" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="email" class="form-control form-control-xl" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : ''; ?>" placeholder="Email" required>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" class="form-control form-control-xl" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <div>
                            <label for="formFileLg" class="form-label text-capitalize">Upload your profile image</label>
                            <input class="form-control form-control-lg" id="formFileLg" type="file" name="image">
                        </div>
                        <div class="form-group d-flex gap-4 pt-4">
                            <label for="formFile" class="form-label text-capitalize">select Gender</label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-primary d-flex gap-1">
                                    <input class="form-check-input" type="radio" name="gender" value="male" id="Primary" checked>
                                    <label class="form-check-label" for="Primary">
                                        Male
                                    </label>
                                </div>
                                <div class="form-check form-check-info d-flex gap-1">
                                    <input class="form-check-input" type="radio" name="gender" value="female" id="Danger">
                                    <label class="form-check-label" for="Danger">
                                        Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button name="register" class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Sign Up</button>
                    </form>
                    <div class="text-center mt-4 text-lg fs-4">
                        <p class='text-gray-600'>Already have an account? <a href="login.php" class="font-bold">Log in</a></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>

    </div>
</body>

</html>