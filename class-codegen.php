<?php

function codeGen()
{

    require('conn.php');
    $string = '';
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 6; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    $classCodes = "select * from `classes` where classCode = '". $string . "'";
    $classCodeCheck = mysqli_query($conn, $classCodes);

    if(mysqli_num_rows($classCodeCheck) > 0) {

        //try again to gen random code
        return codeGen();
    }
    else {
        return $string;
    }
}
