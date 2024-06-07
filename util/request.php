<?php
session_start();
if (!isset($_SESSION['pkUser'])){
    die();
}
require_once("db.php");
$request = filter_input(INPUT_POST, "RQ", FILTER_SANITIZE_STRING);
switch($request){
    case "signin":
        $pk = filter_input(INPUT_POST, "s", FILTER_SANITIZE_NUMBER_INT);
        $result = signUserIn($pk);
        if ($result == false){
            echo("Error: there was a problem signing that user in");
        }
        else{
            echo("TRUE");
        }
        break;
    case "getSignout":
        $pk = filter_input(INPUT_POST, "s", FILTER_SANITIZE_NUMBER_INT);
        $result = getSignoutById($pk);
        if ($result == false){
            echo("Error retrieving result");
        }
        else{
            $jResult = json_encode($result);
            echo($jResult);
        }
        break;
    case "delEntry":
        $pk = filter_input(INPUT_POST, "s", FILTER_SANITIZE_NUMBER_INT);
        $result = deleteSignout($pk);
        if ($result == false){
            echo("Error: could not delete entry");
        }
        else{
            echo("Success");
        }
        
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

