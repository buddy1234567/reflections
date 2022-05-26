<?php

session_start();

if(!ISSET($_SESSION['username'])) {
    header('location:login.php');
} else {

    if(ISSET($_GET['msg'])) {
        $msg = $_GET['msg'];
    }
    require('conn.php');

    if(ISSET($_POST['joinClass'])) {

        $joinCode = $_POST['joinCode'];

        if(!empty($joinCode)) {

            $joinQuery = "SELECT `classID` FROM `classes` WHERE `classCode` = '" . $joinCode . "'";
            $result = mysqli_query($conn, $joinQuery) or DIE('Bad Class Code Query');

            //check for valid join code
            if(mysqli_num_rows($result) > 0) {

                $data = mysqli_fetch_array($result);
                $classID = $data['classID'];
                
                //check if already joined this class
                $checkIfEnrolledQuery = "SELECT * FROM `enrollments` WHERE `classID` = '" . $classID . "' AND `userID` = '" . $_SESSION['userID'] . "'";
                $enrolled = 0;
                $enrolled = mysqli_query($conn, $checkIfEnrolledQuery) or DIE('Enroll query error');
                if(mysqli_num_rows($enrolled) == 0) {
                    $enrollQuery = "INSERT INTO `enrollments` (`enrollmentsID`, `classID`, `userID`) VALUES (NULL, '" . $classID . "', '" . $_SESSION['userID'] . "')";
                    mysqli_query($conn, $enrollQuery) or DIE('Bad Enroll Query');
                    $msg_success = "Class Joined!";
                } else {
                    $msg = "You are already registered in this class";
                }
            } else {
                $msg = "Invalid Join Code";
            }
        } else {
            $msg = "No Join Code Provided";
        }
    }

    // Get list of classes
    $classesQuery = "SELECT `classes`.`classID`, `classes`.`className`, `classes`.`classCode`, `subjects`.`subjectName` FROM `enrollments` 
                    INNER JOIN `classes` ON `enrollments`.`classID` = `classes`.`classID`
                    INNER JOIN `subjects` ON `classes`.`subjectID` = `subjects`.`subjectID`
                    WHERE `enrollments`.`userID` = '" . $_SESSION['userID'] . "';";
    $classesData = mysqli_query($conn, $classesQuery) or DIE('Bad classes query');

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reflections: User Registration</title>
        <script src="https://kit.fontawesome.com/4f27650962.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="styles.css" rel="stylesheet">
    </head>
    <body class="d-flex flex-column">
    <header class="p-3 bg-dark text-white">
        <?php require('header.php'); ?>
    </header>
    <main class="flex-shrink-0">
        <div class="container">
            <div class="row g-2 my-5">
                <div class="col-auto me-auto">
                    <div class="form-floating">
                        <h1>Welcome, <?php echo $_SESSION['fname']; ?>
                            <?php if($_SESSION['level'] == 'Admin') {
                                echo '<i class="fa-solid fa-id-card"></i>';
                            } elseif($_SESSION['level'] == 'Teacher') {
                                echo '<i class="fa-solid fa-person-chalkboard"></i>';
                            } else {
                                echo '<i class="fa-solid fa-circle-user"></i>';
                            } ?>
                        </h1>
                    </div>
                </div>
                <div class="col-auto">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-floating">
                        <input type="text" name="joinCode" class="form-control" id="floatingInputGrid" placeholder="name@example.com">
                        <label for="floatingInputGrid">Enter Join Code:</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-floating">
                        <input type="submit" name="joinClass" class="btn btn-success py-3" value="Join Class"> 
                        <?php if($_SESSION['level'] != "Student") { ?>
                            <a href="classes.php" class="btn btn-primary py-3">Create Class</a> 
                        <?php } ?>
                    </div>
                </div>
                </form>
            </div>
            <div class="row">
                <div class="col">
                    <?php 
                        if(ISSET($msg)) {
                            echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                        }
                        if(ISSET($msg_success)) {
                            echo '<div class="alert alert-success" role="alert">' . $msg_success . '</div>';
                        }
                    ?>
                    <p class="lead">Use the boxes below to view available <code class="small">Reflections</code> or use the <code class="small">Classes</code> links to view available classes.</p>
                </div>
            </div>
            <!--Cards-->
            <div class="row mb-2">
                <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <h3 class="mb-0"><strong>My</strong><span class="text-muted">Classes:</span></h3>
                        <div class="table-responsive mt-2">
                            <table class="table align-middle" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <td>Class Name</td>
                                    <td>Subject</td>
                                    <td>Class Code</td>
                                    <td></td>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php
                                    while($row = mysqli_fetch_array($classesData)) {
                                        echo '<tr><td>' . $row['className'] . '</td>';
                                        echo '<td>' . $row['subjectName'] . '</td>';
                                        echo '<td>' . $row['classCode'] . '</td>';
                                        if($_SESSION['level'] == 'Admin' or $_SESSION['level'] == 'Teacher') {
                                            echo '<td><a href="class_view.php?class_id=' . $row['classID']  . '" class="btn btn-outline-success btn-sm">View</a></td></tr>';
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">
                            <h3 class="mb-0"><strong>Available</strong><span class="text-muted">Reflections:</span></h3>
                            <div class="table-responsive mt-2">
                            <table class="table align-middle" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <td>Reflection Name</td>
                                    <td>Class Name</td>
                                    <td>Date Due</td>
                                    <td></td>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php
                                    while($row = mysqli_fetch_array($classesData)) {
                                        echo '<tr><td>' . $row['className'] . '</td>';
                                        echo '<td>' . $row['subjectName'] . '</td>';
                                        echo '<td>' . $row['classCode'] . '</td>';
                                        echo '<td><a href="class_view.php?class_id=' . $row['classID']  . '" class="btn btn-outline-success btn-sm">View</a></td></tr>';
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </main>
    <?php require('footer.php'); ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  </body>
</html>
<?php 
    // end check for valid session
    }
?>