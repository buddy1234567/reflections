<?php

session_start();

if(!ISSET($_SESSION['username'])) {
    header('location:login.php');
} elseif($_SESSION['level'] != 'Admin' or $_SESSION['level'] != 'Teacher') {
    header('location:index.php?msg=Permission%20Denied');
} else {

    if(!ISSET($_GET['class_id'])){

        header('location:classes.php');
    
    } else {

        // Grab class ID from url
        $class_id = $_GET['class_id'];

        require('conn.php');

        // Get list of subjects
        $subjectsQuery = "SELECT * FROM `subjects`";
        $subjectsData = mysqli_query($conn, $subjectsQuery) or DIE('Bad subjects query');

        // Get class data
        $classesQuery = "SELECT `classID`, `className`, `classCode`, `subjects`.`subjectID`, `subjectName` FROM `classes`
                        INNER JOIN `subjects`
                        ON `classes`.`subjectID` = `subjects`.`subjectID`
                        WHERE `classes`.`classID`=" . $class_id;
        $classesData = mysqli_query($conn, $classesQuery) or DIE('Bad classes query');

        if(ISSET($_POST['updateClass'])) {

            //Validate
            if(!empty($_POST['className']) && $_POST['subjectSelection'] != 0) {

                //update class in DB
                $className = $_POST['className'];
                $subjectSelection = $_POST['subjectSelection'];
                $updateClass = "UPDATE `classes` SET `className` = '$className', `subjectID` = '$subjectSelection' WHERE `classes`.`classID` = '" . $class_id . "';";
                mysqli_query($conn, $updateClass) or DIE('Cannot update class record');
                header('Location:classes.php?class_id=0&msg_notice=Class%20Updated');
            }
        }
        if(ISSET($_POST['generateNewClassCode'])) {
            
            $classID = $_POST['classID'];

            //create class code
            require('class-codegen.php');
            $classCode = codeGen();

            $updateClassQuery = "UPDATE `classes` SET `classCode` = '" . $classCode ."' WHERE `classes`.`classID` =" . $classID;
            mysqli_query($conn, $updateClassQuery) or DIE('Bad classes query');
            header('Location:classes.php?class_id=0&msg_notice=Class%20Code%20Updated');
        }
    }

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
                    <h5 class="card-title">Update Class</h5>
                    <p class="card-text">Make changes to your Class below:</p>
                    <?php while($row=mysqli_fetch_array($classesData)) { ?>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=2">
                        <div class="row g-3">
                            <div class="col-sm-5">
                                <div class="form-floating mb-3">
                                    <input type="text" name="className" class="form-control" id="floatingInputGrid" placeholder="name@example.com" value="<?php echo $row['className']; ?>">
                                    <label for="floatingInputGrid">Class Name:</label>
                                </div>
                                <div class="form-floating">
                                    <input type="submit" name="updateClass" class="btn btn-outline-success" value="Update Class"> <input type="submit" name="generateNewClassCode" class="btn btn-outline-secondary" value="Generate New Class Code">
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-floating">
                                    <select name="subjectSelection" class="form-select" id="floatingSelectGrid">
                                        <option value="<?php echo $row['subjectID']; ?>" selected><?php echo $row['subjectName']; ?></option>
                                        <?php
                                            while($rowSubjects = mysqli_fetch_array($subjectsData)) {
                                                if($row['subjectID'] != $rowSubjects['subjectID']) {
                                                    echo '<option value="' . $rowSubjects['subjectID'] . '">' . $rowSubjects['subjectName'] . '</option>';
                                            
                                                }
                                            }
                                        ?>
                                    </select>
                                    <label for="floatingSelectGrid">Subject Select</label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-floating mb-3">
                                    <input type="text" name="classCode" class="form-control muted" id="floatingInputGrid" placeholder="name@example.com" value="<?php echo $row['classCode']; ?>">
                                    <label for="floatingInputGrid">Class Code:</label>
                                    <input type="hidden" name="classID" value="<?php echo $row['classID']; ?>">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php } ?>
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