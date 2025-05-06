<?php
session_start();
$errors = [];

if (empty($_REQUEST["email"])) {
    $errors["email"] = "Email is required";
} elseif (!filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Email format is invalid";
}

if (empty($_REQUEST["password"])) {
    $errors["password"] = "Password is required";
}

$email = filter_var($_REQUEST["email"], FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars($_REQUEST["password"]);

if (!empty($errors)) {
    $_SESSION["errors"] = $errors;
    header("Location: login.php");
    exit;
}
if (empty($errors)){
    require_once("classes.php");
    $user= user:: login($email,md5($password));

    

    if (!empty($user)) {
        $_SESSION ["user"] = serialize($user);
        

        if ($user-> role =="admin") {
            header("location: admin/dashbord/index_admin.php");
            
        }
        elseif($user-> role =="n_user"){
            header("location: n_user/index_user.php");
            exit;
        }
    }
    else{
        header("Location: login.php?msg=no_user");
    }
}

?>
