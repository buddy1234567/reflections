<?php
    require('conn.php');

    $username = $_POST['username'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $error = False;

    if(!empty($username) && !empty($password) && !empty($role)) {
        
        if (strlen($password) >= 5 && strlen($username) >= 4) {

            // check if username exists
            $query = "SELECT `username` FROM `users`;";
            $result = mysqli_query($conn, $query) or DIE('bad SELECT query');
            while ($row = mysqli_fetch_array($result)) {
                $dbuser = $row['username'];
                if ($dbuser == $username) {
                    $error = True;
                }
            }
            if ($error) {
                $msg = 'That username is already taken';
            } else {
                $pwdhash = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO `users` (`userID`, `userLevel`, `fname`, `lname`, `username`, `password`) VALUES (NULL, '$role', '$fname', '$lname', '$username', '$pwdhash');";
                mysqli_query($conn, $query) or DIE('bad Insert query');
                if(ISSET($_POST['fromStudents'])) {
                    header('location: students.php?&msg=success');
                } else {
                    header('location: login.php?&msg=success');
                }
            }
            
        } elseif (strlen($password) < 6) {
            $msg = 'Password must be at least 6 characters.';
        } elseif (strlen($username) < 4) {
            $msg = 'Username must be at least 4 characters.';
        }
    } else {
        $msg = "Please fill in all fields";
    }  