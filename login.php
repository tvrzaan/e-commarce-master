<?php
session_start();
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]);



?>
<HTml>
  <head>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

  </head>
<body>
<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-balck text-black" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 pb-5">

              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
              <p class="text-black-50 mb-5">Please enter your login and password!</p>
            <form action="handel_login.php" method="post">


            <?php
            if(!empty($_GET["msg"])&& $_GET["msg"]==="empty_feild"){
              ?>
              <div class="alert alert-danger" role="alert">
               <strong>feild</strong> <br/>please check email and password
              </div>
              
              <?php
            }
            
            ?>
              <div data-mdb-input-init class="form-outline form-balck mb-4">
                <input type="email" name="email" id="typeEmailX" class="form-control form-control-lg" />
                <label class="form-label" for="typeEmailX">Email</label><br/>
                <small style='color:red;'><?php
                  if (isset($errors["email"])) echo $errors["email"];
                  ?></small> 
              </div>
              
              <div data-mdb-input-init class="form-outline form-balck mb-4">
                <input type="password" name="password" id="typePasswordX" class="form-control form-control-lg" />
                <label class="form-label" for="typePasswordX">Password</label><br/>
                <small style='color:red;'><?php
                 if (isset($errors["password"])) echo $errors["password"];
                  ?></small>
              </div>
              
              <p class="small mb-5 pb-lg-2"><a class="text-balck-50" href="#!">Forgot password?</a></p>

              <button data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-dark btn-lg px-5" type="submit">Login</button>
            </form>
              <div class="d-flex justify-content-center text-center mt-4 pt-1">
                <a href="#!" class="text-balck"><i class="fab fa-facebook-f fa-lg"></i></a>
                <a href="#!" class="text-balck"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                <a href="#!" class="text-balck"><i class="fab fa-google fa-lg"></i></a>
              </div>

            </div>

            <div>
              <p class="mb-0">Don't have an account? <a href="register.php" class="text-balck-50 fw-bold">Sign Up</a>
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</HTml>