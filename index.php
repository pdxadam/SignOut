<?php
require_once("header.php");
echo("<meta http-equiv='refresh' content='120'>");
echo("<script src='scripts/signOut.js?ver=3.32'></script>");
require_once("util/db.php");
if (isset($_SESSION['pkUser'])){
    $_SESSION['mode'] = "signOut";
   echo("<div class='flexContainer'>");
echo('
<form action="signOut.php" method="POST">
<fieldset>
        <input type="text" autocomplete = "off" autocorrect="off" spellcheck="false" placeholder="Name" name="signOutName" id="default" required>
        <input type="text" autocomplete = "off" placeholder = "Destination" name = "destination" id = "destination">
        <span class="explain">Leave destination blank for restroom. Your answer is only visible to the teacher.</span>
        <input type="submit" value="Sign out of Class">
        </fieldset>
        </form>
        <div id="signedOut">
        ');

$db = getConn();
$sql = $db->prepare('SELECT * FROM tblSignOut WHERE fkUser = :fk AND inTime IS NULL;');
$sql->bindValue(":fk", $_SESSION['pkUser']);
if ($sql->execute()){
    if ($sql->rowCount() == 0){
        echo("<h3 id='signOutHeader'>No sign outs currently</h3>");
    }
    else{
        echo("<h3 id='signOutHeader'>Click your name to sign back in</h3>");
    }
while ($row = $sql->fetch(PDO::FETCH_ASSOC)){
    echo("<div id='signout" . $row['pkSignOut'] . "' class='signOut'>");
   
    
    echo("<span class='name'>" . $row['name'] . "</span>");
    echo("<span class='outTime'>" . $row['outTime'] . "</span>");
    
    echo("</div>");
    
}
}
else{
    error_log(print_r($sql->errorInfo(),true));
}


echo('</div></div>');

}
else{
    echo('
        <form action="loginAction.php" method="POST">
        <input type="text" placeholder="User Name" name="userName" id="default">
        <input type="hidden" id="tzOffset" name = "tzOffset">
        <input type="password" placeholder="Password" name="pw">
        <input type="submit" value="Log In">
        </form>
            ');
}
require_once("footer.php");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

