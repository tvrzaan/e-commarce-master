<?php
session_start();
$erros=[];
if(empty($_REQUEST["firstName"])) $erros ["firstName"] ="firstName is requerd";
if(empty($_REQUEST["lastname"])) $erros ["lastname"] ="lastName is requerd";
if(empty($_REQUEST["birthday"])) $erros ["birthday"] ="birthday is requerd";
if(empty($_REQUEST["gender"])) $erros ["gender"] ="gender is requerd";
if(empty($_REQUEST["email"])) $erros ["email"] ="email is requerd";
if(empty($_REQUEST["phoneNumber"])) $erros ["phoneNumber"] ="phoneNumber is requerd";
if(empty($_REQUEST["adress"])) $erros ["adress"] ="Adress is requerd";
if(empty($_REQUEST["password"]) || empty($_REQUEST["confirmpassword"])){
     $erros ["password"] ="password and confirmpassword is requerd";}
elseif($_REQUEST["password"] != $_REQUEST["confirmpassword"]){
    $erros ["confirmpassword"] ="password and confirmpassword isnot be equl";
}


$firstName =htmlspecialchars( $_REQUEST["firstName"]);
$lastname = htmlspecialchars($_REQUEST["lastname"]);
$birthday = $_REQUEST["birthday"];
$username = $firstName . ' ' . $lastname;
$gender = $_REQUEST["gender"];
$email =filter_var($_REQUEST["email"], FILTER_SANITIZE_EMAIL) ;
$phoneNumber = htmlspecialchars($_REQUEST["phoneNumber"]);
$adress =htmlspecialchars( $_REQUEST["adress"]);
$password = htmlspecialchars($_REQUEST["password"]);
$confirmpassword = htmlspecialchars($_REQUEST["confirmpassword"]);
if(!empty($_REQUEST["email"])&&!filter_var($_REQUEST["email"],FILTER_VALIDATE_EMAIL)) $erros["email"] = "Email invalid format";
if(empty($erros)){
    require_once('classes.php');
    if (!file_exists('classes.php')) {
        die('ملف classes.php مش موجود!');
    }
    try{
        $rsl = n_user::register($username, $email, $phoneNumber, $adress, md5($password), $gender, $birthday);
        header("location:login.php?msg=sr");
        }
        catch(\Throwable $th){ 
            header("location:register.php?msg=ar");
    
        }
}
else{$_SESSION["erros"]=$erros;
    header("location:register.php");
    exit;}

?>