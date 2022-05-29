<?php

session_start();

if(!ISSET($_SESSION['username'])) {
    header('location:login.php');
} elseif($_SESSION['level'] >= 3) {
    header('location:index.php?msg=Permission%20Denied');
} else {

    require('conn.php');

    if(ISSET($_GET['class_id'])) {
        $classID = $_GET['class_id'];
    } else {
        $classID = $_POST['classID'];
    }

    // enroll new students in class
    if(ISSET($_POST['enrollButton'])) {

        if(ISSET($_POST['studentsToEnroll'])) {

            $addStudentsToClassQuery = "INSERT INTO `enrollments` (`enrollmentsID`, `classID`, `userID`) VALUES ";

            //add each students to the class
            $appendToQuery = '';
            foreach($_POST['studentsToEnroll'] as $key => $studentID) {
                $appendToQuery = $appendToQuery . '(NULL,' . $classID . ',' . $studentID . ')';
                if ($key != array_key_last($_POST['studentsToEnroll'])) {
                    $appendToQuery = $appendToQuery . ',';
                }
            } 
            $addStudentsToClassQuery = $addStudentsToClassQuery . $appendToQuery;
            mysqli_query($conn, $addStudentsToClassQuery) or DIE('Cannot add students to class.');
        }
        if(empty($classID)) {
            $msg = "No Students Selected.";
        }
    }

    //unenroll students from class
    if(ISSET($_POST['unEnrollButton'])) {

        if(ISSET($_POST['studentsToUnEnroll'])) {

            $removeStudentsFromClassQuery = "DELETE from `enrollments` WHERE (`classID`,`userID`) IN (";
            //add each students to the class
            $appendToQuery = '';
            foreach($_POST['studentsToUnEnroll'] as $key => $studentID) {
                $appendToQuery = $appendToQuery . "(" . $classID . "," . $studentID;
                if ($key != array_key_last($_POST['studentsToUnEnroll'])) {
                    $appendToQuery = $appendToQuery . '),';
                } else {
                    $appendToQuery = $appendToQuery . '))';
                }
            } 
            $removeStudentsFromClassQuery = $removeStudentsFromClassQuery . $appendToQuery;
            mysqli_query($conn, $removeStudentsFromClassQuery) or DIE('Cannot remove students frpm class.');
        }
    }

    //get list of classes
    $classesQuery = "SELECT `classes`.`className`, `classes`.`classID` FROM `classes`";
    $classesData = mysqli_fetch_all(mysqli_query($conn, $classesQuery), MYSQLI_ASSOC) or DIE('Cannot fetch list of classes');
    $currentClass = $classesData[$classID - 1]['className'];

    //get list of students not enrolled in current class
    $studentsNotAlreadyEnrolledQuery = "SELECT * FROM `users` WHERE NOT EXISTS (SELECT * FROM `enrollments` where `users`.`userID` = `enrollments`.`userID` and `enrollments`.`classID` = '" . $classID . "');";
    $studentsNotInClass = mysqli_query($conn, $studentsNotAlreadyEnrolledQuery) or DIR('Bad All Students Query');

    // Get list of students in class
    $studentsQuery = "SELECT `users`.`userID`, `users`.`username`, `users`.`fname`, `users`.`lname` 
                        FROM `users` INNER JOIN `enrollments` on `users`.`userID` = `enrollments`.`userID` 
                        WHERE `enrollments`.`classID` = " . $classID;
    $enrolledStudents = mysqli_query($conn, $studentsQuery) or DIE('Bad classes query');

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
                            <?php if($_SESSION['level'] == 1) {
                                echo '<i class="fa-solid fa-id-card"></i>';
                            } elseif($_SESSION['level'] == 2) {
                                echo '<i class="fa-solid fa-person-chalkboard"></i>';
                            } else {
                                echo '<i class="fa-solid fa-circle-user"></i>';
                            } ?>
                        </h1>
                    </div>
                </div>
                <div class="col-auto">
                    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-floating">
                    <select name="class_id" class="form-select" id="floatingSelectGrid">
                        <option value="0">Select Class</option>
                        <?php
                            foreach($classesData as $class) {
                                if($class['classID'] == $classID) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo '<option value="' . $class['classID'] . '" ' . $selected . '>' . $class['className'] . '</option>';
                            }
                        ?>
                    </select>
                    <label for="floatingSelectGrid">Select a Class</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-floating">
                        <input type="submit" name="viewClass" class="btn btn-success py-3" value="View Class"> 
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
                    <p class="lead">Review the list of available <code class="small">Students</code> or add students <code class="small">Manually</code> to a class.</p>
                </div>
            </div>
            <!--Cards-->
            <div class="row mb-2">
                <div class="col">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                    <h3 class="mb-0"><strong>Class:</strong><span class="text-muted"><?php echo $currentClass; ?></span></h3>
                    <div class="table-responsive mt-2">
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <table class="table align-middle" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <td></td>
                                <td>Surname</td>
                                <td>First Name</td>
                                <td>Username</td>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php
                                while($row = mysqli_fetch_array($enrolledStudents)) {
                                    echo '<tr><td><input type="checkbox" name="studentsToUnEnroll[]" value="' . $row['userID'] . '"></td>';
                                    echo '<td>' . $row['lname'] . '</td>';
                                    echo '<td>' . $row['fname'] . '</td>';
                                    echo '<td>' . $row['username'] . '</td></tr>';
                                } ?>
                            </tbody>
                        </table>
                        <div class="d-flex align-items-end justify-content-end">
                            <input type="submit" class="btn btn-primary" name="unEnrollButton" value="Unenroll Selected"></td>
                        </div>
                    </div>
                    <!--<a href="#" class="stretched-link">Continue reading</a>-->
                    </div>
                    <div class="col-auto d-none d-lg-block">
                    <!--<svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>-->
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">
                            <h3 class="mb-0"><strong>Available</strong><span class="text-muted">Students</span></h3>
                            <div class="table-responsive mt-2">
                                <table class="table align-middle" width="100%" cellspacing="0">
                                    <thead class="table-light">
                                        <td></td>
                                        <td>Surname</td>
                                        <td>Given</td>
                                        <td>Username</td>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        while($row = mysqli_fetch_array($studentsNotInClass)) {
                                            echo '<tr><td><input type="checkbox" name="studentsToEnroll[]" value="' . $row['userID'] . '"></td>';
                                            echo '<td>' . $row['lname'] . '</td>';
                                            echo '<td>' . $row['fname'] . '</td>';
                                            echo '<td>' . $row['username'] . '</td></tr>';
                                        } ?>
                                    </tbody>
                                </table>
                                <div class="d-flex align-items-end justify-content-end">
                                    <input type="hidden" name="classID" value="<?php echo $classID; ?>">
                                    <input type="submit" class="btn btn-primary" name="enrollButton" value="Enroll Selected"></td>
                                </div>
                            </div>
                            </form>
                        </div>
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