<?php
ob_start();
include './includes/header.php';
?>
<div>

    <div class="page-heading d-flex justify-content-between">
        <h3></h3>
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

    <div class="page-heading">
        <section class="section">
            <div class="card d-flex flex-column flex-xxl-row justify-content-evenly align-items-center p-4 p-sm-5 gap-4">
                <div class="">
                    <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                </div>
                <div class="col-12 col-md-10 col-xxl-6 text-center text-xxl-start">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Perferendis expedita voluptatum porro quo nemo soluta rerum iste necessitatibus deserunt laboriosam error, nesciunt minima pariatur possimus qui id, ut eum eveniet. Lorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati vero quibusdam expedita animi dolorum amet debitis. Distinctio unde atque temporibus ipsa ab. Corrupti maiores earum doloremque laudantium. Deserunt, odit sapiente! Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tenetur facere quo odio nostrum inventore cum alias aut amet rerum quos eaque ea sed, nobis deserunt natus tempora architecto quod ex!
                </div>
            </div>
        </section>
    </div>

</div>

<?php
include './includes/footer.php';
?>