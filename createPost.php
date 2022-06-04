<?php
ob_start();
include './includes/header.php';
include './includes/page.php';

//user in time login
$a = validate($_COOKIE['name']);
$b = validate($_COOKIE['password']);

$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$user = mysqli_fetch_assoc($result);
$userid = $user['id'];


$alert = '';

$users = mysqli_query($conn, "SELECT * FROM $database.`post` WHERE `userid` = '$userid'");
$num_row = mysqli_num_rows($users);

// sql to create a record
if (isset($_POST['create'])) {

    $title = validate($_POST['title']);
    $details = validate($_POST['details']);
    $privilege = validate($_POST['privilege']);

    $errors = array();
    if (!empty($_FILES['image']['size'])) {

        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];

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

            $sql = "INSERT INTO $database.`post`(`title`, `details`, `privilege`, `image`, `userid`) VALUES ('$title','$details','$privilege','$newfilename','$userid')";
            $alert = '';
            if ($conn->query($sql) === TRUE) {
                move_uploaded_file($file_tmp, "images/post/" . $newfilename);
                $alert = "Record updated successfully, Please Refresh That Page!";
            } else {
                $alert = "Error updating record!" . $conn->error;
            }
        }
    } else {
        $alert = "Please Image Upload for post!";
    }
}

// sql to save a record
if (isset($_POST['save'])) {

    $title = validate($_POST['title']);
    $details = validate($_POST['details']);

    $sql = "INSERT INTO $database.`savepost`(`title`, `details`, `userid`) VALUES ('$title','$details','$userid')";
    $alert = '';
    if ($conn->query($sql) === TRUE) {
        $alert = "Record Save successfully, Please Refresh That Page!";
    } else {
        $alert = "Error Save record!" . $conn->error;
    }
}

?>

<style>
    .main {
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        width: 100%;
    }

    .main div {
        width: 100%;
        height: auto;
        text-align: center;
        font-size: 20px;
        font-weight: 500;
        color: white;
        background-color: #435ebe;
    }

    .clockpart {
        display: inline-block;
    }

    .clockmin {
        width: 5px !important;
        min-width: 5px !important;
        display: inline-block;
    }
</style>

<div>

    <div class="page-heading d-flex justify-content-between">
        <h3>Create Post</h3>
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

    <div class="page-content">
        <form method="POST" class="row" enctype="multipart/form-data">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Input Post Title
                            </div>
                            <div class="card-body">
                                <input type="text" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES) : ''; ?>" id="title" name="title" class="form-control py-2 fs-5" placeholder="Title" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Textarea For Post Detail
                            </div>
                            <div class="card-body">
                                <div class="form-group with-title mb-3">
                                    <textarea class="form-control" id="detail" name="details" rows="10" required><?php echo isset($_POST['details']) ? htmlspecialchars($_POST['details'], ENT_QUOTES) : ''; ?></textarea>
                                    <label>Post Details</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Upload Image For This Post
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="inputGroupFile01">
                                        <i class="bi bi-upload"></i>
                                    </label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="">
                                <div class="stats-icon purple">
                                    <i class="bi bi-collection-fill fs-4 w-auto h-50"></i>
                                </div>
                            </div>
                            <div class="">
                                <h6 class="text-muted font-semibold">Post User</h6>
                                <h6 class="font-extrabold mb-0"><?= $num_row ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        Multiple choices Tage
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <select class="choices form-select multiple-remove" multiple="multiple">
                                <optgroup label="Figures">
                                    <option value="romboid">Romboid</option>
                                    <option value="trapeze" selected>Trapeze</option>
                                    <option value="triangle">Triangle</option>
                                    <option value="polygon">Polygon</option>
                                </optgroup>
                                <optgroup label="Colors">
                                    <option value="red">Red</option>
                                    <option value="green">Green</option>
                                    <option value="blue" selected>Blue</option>
                                    <option value="purple">Purple</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="card-header m-0 p-4">
                        Time For Now
                    </div> -->
                    <div class="px-4 pt-0 pb-4">
                        <div class="main">
                            <div class="clockContainer py-2 rounded">
                                <p id="date" class="m-0 p-0 fs-3"></p>
                                <hr class="my-2">
                                <p id="time" class="m-0 p-0 fs-1"></p>
                                <span id="hours" class="clockpart"></span>
                                <span id="colon1" class="clockmin"></span>
                                <span id="minutes" class="clockpart"></span>
                                <span id="colon2" class="clockmin"> </span>
                                <span id="seconds" class="clockpart"></span>
                                <span id="ampm" class="clockpart"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="col-12 mt-2 mb-1">
                        <div class="form-group d-flex flex-column justify-content-center p-2 pt-3">
                            <label for="formFile" class="form-label text-capitalize">select type of privilege</label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-primary d-flex gap-4">
                                    <input class="form-check-input" type="radio" name="privilege" value="public" id="Primary" checked>
                                    <label class="form-check-label" for="Primary">
                                        Public
                                    </label>
                                </div>
                                <div class="form-check form-check-danger d-flex gap-4">
                                    <input class="form-check-input" type="radio" name="privilege" value="private" id="Danger">
                                    <label class="form-check-label" for="Danger">
                                        Private
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap px-4 pt-0 pb-4 gap-2">
                        <button name="create" class="btn btn-lg btn-primary py-2">Create Post</button>
                        <button name="save" class="btn btn-lg btn-secondary py-2">Save Post</button>
                        <button type="button" onclick="ClearFields()" class="btn btn-lg btn-dark py-2">Clear Filds</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    function ClearFields() {
        document.getElementById("title").value = "";
        document.getElementById("detail").value = "";
        document.getElementById("image").value = "";
    }

    var myVar = setInterval(myTimer, 1000);
    var hours = ["12",
        "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12",
        "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"
    ];
    var months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];

    function myTimer() {
        var date = new Date();
        document.getElementById("date").innerHTML =
            date.getDate() +
            "-" +
            months[date.getMonth()] +
            "-" +
            date.getFullYear();
        document.getElementById("hours").innerHTML = hours[date.getHours()];
        document.getElementById("minutes").innerHTML = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        document.getElementById("seconds").innerHTML = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        document.getElementById("ampm").innerHTML = date.getHours() < 12 ? "AM" : "PM";

        if (document.getElementById("colon1").innerHTML.includes(":")) {

            document.getElementById("colon2").innerHTML = "";
            document.getElementById("colon1").innerHTML = "";
        } else {

            document.getElementById("colon2").innerHTML = ":";
            document.getElementById("colon1").innerHTML = ":";
        }

    }
</script>

<?php
include './includes/footer.php';
?>