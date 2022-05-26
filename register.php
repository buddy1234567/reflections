<?php

session_start();

// LOGIN VERIFICATION
if (ISSET($_POST['register'])) {

    require('functions.php');
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reflections: User Registration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="styles.css" rel="stylesheet">
    </head>
    <body class="d-flex flex-column text-center">
    <header class="p-3 bg-dark text-white">
        <?php require('header.php'); ?>
    </header>
    <main class="form-signin">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1 class="h3 mb-3 fw-normal">User Registration</h1>
            <?php
            if(ISSET($msg)) {
                echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
            }
            ?>
            <div class="form-floating">
                <input type="text" class="form-control username form-control-sm" id="floatingInput" placeholder="name@example.com" name="username">
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control box form-control-sm" id="floatingInput" placeholder="name@example.com" name="fname">
                <label for="floatingInput">First Name</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control box form-control-sm" id="floatingInput" placeholder="name@example.com" name="lname">
                <label for="floatingInput">Last Name</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control form-control-sm" id="floatingPassword" placeholder="Password" name="password">
                <label for="floatingPassword">Password</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select form-select-sm" id="floatingSelect" aria-label="Default select example" name="role">
                    <option disabled>Select Role</option>
                    <option selected value="3">Student</option>
                    <option value="2">Teacher</option>
                </select>
            </div>

            <input type="submit" class="w-100 btn btn-lg btn-primary" name="register" value="Register">
        </form>
    </main>   
    <?php require('footer.php'); ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>