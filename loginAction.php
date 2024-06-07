<?php
    
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("util/db.php");
session_start();

$user = filter_input(INPUT_POST, 'userName');
$pw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_STRING);
// $db = getConn();
// if ($db == false){
//     error_log("Failed to connect to database in loginAction.php");
//     header("location:index.php");
//     die();
// }

//     $sql = $db->prepare("SELECT * FROM tblUsers WHERE userName = :un");
//     $sql->bindValue(":un", $user);
    
//     if ($sql->execute()){
//         $row = $sql->fetch(PDO::FETCH_ASSOC);
//         if (password_verify($pw, $row['password'])){
//             //the signin is correct
//             $_SESSION['pkUser'] = $row['pkUser'];
//             $_SESSION['first'] = $row['first'];
//             $_SESSION['last'] = $row['last'];
//             $_SESSION['userName'] = $user;
//             $jsOffset = filter_input(INPUT_POST, 'tzOffset', FILTER_SANITIZE_NUMBER_INT);
//             $_SESSION['tzOffset'] = convertOffset($jsOffset);

//             if(password_needs_rehash($row['password'], PASSWORD_DEFAULT)){
//                 $newHash = password_hash($pw, PASSWORD_DEFAULT);
//                 $sql2 = $db->prepare("UPDATE tblUsers SET password = :pw WHERE pkUser = :pk;");
//                 $sql2->bindValue(":pw", $newHash);
//                 $sql2->bindValue(":pk", $row['pkUser']);
//                 if ($sql2->execute()){
//                     error_log("Updated password hash for " . $row['pkUser']);
//                 }
//                 else{
//                     error_log(print_r($sql->errorInfo(),true));
//                 }
                
//             }
        $result = authenticate($user, $pw);
        if ($result === true){
            if ($_SESSION['mode'] === "seekAdmin"){
                $_SESSION['mode'] = "admin";
                header("Location: admin/admin.php");
            }
            else{
                $_SESSION['mode'] = "console";
                header("Location: index.php");
            }
        }
        else{
            session_destroy();
            echo("Unrecognized UserID or Password <a href='index.php'>Try Again</a>");
        }
        
 
            
