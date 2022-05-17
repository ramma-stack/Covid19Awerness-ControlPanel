<?php

include '../includes/config.php';

if (isset($_COOKIE['name'])) {
    header("location:../index.php");
}

if (isset($_REQUEST['login'])) {
    $a = validate($_REQUEST['name']);
    $b = hash('gost', validate($_REQUEST['password']));

    $res = mysqli_query($conn, "SELECT * FROM $database.`user` WHERE `fullname` = '$a' AND `password` = '$b'");
    $result = mysqli_fetch_array($res);

    if ($result) {
        setcookie("name", $a, time() + 86400, '/'); // second on page time 
        setcookie("password", $b, time() + 86400, '/'); // second on page time
        header("location:../index.php");
    } else {
        header("location:login.php?err=1&fullname=$a");
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
                    <div class="auth-logo">
                        <a href="index.html"><img src="../assets/images/logo/logo.png" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input name="name" value="<?php if (isset($_GET['fullname']) && isset($_GET['err'])) {
                                                            echo $_GET['fullname'];
                                                        } ?>" type="text" value="" class="form-control form-control-xl <?php if (isset($_GET['err'])) {
                                                                                                                            echo 'is-invalid';
                                                                                                                        } ?>" placeholder="Fullname">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input name="password" type="password" class="form-control form-control-xl <?php if (isset($_GET['err'])) {
                                                                                                            echo 'is-invalid';
                                                                                                        } ?>" placeholder="Password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button name="login" class="btn btn-primary btn-block btn-lg shadow-lg mt-2">Log in</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">Don't have an account?
                            <a href="reg.php" class="font-bold">Sign up</a>.
                        </p>
                        <p><a class="font-bold" href="recetPassword/forgetpassword.php">Forgot password?</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">

                </div>
            </div>
        </div>

    </div>
</body>

</html>