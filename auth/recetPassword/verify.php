<?php
include '../../includes/config.php';
session_start();

$email = $_SESSION['email'];
$qeury = "SELECT * FROM $database.`user` WHERE `email` = '$email'";
$result = mysqli_query($conn, $qeury);
$row = mysqli_fetch_assoc($result);
echo $row['code'];

if ($row['code'] != 0) {
    if (isset($_POST['checkCode'])) {
        if ($_POST['code'] === $row['code']) {
            $qeury = "UPDATE $database.`user` SET `code` = '0' WHERE `email` = '$email'";
            $result = mysqli_query($conn, $qeury);
            if ($result) {
                echo "update seccess!";
                header('Location:newpassword.php');
            } else {
                echo "update fail!";
            }
        } else {
            echo "Code incorrect";
        }
    }
} else {
    header('Location:forgetpassword.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mazer Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/app.css">
    <link rel="stylesheet" href="../../assets/css/pages/auth.css">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="index.html"><img src="../../assets/images/logo/logo.png" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Confirm Code</h1>
                    <p class="auth-subtitle mb-5 text-capitalize">Input your code and check your email.</p>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="number" name="code" pattern="\d*[0-9]{6}" maxlength="6" class="form-control form-control-xl" placeholder="Enter Your Code" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button name="checkCode" class="btn btn-primary btn-block btn-lg shadow-lg mt-5 text-capitalize">confirm</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class='text-gray-600 text-capitalize'>Remember your account? <a href="../login.php" class="font-bold">Log
                                in</a>.
                        </p>
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