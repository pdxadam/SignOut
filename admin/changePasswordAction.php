<?php
    session_start();
    require_once("../util/db.php");
    $user = $_SESSION['userName'];
    $pw = filter_input(INPUT_POST, 'oldPassword', FILTER_SANITIZE_STRING);
    $newPw = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
    $confirm = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);
    $result = authenticate($user, $pw);
    if ($result === true && $newPw == $confirm){
        //authentication passed
        $changed = changePassword($user, $newPw);
        echo("Success");

    }
    else{
        //What should we do if it fails?
        echo("Failed");
        echo("authenticate: $result");
        echo($newPw);
    }
