<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//
session_start();
$name = filter_input(INPUT_POST, "adjustName");
$inDate = filter_input(INPUT_POST, "endDate");
$inTime = filter_input(INPUT_POST, "endTime");
$outDate = filter_input(INPUT_POST, "startDate");
$outTime = filter_input(INPUT_POST, "startTime");
$notes = filter_input(INPUT_POST, "notes");
$outStamp = $outDate . " " . $outTime;
$inStamp = $inDate . " " . $inTime;
$pk = filter_input(INPUT_POST, "pk");

if (isset($_SESSION['pkUser']) AND $_SESSION['mode'] == "admin"){
    error_log("got it!");
    require_once("util/db.php");
    $db = getConn();
    $sql = $db->prepare('UPDATE tblSignOut SET adjustName = :name, '
            . ' outTime = :start, inTime = :end, notes = :notes WHERE pkSignOut = :pk;');
    $sql->bindValue(":name", $name);
    $sql->bindValue(":start", $outStamp);
    $sql->bindValue(":end", $inStamp);
    $sql->bindValue(":notes", $notes);
    $sql->bindValue(":pk", $pk);
    if ($sql->execute()){
        $db = null;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
    }
   
}
 header("Location: admin/admin.php");