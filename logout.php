<?php

session_start();

//end! the! session!
session_destroy();

// send em back
header('location: login.php');