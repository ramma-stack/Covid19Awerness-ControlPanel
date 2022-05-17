<?php
ob_start();
include './includes/header.php';

//user in time login
$a = $_COOKIE['name'];
$b = $_COOKIE['password'];
$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$thisuser = mysqli_fetch_assoc($result);
$userid = $thisuser['id'];

$alert = '';
$search = '';
if (isset($_POST['search_value'])) {
    $search = $_POST['search_value'];
}
// all users
$savepost = mysqli_query($conn, "SELECT * FROM $database.`savepost` WHERE `userid` = '$userid' order by `id` desc");
if (mysqli_num_rows($savepost) === 0) {
    $alert = "Data Not Found!, Please Refresh That Page!";
}

// sql to delete a record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    echo $userid;
    $conn->query("DELETE FROM $database.`savepost` WHERE `id`= '$id' AND `userid` = '$userid'");
    if ($conn->affected_rows === 1) {
        $alert = "Record deleted successfully, Please Refresh That Page!";
    } elseif ($conn->affected_rows === 0) {
        $alert = "Error deleting record!";
    }
}

if (isset($_POST['transport'])) {

    $id = validate($_POST['id']);
    $save = mysqli_query($conn, "SELECT * FROM $database.`savepost` WHERE `id` = '$id' AND `userid` = '$userid'");
    $save = mysqli_fetch_assoc($save);
    $title = validate($save['title']);
    $details = validate($save['details']);

    $sql = "INSERT INTO $database.`post`(`title`, `details`, `privilege`, `userid`) VALUES ('$title','$details','private','$userid')";
    $alert = '';
    if ($conn->query($sql) === TRUE) {
        $conn->query("DELETE FROM $database.`savepost` WHERE `id`= '$id' AND `userid` = '$userid'");
        if ($conn->affected_rows === 1) {
            $alert = "Record deleted successfully, Please Refresh That Page!";
        } elseif ($conn->affected_rows === 0) {
            $alert = "Error deleting record!";
        }
    } else {
        $alert = "Error updating record!";
    }
}
?>

<div>

    <div class="page-heading d-flex justify-content-between">
        <div class="d-flex align-items-center gap-4">
            <h3>Saved Posts</h3>
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
        <div class="alert alert-info">
            <i class="bi bi-check-circle"></i>
            <?= $alert ?>
        </div>
    <?php } ?>

    <div class="page-heading">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    Save Post Datatable
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Detail</th>
                                <th>Datetime Save</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($save = $savepost->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $save['title'] ?></td>
                                    <td><?= mb_strimwidth($save['details'], 0, 70, "..."); ?></td>
                                    <td><?= $save['create_date'] ?></td>
                                    <td class="col-auto">
                                        <!-- Button trigger for danger theme modal -->
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#danger<?php echo $save['id'] ?>">
                                            Delete
                                        </button>
                                        <!--Danger theme Modal -->
                                        <div class="modal fade text-left" id="danger<?php echo $save['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title white" id="myModalLabel120">
                                                            Delete Save Post
                                                        </h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <i data-feather="x"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-capitalize">
                                                        Are you sure you want to delete this saved post?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Close</span>
                                                        </button>
                                                        <a id="seccess" href=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);
                                                                                echo '?id=' . $save['id'] ?> " class="btn btn-danger ml-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Accept</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-auto">
                                        <!-- Button trigger for info theme modal -->
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#info<?= $save['id'] ?>">
                                            Transport
                                        </button>
                                        <!--info theme Modal -->
                                        <div class="modal fade text-left" id="info<?= $save['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                <form method="POST" class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title white text-capitalize" id="myModalLabel120">
                                                            transport save post
                                                        </h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <i data-feather="x"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-capitalize">
                                                        Are you sure you are moving this saved post to the post section? Because after transferring the saved post, it will be deleted
                                                    </div>
                                                    <input type="hidden" name="id" value="<?= $save['id'] ?>">
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Close</span>
                                                        </button>
                                                        <button id="seccess" name="transport" class="btn btn-info ml-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Accept</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>

</div>

<?php
include './includes/footer.php';
?>