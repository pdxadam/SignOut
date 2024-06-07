<?php

require_once("secret.php");
function getConn(){

    
    try{
        $connection = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (isset($_SESSION['tzOffset'])){
            $offset = $_SESSION['tzOffset'];
            if (strlen($offset) > 3){
                $connection->exec("SET time_zone='" . $_SESSION['tzOffset'] . "';");
            }
        }
        return $connection;

    }catch(PDOException $ex){
        error_log($ex->getMessage());
        return false;
    }//end try/Catch
}//ebd getConnection()
//create a user
function convertOffset($offset){
    if (!is_numeric($offset)){
        return false;
    }
    $offsetHours = $offset / 60;
    $offsetMInutes = $offset % 60;
    $isNeg = true;
    if ($offsetHours < 0){
        $isNeg = false;
        $offsetHours = $offsetHours * -1;


    }
    $strOffset = ($isNeg ? "-" : "") . str_pad($offsetHours,2,"0", STR_PAD_LEFT) . ":" . str_pad($offsetMinutes,2,"0", STR_PAD_LEFT);
    return $strOffset;

}
function createUser($user, $pw, $first, $last, $isBldgAdmin = false, $bldg = null, $pk = null){
    //returns newly generated pk, or the given pk, or false
    $pw = password_hash($pw, PASSWORD_DEFAULT);
    $db = getConn();
    if ($bldg == null){
        $bldg = $_SESSION['building'];
    }
    if ($isBldgAdmin == null){
        $isBldgAdmin = false;
    }
    if ($pk !== null){
        $sql = $db->prepare("UPDATE tblUsers SET username = :user, password = :pw, "
                . "first = :first, last = :last, isBuildingAdmin = :admin, fkBuilding = :bldg WHERE pkUser = :pk;");
        $sql->bindValue(":pk", $pk);
                
    }else{
        $sql = $db->prepare("INSERT INTO tblUsers (userName, password, first, last, isBuildingAdmin, fkBuilding)"
            . " VALUES (:user, :pw, :first, :last, :admin, :bldg);");
    }
    $sql->bindValue(":user", $user);
    $sql->bindValue(":pw", $pw);
    $sql->bindValue(":first", $first);
    $sql->bindValue(":last", $last);
    $sql->bindValue(":admin", $isBldgAdmin);
    $sql->bindValue(":bldg", $bldg);
    if ($sql->execute()){
        if ($pk === null){
            $pk = $db->lastInsertId();
        }
        $db = null;
        return $pk;
        
    }
    else{
        $db = null;
        return false;
        
    }
    
    
}
function signUserIn($signIn){

    $db = getConn();
    $sql = $db->prepare("UPDATE tblSignOut SET inTime = NOW() WHERE pkSignOut = :pk AND "
            . " fkUser = :pkUser");
    
    $sql->bindValue(":pk", $signIn);
    $sql->bindValue(":pkUser", $_SESSION['pkUser']);
    if ($sql->execute()){
        $db = null;
        return true;
    }
    else{
        error_log(print_r($sql->errorInfo()));
        $db = null;
        return false;
    }
    
}
function signouts_getCurrent($user, $order = "outTime DESC"){
    $db = getConn();
    if ($db == false){
        return false;
    }
    $sql = $db->prepare("SELECT * FROM tblSignOut WHERE inTime is null and fkUser = :user ORDER BY $order;");
    $sql->bindValue(":user", $user, PDO::PARAM_INT);
    if ($sql->execute()){
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        return $result;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
        return false;
    }
}
function signouts_getHistory($user, $startDate, $endDate, $order = "outTime DESC", $q = ""){
     $db = getConn();
    if ($db == false){
        return false;
    }
    $startDate = date_format($startDate, "Y-m-d H:i:s");
    $endDate = date_format($endDate, "Y-m-d H:i:s");
    $qry = "SELECT * FROM tblSignOut WHERE inTime is not null and "
            . " outTime >= :start and outTime <= :end and fkUser = :user and deleted = 0";
    if ($q !== ''){

        $qry = $qry . " and Name LIKE :q";
    }
    $qry = $qry . " ORDER BY $order;";

    $sql = $db->prepare($qry);
    $sql->bindValue(":user", $user, PDO::PARAM_INT);
    $sql->bindValue(":start", $startDate);
    $sql->bindValue(":end", $endDate);
    if ($q !== ''){
        $q = '%' . $q . '%';
        $sql->bindValue(":q", $q);
        
    }
    
    if ($sql->execute()){
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        return $result;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
        return false;
    }
}
function getSignoutById($pk){
    $db = getConn();
    if ($db == false){
        return false;
    }
    $user = $_SESSION['pkUser'];
    $sql = $db->prepare("SELECT * FROM tblSignOut Where pkSignOut = :pk AND fkUser = :user and deleted = 0"
            . " ORDER BY outTime DESC;");
    $sql->bindValue(":pk", $pk);
    $sql->bindValue(":user", $user);
    if ($sql->execute()){
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        return $results;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
        return false;
    }
}
function deleteSignout($pk){
    $db = getConn();
    if ($db == false){
        return false;
    }
    $user = $_SESSION['pkUser'];
    $sql = $db->prepare("UPDATE tblSignOut SET deleted = 1 WHERE pkSignOut = :pk and fkUser = :user;");
    
    $sql->bindValue(":pk", $pk);
    $sql->bindValue(":user", $user);
    if ($sql->execute()){
        $results = $sql->rowCount();
        $success = true;
        if ($results == 0){
            $success = false;
        }
        $db = null;
        return $success;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
        return false;
    }
}
function authenticate($user, $pw){
    $db = getConn();
    
    if ($db == false){
        error_log("Failed to connect to database in loginAction.php");
        return false;
    }

    $sql = $db->prepare("SELECT * FROM tblUsers WHERE userName = :un");
    $sql->bindValue(":un", $user);
    
    if ($sql->execute()){
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if (password_verify($pw, $row['password'])){
            //the signin is correct
            $_SESSION['pkUser'] = $row['pkUser'];
            $_SESSION['first'] = $row['first'];
            $_SESSION['last'] = $row['last'];
            $_SESSION['userName'] = $user;
            $_SESSION['building'] = $row['fkBuilding'];
            $_SESSION['isBldgAdmin'] = $row['isBuildingAdmin'];
            if (isset($_POST['tzOffset'])){
            
                $jsOffset = filter_input(INPUT_POST, 'tzOffset', FILTER_SANITIZE_NUMBER_INT);
                if (is_numeric($jsOffset) && $jsOffset != 0){
                    $_SESSION['tzOffset'] = convertOffset($jsOffset);
                }
            }
            else{
                $_SESSION['tzOffset'] = "-07:00";
                error_log("No Offset--setting to default");


            }
            if(password_needs_rehash($row['password'], PASSWORD_DEFAULT)){
                $result = changePassword($pw, $row['pkUser'], $db);
               
                if ($result === true){
                    error_log("Updated password hash for " . $row['pkUser']);
                }               
                
            }
            $db = null;
            return true;
        }
        else{
            $db = null;
            return false;
        }
        
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        $db = null;
        return false;
    }
}
function changePassword($pk, $password, $db=null){
   
    $killDB = false;
    if ($db == null){
        $killDB = true;
        $db = getConn();
        if ($db == false){
            return false;
            error_log("Failed to get conn");
        }

    }
    
    $newHash = password_hash($pw, PASSWORD_DEFAULT);
    $sql2 = $db->prepare("UPDATE tblUsers SET password = :pw WHERE pkUser = :pk;");
    $sql2->bindValue(":pw", $newHash);
    $sql2->bindValue(":pk", $row['pkUser']);
    error_log("executing");
    if ($sql2->execute()){
        error_log("Success");
        if ($killDB == true){
            $db = null;
        }
        return true;
    }
    else{
        error_log(print_r($sql->errorInfo(),true));
        if ($killDB == true){
            $db = null;
        }
        return false;
    }
                
}
//$stl = $_GET['stl'];
//if ($stl == 'mouseApple'){
//echo("Creating cd");
//createUser('cdrogosch', 'AppleFlask', 'Christy', 'Drogosch');
//echo("successful");
//} 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getUsers(){
    //validate authenticity
 
    if (!isset($_SESSION['isBldgAdmin']) || $_SESSION['isBldgAdmin'] != 1){
        return "false";
    }
    //get the users from database based on school
    $conn = getConn();
    if ($conn == false){
        return false;
    }
    $sql = $conn->prepare("SELECT pkUser, userName, first, last, fkBuilding, isBuildingAdmin FROM tblUsers WHERE fkBuilding = :bldg;");
    $sql->bindValue(":bldg", $_SESSION['building']);
    if ($sql->execute()){
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $conn = null;
        return $rows;

    }
    else{
        error_log(print_r($sql->errorInfo()), true);
        $conn = null;
        return false;

    }
    //return the results
}
function getUserDetail($pk){
    //validate authenticity
 
    if (!isset($_SESSION['isBldgAdmin']) || $_SESSION['isBldgAdmin'] != 1){
        return "false";
    }
    //get the users from database based on school
    $conn = getConn();
    if ($conn == false){
        return false;
    }
    $sql = $conn->prepare("SELECT pkUser, userName, first, last, fkBuilding, isBuildingAdmin FROM tblUsers WHERE fkBuilding = :bldg and pkUser = :pk;");
    $sql->bindValue(":bldg", $_SESSION['building']);
    $sql->bindValue(":pk", $pk);
    if ($sql->execute()){
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        $conn = null;
        return $user;
    }
    else{
        error_log(print_r($sql->errorInfo()), true);
        $conn = null;
        return false;

    }
    //return the results
}