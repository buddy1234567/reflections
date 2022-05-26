<?php

session_start();

// LOGIN VERIFICATION
if (ISSET($_POST['login'])) {

    if(!empty($_POST['username'] && !empty($_POST['password']))){

        require('conn.php');
        $query = "SELECT * FROM `users` WHERE username = '" . $_POST['username'] . "'";
        $result = mysqli_query($conn, $query) or die($msg = 'That username does not exist');

        $userData = mysqli_fetch_array($result);
        if(mysqli_num_rows($result) == 0) {
            $msg = 'That username does not exist';
        } else {

            //user exists, get user info
            $password = $userData['password'];

            //check pwd and authenticate
            if($password == $userData['password']) {
            // if (password_verify($_POST['password'], $password)) {
                //profile info
                if($userData['userLevel'] == '1') {
                    $level = 'Admin';
                } elseif($userData['userLevel'] == '2') {
                    $level = 'Teacher';
                } else {
                    $level = 'Student';
                }
                $_SESSION['level'] = $level;
                $_SESSION['userID'] = $userData['userID'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['fname'] = $userData['fname'];
                $_SESSION['lname'] = $userData['lname'];

                // send em
                header('location: index.php');
            } else {
                $msg = 'You did not enter the correct password';
            }
        } 
    } else {
        $msg = "Missing Username or Password";
    }   
} else {
    $level = '';
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
        <?php
            if(ISSET($_GET['msg'])) {
                echo '<div class="alert alert-success" role="alert">User Registration Successful</div>';
            }
            if(ISSET($msg)) {
                echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
            }
        ?>
        <form method = "POST" action = "<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
            <div class="form-floating">
                <input type="text" class="form-control username" id="floatingInput" placeholder="name@example.com" name="username">
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                <label for="floatingPassword">Password</label>
            </div>

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            <input type ="submit" class="w-100 btn btn-lg btn-primary" name="login" value="Sign in">
        </form>
    </main>
    <?php require('footer.php'); ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  </body>
</html>