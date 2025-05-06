<?php
session_start();
$erros = $_SESSION["erros"] ?? [];
unset($_SESSION["erros"]);

?>
<html>
<head>
<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body>
<div>

</div>
<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Registration Form</h3>
            <form action = "handel_register.php" method="post">
            <?php


           // if (!empty ($_SESSION ["erros"])) {
            //   foreach($_SESSION ["erros"] as $input_key => $erros){
            //     echo "<small style='color:red;'>$erros</small>" . "<br>";
            //     }
            //   }

            if(!empty($_GET["msg"])&& $_GET["msg"]=='ar'){

            ?>
            <div class="alert alert-warning" role="alert">
              <strong> ERROR! </strong> you are already registered , please login
            </div>

              <?php
            }?>
                 
              <div class="row">
                <div class="col-md-6 mb-4">

                  <div data-mdb-input-init class="form-outline">
                    <input type="text" name="firstName" id="firstName" class="form-control form-control-lg" />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>
                  <small style='color:red;'><?php
                     if (isset($erros["firstName"])) echo $erros["firstName"];

                  
                  
                  ?></small>
                </div>
                <div class="col-md-6 mb-4">

                  <div data-mdb-input-init class="form-outline">
                    <input type="text" name="lastname" id="lastName" class="form-control form-control-lg" />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["lastname"])) echo $erros["lastname"];
                  ?></small>
                </div>
                
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 d-flex align-items-center">

                  <div data-mdb-input-init class="form-outline datepicker w-100">
                    <input type="date" name= "birthday" class="form-control form-control-lg" id="birthdayDate" />
                    <label for="birthdayDate" class="form-label">Birthday</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["birthday"])) echo $erros["birthday"];
                  ?></small>
                </div>
                <div class="col-md-6 mb-4">

                  <h6 class="mb-2 pb-1">Gender: </h6>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="femaleGender"
                      value="option1" checked />
                    <label class="form-check-label" for="femaleGender">Female</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="maleGender"
                      value="option2" />
                    <label class="form-check-label" for="maleGender">Male</label>
                  </div>                
                </div>
                <small style='color:red;'><?php
                  if (isset($erros["gender"])) echo $erros["gender"];
                  ?></small>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                    <input type="email" name= "email" id="emailAddress" class="form-control form-control-lg" />
                    <label class="form-label" for="emailAddress">Email</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["email"])) echo $erros["email"];
                  ?></small>
                </div>
               
              <div class="col-md-6 mb-4 pb-2">

                <div data-mdb-input-init class="form-outline">
                 <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control form-control-lg" />
                 <label class="form-label" for="phoneNumber">Phone Number</label>
                </div>
                <small style='color:red;'><?php
                  if (isset($erros["phoneNumber"])) echo $erros["phoneNumber"];
                  ?></small>
              </div>
              <div class="row">
              <div class="col-md-6 mb-4">

                  <div data-mdb-input-init class="form-outline">
                    <input type="text" name="adress" id="firstName" class="form-control form-control-lg" />
                    <label class="form-label" for="adress">Adress</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["adress"])) echo $erros["adress"];
                  ?></small>
              </div>
                <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                    <input type="password" name= "password" id="emailAddress" class="form-control form-control-lg" />
                    <label class="form-label" for="password">Password</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["password"])) echo $erros["password"];
                  ?></small>
                </div>
              
              
                <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                    <input type="password" name= "confirmpassword" id="emailAddress" class="form-control form-control-lg" />
                    <label class="form-label" for="confirmpassword"> Confirm Password</label>
                  </div>
                  <small style='color:red;'><?php
                  if (isset($erros["confirmpassword"])) echo $erros["confirmpassword"];
                  ?></small>
                </div>
               
                

              

              <div class="mt-5 pt-4">
                <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</body></html>