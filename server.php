<?php
session_start();
include 'conn.php';

if(isset($_POST['register'])){

    $username=trim($_POST['names']);
    $password=trim($_POST['password']);
    $phone=trim($_POST['phone']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $role= 'parent';

    
    $hashedpassword = password_hash($password,PASSWORD_DEFAULT);

    $acc = $conn->query("SELECT full_name FROM users WHERE full_name = '$username'");

    if($acc->num_rows>0){
        ?>
        <script>
            window.alert("Username Aleardy Taken By Another User");
            window.history.back();
        </script>
        <?php

        exit;
    }

    $ins = $conn->query("INSERT INTO users(full_name,phone,pin,role,province,district) VALUES('$username','$phone','$hashedpassword','$role','$province','$district')");
    if($ins){
        ?>
        <script>
            window.alert('🎉Thank You For Registering To Our Platform');
            window.location.href = "http://127.0.0.1:5500/login.html";
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

    $stmt = $conn->prepare("SELECT user_id, pin, full_name FROM users WHERE full_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $pin = $user['pin'];
       if(password_verify($password, $pin)){
        $_SESSION['username'] = $user['full_name'];
        $_SESSION['id'] = $user['user_id'];
        ?>
        <script>
            window.alert("Login Successfull");
            window.location.href = "http://localhost/fin-backend/parent.php";
        </script>
        <?php
       }

       else{
        ?>
        <script>
            window.alert("Incorrect Username Or Password!");
            window.location.href = "http://127.0.0.1:5500/login.html";
        </script>
        <?php
       }
       $stmt->close();
    }
    else{
        ?>
        <script>
            window.alert("Incorrect Username Or Password!");
            window.location.href = "http://127.0.0.1:5500/login.html";
        </script>
        <?php
    }

}

if(isset($_POST['signin'])){

    $username = trim($_POST['names']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT admin_id, password, admin_names FROM admin WHERE admin_names = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $admin = $result->fetch_assoc();
        $hashedPassword = $admin['password'];
       if(password_verify($password, $hashedPassword)){
        $_SESSION['username'] = $admin['admin_names'];
        $_SESSION['id'] = $admin['admin_id'];
        $_SESSION['role'] = 'admin';
        ?>
        <script>
            window.alert("Login Successfull");
            window.location.href = "http://localhost/fin-backend/admin.php";
        </script>
        <?php
       }

       else{
        ?>
        <script>
            window.alert("Incorrect Username Or Password!");
            window.location.href = "http://127.0.0.1:5500/admin.html";
        </script>
        <?php
       }
       $stmt->close();
    }
    else{
        ?>
        <script>
            window.alert("Incorrect Username Or Password!");
            window.location.href = "http://127.0.0.1:5500/login.html";
        </script>
        <?php
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


$sql = $conn->query("SELECT * FROM children WHERE Reg_Number = '$reg'");

if ($sql->num_rows == 0) {
    ?>
    <script>
        alert("Registration Number Not Found");
    </script>
    <?php
    exit;
}


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



    else{
        ?>
        <script>
            window.alert("Failed To Be Deposited!");
        </script>
      <?php
    }
}
