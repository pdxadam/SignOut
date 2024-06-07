<?php
    session_start();
    if (!isset($_SESSION['pkUser'])){
        header("Location: ../index.php");
        die();
    }
    if ($_SESSION['mode'] !== "admin"){
        unset($_SESSION['pkUser']);
        $_SESSION['mode'] = "seekAdmin";
        header("Location: ../index.php");
        die();
    }
    require_once("../header.php");
    require_once("../util/db.php");
    echo("<script src='scripts/admin.js'></script>");
    echo("<link rel='stylesheet' href='styles/admin.css'>");
    //get current signouts
    if ($_SESSION['isBldgAdmin'] === "1"){
        echo("<a href='./admin/manageUsers.php' target='SignOutManageUsers'>Manage Users</a>");
    }
    echo("<div class='tabBar' id='tabBar'>"
        . "<span class='tabTitle' id='historyTitle' data-target='history'>History</span>"
        . "<span class='tabTitle' id='settingsTitle' data-target='settings'>Settings</span>"
        . "</div>");
    echo("<div class='tab' id='history'>");
    echo("<h1>Current SignOuts</h1>"
            . "<table>"
            . "<tr><th>Name</th><th>Time Out</th><th>Destination</th></tr>");
    $current = signouts_getCurrent($_SESSION['pkUser']);
    foreach($current as $signout){
        
        echo("<tr><td>"
                . $signout['name'] . "</td>");
        echo("<td>" . $signout['outTime'] . "</td>"
                . "<td>" . $signout['destination'] . "</tr>");
        
    }
    echo("</table>");
    $day = "1 day";
    if (isset($_GET['d'])){
        $d = filter_input(INPUT_GET, "d", FILTER_SANITIZE_NUMBER_INT);
        
        if ($d > 1){
            $day = "$d days";
            
        }
    }
    if (isset($_GET['q'])){
        $q = filter_input(INPUT_GET, "q", FILTER_SANITIZE_STRING);
        
    }
    
 
    $start = date_sub(date_create(), date_interval_create_from_date_string($day));
    $end = date_create();
    echo("<label class='button' for='chkHideNames' id='hideButton' onclick='toggleHideButton()'>Hide Names</label>");
    $past = signouts_getHistory($_SESSION['pkUser'],$start, $end, "outTime DESC", $q);
    echo("<h1>Past Sign-Outs: $day</h1>"
            . "<form action='admin/admin.php' method='GET'><input type='text' placeholder='Days to show' name='d'>"
            . "<input name='q' type='text' placeholder='name to search'><input type='submit' value='Set Range'></form>"
            . "<input type='checkbox' id='chkHideNames'><table>"
            . "<tr><th>Name</th><th>Adjusted Name</th><th>Time Out</th><th>Time In</th><th>Duration</th><th>Destination</th><th>Notes</th></tr>");
    $headerDate;
    foreach($past as $signout){
        $out = date_create($signout['outTime']);
        $in = date_create($signout['inTime']);
        $pk = $signout['pkSignOut'];
        if (date_format($out, "m/d/Y") != date_format($headerDate, "m/d/Y")){
            $headerDate = $out;
            echo("<tr><th colspan='7'>" . date_format($out, "m/d/Y") . "</th></tr>");
        }
        $duration = date_diff($out, $in);
          echo("<tr data-pk='$pk' class='pastSignouts'><td>"
                . $signout['name'] . "</td>"
                  . "<td>" . $signout['adjustName'] . "</td>");
        echo("<td>" . date_format($out, "g:i a") . "</td>"
                . "<td>" . date_format($in, "g:i a") . "</td>"
                . "<td>" . $duration->format("%d days, %h:%I:%S") . "</td>"
                . "<td>" . $signout['destination'] . "</td>"
                . "<td>" . $signout['notes'] . "</td></tr>");
    }
    echo("</table>");
    echo("<div class='cover' id='cover'><div>"
            . "<h1 id='editHeader'>Edit Signout for student</h1>"
            . "<form method='post' action='editSignOut.php'>"
            . "<fieldset><legend>Name</legend>"
            . "<input type='hidden' name='pk' id='pk'>"
            . "<input type='text' name='adjustName' placeholder='enter adjusted name' id='adjustName'>"
            . "</fieldset>"
            . "<fieldset><legend>Sign out at</legend>"
            . "<input type='date' name='startDate' id='startDate'>"
            . "<input type='time' name = 'startTime' id='startTime' step='1'>"
            . "</fieldset><fieldset><legend>Sign back in at</legend>"
            . "<input type='date' name = 'endDate' id='endDate'>"
            . "<input type='time' name = 'endTime' id='endTime' step='1'>"
            . "</fieldset>"
            . "<fieldset>"
            . "<legend>Notes</legend>"
            . "<textarea rows=3 cols=50 name='notes' id='notes'></textarea>"
            . "</fieldset><fieldset>"
            . "<input type='submit' value = 'Save Changes'>"
            . "</fieldset>"
            . "<span class='closeButton' onclick='closeCover();'>X</span>"
            . "<span class='delButton' onclick='deleteEntry();'>&#x1F5D1;</span></form>"
            . "</div></div>");
    echo("</div>");
    echo("<div class='tab hidden' id='settings'>");
    echo("<h1>Change Password</h1>"
    . "<form action='admin/changePasswordAction.php' method='post'>"
    . "<input type='password' placeholder = 'Current Password' name='oldPassword' required>"
    . "<input type='password' placeholder = 'New Password' name='newPassword' onchange = 'checkMatch();' id='newPassword' required>"
    . "<input type='password' name='confirmPassword' placeholder = 'Confirm Password' onkeyup='checkMatch()' id='confirm' required>"
    . "<input type='submit' id='submitPassChange' disabled></form>");
    echo("</div>");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

