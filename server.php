<?php
session_start();
include 'conn.php';

if(isset($_POST['register'])){

    $username=trim($_POST['names']);
    $password=trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $role= 'parent';

    
    $hashedpassword = password_hash($password,PASSWORD_DEFAULT);

    $acc = $conn->query("SELECT full_name FROM users ");

    if($acc->num_rows>0){
        ?>
        <script>
            window.alert("Username Aleardy Taken By Another User");
        </script>
        <?php

        exit;
    }

    $ins = $conn->query("INSERT INTO users(full_name,phone,pin,role,province,district) VALUES('$username','$phone','$hashedpassword','$role','$province','$district')");
    if($ins){
        ?>
        <script>
            window.alert('🎉Thank You For Registering To Our Platform')
        </script>
        <?php
    }

    else{
        ?>
        <script>
            window.alert('❌Failed To Create An Account');
            window.history.back();
        </script>
        <?php
    }

}

if(isset($_POST['login'])){

    $username = trim($_POST['names']);
    $password = trim($_POST['password']);

    $sel = $conn->query("SELECT pin FROM users");

    if($sel->num_rows>0){
        $pass = $sel->fetch_assoc();
        $pin = $pass['pin'];
       if(password_verify($password,$pin)){
        $_SESSION['id'] = $pass['user_id'];
        $_SESSION['username']=$username;
        ?>
        <script>
            window.alert("Login Successfull");
            window.location.href = "http://localhost/fin-backend/P.PHP";
        </script>
        <?php
       }

       else{
        ?>
        <script>
            window.alert("Incorrect Username Or Password!");
            window.Location.href = "http://127.0.0.1:5500/login.html";
        </script>
        <?php
       }
    }

}

if(isset($_POST['deposit'])){

    $username = trim($_POST['username']);
    $reg=trim($_POST['regNumber']);
    $role = "relative/helper";
    $amount=trim($_POST['amount']);
    $type=trim($_POST['type']);
    $pin=trim($_POST['PIN']);

if(isset($_POST['deposit'])){

    $username = trim($_POST['username']);
    $reg = trim($_POST['regNumber']);
    $role = "relative/helper";
    $amount = trim($_POST['amount']);
    $type = trim($_POST['type']);
    $pin = trim($_POST['PIN']);
    $hashpin = password_hash($pin,PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO deposits(username, child_reg, role, amount, deposit_method, pin) VALUES(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $reg, $role, $amount, $type, $hashpin);
    $dep = $stmt->execute();

    if($dep){
        ?>
        <script>
            window.alert("Deposited Successfully!");
        </script>
        <?php
    }
    else{
        ?>
        <script>
            window.alert("Failed To Deposit!");
        </script>
        <?php
    }
}


    if($dep){
        ?>
        <script>
            window.alert("Deposited Successfully!");
        </script>
        <?php
    }

    else{
        ?>
        <script>
            window.alert("Failed To Deposited!");
        </script>
      <?php
    }
}