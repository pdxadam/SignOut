<?php
session_start();

echo("
       <!doctype html>
       <html lang='en'>
       <head>
        <meta charset='utf-8'>
        <base href='https://mclainonline.com/SignOut/'>
        <link rel='stylesheet' href='styles/styles.css?ver=2.7'>
        <script src = 'scripts/script.js'></script>
        <title>Classroom Sign Out</title>
        
       </head>
       <body>
        <nav>");
        
        
    if (isset($_SESSION['pkUser'])){
        echo("<a href='logout.php'>logout</a>");
        if ($_SESSION['mode'] == "admin"){
            echo("<a href='index.php'>SignOut Mode</a>");
        }
        else{
            echo("<a href='admin/admin.php'>admin</a>");
        }
        echo("</nav>
        <header>");
        echo($_SESSION['last'] . "'s Classroom");
        echo("<span id='time'></span>");
    }
    else{
        echo("</nav>
        <header>");
        echo("Please Login");
    }
            
       echo("</header>
            ");

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

