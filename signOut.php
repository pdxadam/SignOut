<?php
    require_once("util/db.php");
    session_start();
    $name = filter_input(INPUT_POST, 'signOutName', FILTER_SANITIZE_STRING);
    $destination = filter_input(INPUT_POST, 'destination', FILTER_SANITIZE_STRING);
    
    
    $db = getConn();
    if ($db == false){
       error_log("failed!");
    }
    else{
        
        $sql = $db->prepare("INSERT INTO tblSignOut (name, outTime, fkUser, destination)"
                . " VALUES (:name, NOW(), :fkUser, :destination);");
        $sql->bindValue(":name", $name);
        $sql->bindValue(":fkUser", $_SESSION['pkUser']);
        $sql->bindValue(":destination", $destination);
        if ($sql->execute()){
            //redirect to the other place
            
        }
        else{
            error_log(print_r($sql->errorInfo(),true));
            
            
        }
        $db = null;
        header("Location:index.php");
    }
    
