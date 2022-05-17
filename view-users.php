<?php
ob_start();
include './includes/header.php';

//user in time login
$a = $_COOKIE['name'];
$b = $_COOKIE['password'];
$result = mysqli_query($conn, "select * from $database.`user` where `fullname` = '$a' and `password` = '$b' ");
$thisuser = mysqli_fetch_assoc($result);
$thisuser['rule'] == 'editor' ? header("location:index.php") : '';

$alert = '';
$search = '';
if (isset($_POST['search_value'])) {
    $search = $_POST['search_value'];
}
// all users
$users = mysqli_query($conn, "select * from $database.`user` WHERE `fullname` LIKE '%$search%' order by reg_date desc");
if (mysqli_num_rows($users) === 0) {
    $alert = "Data Not Found!, Please Refresh That Page!";
}

// sql to delete a record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM $database.`user` WHERE `id`= '$id'");
    if ($conn->affected_rows === 1) {
        $alert = "Record deleted successfully, Please Refresh That Page!";
    } elseif ($conn->affected_rows === 0) {
        $alert = "Error deleting record!";
    }
}

if (isset($_POST['edit'])) {
    $id = validate($_POST['id']);
    $rule = $_POST['rule'];

    if (empty($error_update)) {
        $sql = "UPDATE $database.`user` SET `rule`='$rule' WHERE `id` = $id";
        $alert = '';
        if ($conn->query($sql) === TRUE) {
            $alert = "Record updated successfully, Please Refresh That Page!";
        } else {
            $alert = "Error updating record!";
        }
    }
}
?>

<div>

    <div class="page-heading d-flex justify-content-between">
        <div class="d-flex align-items-center gap-4">
            <h3>View Users</h3>
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

    <div class="page-content">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <section class="row">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-column align-items-center flex-md-row justify-content-md-between gap-3">
                            <p class="card-text d-flex align-items-center mb-0 pe-5 text-capitalize">
                            Only the user can allow access to edit their profile. For now, the administrator can only delete the user and change the user's rule!
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-group has-icon-left d-flex gap-1">
                                    <input type="text" id="search-box" name="search_value" value="" class="form-control py-2 search-box" style="width: 200px;" placeholder="Search...">
                                    <button name="search_btn" class="btn btn-sm btn-primary py-2">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg p-0">
                                <thead>
                                    <tr class="border-top-0">
                                        <th>Fullname</th>
                                        <th>Tagename</th>
                                        <th>Phone</th>
                                        <th>Experience</th>
                                        <th>Address</th>
                                        <th>Email</th>
                                        <th>Start Work</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $users->fetch_assoc()) { ?>
                                        <tr>
                                            <td class="col-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md">
                                                        <img src="<?= 'images/' . $user['image']; ?>">
                                                    </div>
                                                    <p class="font-bold ms-3 mb-0 text-capitalize"><?php echo $user['fullname'] ?></p>
                                                </div>
                                            </td>
                                            <td class="col-2">
                                                <p class=" mb-0">@<?php echo $user['tagname'] ?></p>
                                            </td>
                                            <td class="col-1">
                                                <p class=" mb-0"><?php echo $user['phone'] ?></p>
                                            </td>
                                            <td class="col-1 text-capitalize">
                                                <p class=" mb-0"><?php echo $user['ex'] ?></p>
                                            </td>
                                            <td class="col-2 text-capitalize">
                                                <p class=" mb-0"><?php echo $user['addr'] ?></p>
                                            </td>
                                            <td class="col-2">
                                                <p class=" mb-0"><?php echo $user['email'] ?></p>
                                            </td>
                                            <td class="col-2 text-uppercase">
                                                <p class=" mb-0"><?= date("d/m/Y h:i a", strtotime($user['reg_date'])) ?></p>
                                            </td>
                                            <td class="col-auto">
                                                <!-- Button trigger for danger theme modal -->
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#danger<?php echo $user['id'] ?>">
                                                    Delete
                                                </button>
                                                <!--Danger theme Modal -->
                                                <div class="modal fade text-left" id="danger<?php echo $user['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h5 class="modal-title white" id="myModalLabel120">
                                                                    Delete User
                                                                </h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                    <i data-feather="x"></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-capitalize">
                                                                are you sure for delete this user?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                                    <i class="bx bx-x d-block d-sm-none"></i>
                                                                    <span class="d-none d-sm-block">Close</span>
                                                                </button>
                                                                <a id="seccess" href=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);
                                                                                        echo '?id=' . $user['id'] ?> " class="btn btn-danger ml-1">
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
                                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#info<?= $user['id'] ?>">
                                                    Edit
                                                </button>
                                                <!--info theme Modal -->
                                                <div class="modal fade text-left" id="info<?= $user['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                        <form method="POST" class="modal-content">
                                                            <div class="modal-header bg-info">
                                                                <h5 class="modal-title white" id="myModalLabel120">
                                                                    Edit Rule
                                                                </h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                    <i data-feather="x"></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-capitalize">
                                                                <div class="form-group d-flex gap-4 mt-3">
                                                                    <label for="formFile" class="form-label text-capitalize">select Rule</label>
                                                                    <div class="d-flex gap-4">
                                                                        <div class="form-check form-check-primary d-flex gap-1">
                                                                            <input class="form-check-input" type="radio" name="rule" value="admin" id="Primary" <?= $user['rule'] == 'admin' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label" for="Primary">
                                                                                Admin
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check form-check-secondary d-flex gap-1">
                                                                            <input class="form-check-input" type="radio" name="rule" value="editor" id="Danger" <?= $user['rule'] == 'editor' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label" for="Danger">
                                                                                Editor
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                                    <i class="bx bx-x d-block d-sm-none"></i>
                                                                    <span class="d-none d-sm-block">Close</span>
                                                                </button>
                                                                <button id="seccess" name="edit" class="btn btn-info ml-1">
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
                </div>
            </section>
        </form>
    </div>

</div>

<?php
include './includes/footer.php';
?>