<?php
    session_start();
    if (! isset($_SESSION['isBldgAdmin']) || $_SESSION['isBldgAdmin'] != 1){
       header("location: ../index.php");
       die();
    }
    require_once("../header.php");
    require_once("../util/db.php");
    echo("<link rel='stylesheet' href='./styles/admin.css'>");
    echo("<div id='leftBar'><h1>Users</h1>");
    echo("<label for='chkNewUserForm' class='button'>+</label>");
    $users = getUsers();
    foreach($users as $user){
        echo("<a class='button' href='./admin/manageUsers.php?pkUser=" . $user['pkUser'] . "' id='". $user['pkUser'] . "'>". $user['first'] . " " . $user['last']. "</a>");

    }
    echo("</div>");
    echo("<div id='userDetail'>");
    //edit user form
   
    if (isset($_GET['pkUser'])){
        $pk = filter_input(INPUT_GET, 'pkUser', FILTER_SANITIZE_NUMBER_INT);
        $userDetail = getUserDetail($pk);
        if ($userDetail != false){
            
            echo("<form method='POST' action='editUser'>");
            echo("<input type='hidden' name='pk' value='" . $userDetail['pkUser'] . "'>");
            echo("<label for='userName'>User Name:</label><input type='text' name='userName' placeholder='User Name' value='" . $userDetail['userName'] . "'>");
            echo("<label for='first'>First Name</label><input type='text' name='first' placeholder='First Name' value='" . $userDetail['first'] . "'>");
            echo("<label for='last'>Last Name</label><input type='text' name='last' placeholder='Last Name' value='" . $userDetail['last'] . "'>");
    
            echo("<label for='chkBuildingAdmin'>Building Admin</label><input type='checkbox' name='chkBuildingAdmin'" . ($userDetail['isBuildingAdmin'] == 1?'checked':'') . ">");
            echo("<input type='submit' value='Save Changes'></form>");
            
            
        }
    }
    //New User Form
    echo("<input type='checkbox' id='chkNewUserForm' class='checkHack'>");

   echo("<div class='cover'><div><label for='chkNewUserForm' class='closeButton'>X</label>");
   echo("<h1>Add New User</h1>");
   echo("<form method='POST' action='./admin/addUserAction.php'>");
   echo("<label for='newUserName'>User Name:</label><input type='text' name='newUserName' placeholder='User Name' required>");
   echo("<p>");
   echo("<label for='newFirst'>First Name:</label><input type='text' name='newFirst' placeholder='First Name'>");
   echo("<p>");
   echo("<label for='newLast'>Last Name:</label><input type='text' name='newLast' placeholder='Last Name' required>");
   echo("<p>");
   echo("<label for='newBuildingAdmin'>Building Admin:</label><input type='checkbox' name='newBuildingAdmin'>");
   echo("<p>");
   echo("<label for='newPass'>Password:</label><input type='password' name='newPass' placeholder='Password'>");
   echo("<p>"); echo("<label for='confirmPass'>Confirm:</label><input type='password' name='confirmPass' placeholder='Confirm Password'>");
   echo("<p>");
   echo("<input type='submit' value='Save Changes'></form>");
   echo("</div>");