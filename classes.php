<?php

session_start();

if(!ISSET($_SESSION['username'])) {
    header('location:login.php');
} else {

    require('conn.php');

    if(ISSET($_GET['msg_notice'])) {
        $msg_notice = $_GET['msg_notice'];
    }

    // Create a Class
    if(ISSET($_POST['createClass'])) {

        //Validate
        if(!empty($_POST['className']) && $_POST['subjectSelection'] != 0) {
        
            //create class code
            require('classes-codegen.php');
            $classCode = codeGen();

            //create class in DB
            $className = $_POST['className'];
            $subjectSelection = $_POST['subjectSelection'];
            $insertClass = "INSERT INTO `classes` (`classID`, `className`, `subjectID`, `classCode`) VALUES (NULL, '$className', '$subjectSelection', '$classCode')";
            mysqli_query($conn, $insertClass) or DIE('Cannot insert class record');
            $msg_notice = "Class Created!";
        } else {
            $msg = "Whoops! Please complete all fields!";
        }
    }

    // Get list of subjects
    $subjectsQuery = "SELECT * FROM `subjects`";
    $subjectsData = mysqli_query($conn, $subjectsQuery) or DIE('Bad subjects query');

    // Get list of classes
    // If Admin get all Classes
    if($_SESSION['level'] == 1) {
        $classesQuery = "SELECT `classID`, `className`, `classCode`, `subjectName` FROM `classes`
                        INNER JOIN `subjects`
                        ON `classes`.`subjectID` = `subjects`.`subjectID`";
    } else {

        // if teacher or student get only your classes
        $classesQuery = "SELECT `classID`, `className`, `classCode`, `subjectName` FROM `classes`
                        INNER JOIN `subjects`
                        ON `classes`.`subjectID` = `subjects`.`subjectID`";
    }

    $classesData = mysqli_query($conn, $classesQuery) or DIE('Bad classes query');

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
                <?php if($_SESSION['level'] == 1) {
                    echo '<i class="fa-solid fa-id-card"></i>';
                } elseif($_SESSION['level'] == 2) {
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

                        if($_SESSION['level'] <= 2) {
                    ?>
                    <p class="card-text">Create a Class:</p>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="row g-2 mb-3">
                        <div class="col-sm-5">
                            <div class="form-floating">
                                <input type="text" name="className" class="form-control" id="floatingInputGrid" placeholder="name@example.com">
                                <label for="floatingInputGrid">Class Name:</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating">
                                <select name="subjectSelection" class="form-select" id="floatingSelectGrid">
                                    <option value="0" selected>Select Subject</option>
                                    <?php
                                        while($row = mysqli_fetch_array($subjectsData)) {
                                            echo '<option value="' . $row['subjectID'] . '">' . $row['subjectName'] . '</option>';
                                        }
                                    ?>
                                </select>
                                <label for="floatingSelectGrid">Select a Subject</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating">
                                <input type="submit" name="createClass" class="btn btn-outline-primary py-3" value="Create Class"> 
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php }?>
                    <div class="row g-2 <?php if($_SESSION['level'] == 'Admin' or $_SESSION['level'] == 'Student') { ?>mt-5<?php } ?>">
                        <div class="col-md">
                            <h1 class="display-6">Current Classes:</h1>
                            <!-- Table for classes -->
                            <div class="table-responsive">
                                <table class="table table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <td>Class Name</td>
                                        <td>Subject</td>
                                        <td>Class Code</td>
                                        <td></td>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        while($row = mysqli_fetch_array($classesData)) {
                                            echo '<tr><td><a href="class_view.php?class_id=' . $row['classID'] . '">' . $row['className'] . '</td>';
                                            echo '<td>' . $row['subjectName'] . '</td>';
                                            echo '<td>' . $row['classCode'] . '</td>';
                                            if($_SESSION['level'] <= 2) {
                                                echo '<td><a href="classes_update.php?class_id=' . $row['classID']  . '" class="btn btn-outline-success btn-sm">Update</a></td></tr>';
                                            }
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