<?php
session_start();
print_r($_POST);
// $bldgAdmin = filter_input(INPUT_POST, 'newBuildingAdmin');
// echo($bldgAdmin);
// if ($bldgAdmin){
//     echo("it's 1");
// }
// else{
//     echo("it's not 1");
// }
// die();
//require building adin
if (!isset($_SESSION['isBldgAdmin']) || $_SESSION['isBldgAdmin'] != 1){
    echo("not building admin");
    echo($_SESSION['isBldgAdmin']);
    echo("/////");
    // header("location: ../index.php");
    die();
 }
 require_once("../util/db.php");
 $userName = filter_input(INPUT_POST, 'newUserName', FILTER_SANITIZE_STRING);
 $first = filter_input(INPUT_POST, 'newFirst', FILTER_SANITIZE_STRING);
 $last = filter_input(INPUT_POST, 'newLast', FILTER_SANITIZE_STRING);
 $password = filter_input(INPUT_POST, 'newPass', FILTER_SANITIZE_STRING);
 $confirm = filter_input(INPUT_POST, 'confirmPass', FILTER_SANITIZE_STRING);
 $isAdmin = filter_input(INPUT_POST, 'newBuildingAdmin');
 if ($password != $confirm){
     echo("The passwords do not match.  <a href='manageUsers.php'>Try Again</a>");
     die();
 }
 $bldg = $_SESSION['building'];

$result = createUser($userName, $password, $first, $last, $isAdmin, $bldg);

//  $result = createUser();
 
 