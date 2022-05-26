<?php

session_start();

if(!ISSET($_SESSION['username'])) {
    header('location:login.php');
} elseif($_SESSION['level'] != 'Admin' or $_SESSION['level'] != 'Teacher') {
    header('location:index.php?msg=Permission%20Denied');
} else {

    require('conn.php');

    if(ISSET($_GET['msg_notice'])) {
        $msg_notice = $_GET['msg_notice'];
    }

    // Create a Class
    if(ISSET($_POST['createAccount'])) {

        //Validate
        if(!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['password'])) {

            require('functions.php');
        }
    }

    // Get list of subjects
    if($_SESSION['level'] == 'Admin') {
        $studentsQuery = "SELECT * FROM `users` WHERE `userLevel` NOT IN ('1')";
    } else {
        //$studentsQuery = "SELECT * FROM `users` WHERE "
    }
    $studentData = mysqli_query($conn, $studentsQuery) or DIE('Bad students query');

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reflections: User Registration</title>
        <script src="https://kit.fontawesome.com/4f27650962.js" crossorigin="anonymous"></script>
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
   <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="styles.css" rel="stylesheet">
    </head>
    <body class="d-flex flex-column">
    <header class="p-3 bg-dark text-white">
        <?php require('header.php'); ?>
    </header>
    <main class="flex-shrink-0">
        <div class="container">
            <h1 class="mt-5">Welcome, <?php echo $_SESSION['fname']; ?>
                <?php if($_SESSION['level'] == 'Admin') {
                    echo '<i class="fa-solid fa-id-card"></i>';
                } elseif($_SESSION['level'] == 'Teacher') {
                    echo '<i class="fa-solid fa-person-chalkboard"></i>';
                } else {
                    echo '<i class="fa-solid fa-circle-user"></i>';
                } ?>
            </h1>
            <div class="card">
                <div class="card-body">
                    <?php
                        if(ISSET($msg_notice)) {
                            echo '<div class="alert alert-success" role="alert">' . $msg_notice . '</div>';
                        }
                        if(ISSET($msg)) {
                            echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                        }
                    ?>
                    <p class="card-text">Create Student Account:</p>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="row g-2 mb-3">
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="text" name="username" class="form-control" id="username" placeholder="name@example.com">
                                <label for="fname">Username:</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="text" name="fname" class="form-control" id="fname" placeholder="name@example.com">
                                <label for="fname">First Name:</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="text" name="lname" class="form-control" id="lname" placeholder="name@example.com">
                                <label for="lname">Last Name:</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="password" placeholder="name@example.com">
                                <label for="password">Password:</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating mb-2">
                                <select class="form-select form-select-sm" id="floatingSelect" aria-label="Default select example" name="role">
                                    <option disabled>Select Role</option>
                                    <option selected value="3">Student</option>
                                    <option value="2">Teacher</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="hidden" name="fromStudents" value="students.php">
                                <input type="submit" name="createAccount" class="btn btn-outline-primary py-3" value="Create Student Account"> 
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="row g-2 mt-5">
                        <div class="col-md">
                            <h1 class="display-6">Current Students:</h1>
                            <!-- Table for classes -->
                            <div class="table-responsive">
                                <table class="table table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <td>Username</td>
                                        <td>First Name</td>
                                        <td>Last Name</td>
                                        <td>Role</td>
                                        <td></td>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        while($row = mysqli_fetch_array($studentData)) {
                                            echo '<tr><td>' . $row['username'] . '</td>';
                                            echo '<td>' . $row['fname'] . '</td>';
                                            echo '<td>' . $row['lname'] . '</td>';
                                            if($row['userLevel'] == 2) { $userLevel = "teacher"; } elseif($row['userLevel'] == "3") { $userLevel = "student"; }
                                            echo '<td>' . $userLevel . '</td>';
                                            echo '<td><a href="user_update.php?user_id=' . $row['userID']  . '" class="btn btn-outline-success btn-sm">Update</a></td></tr>';
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require('footer.php'); ?>
   <script src="js/datatables-demo.js"></script>
   </body>
</html>
<?php 
    // end check for valid session
    }
?>