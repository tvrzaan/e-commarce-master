<?php
session_start();
require_once('includes/functions.php');
$errors = $_SESSION["erros"] ?? [];
unset($_SESSION["erros"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Register</h2>
                        
                        <?php 
                        echo showGeneralError();
                        echo showSuccess();
                        ?>

                        <form action="/e-commarce-master/public/auth/regester.php" method="post" id="registrationForm" class="needs-validation" novalidate>
                            <!-- CSRF Protection -->
                            <input type="hidden" name="csrf_token" value="<?php echo hash_hmac('sha256', session_id(), 'secret_key'); ?>">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="text" name="firstName" id="firstName" class="form-control form-control-lg" required 
                                               pattern="[A-Za-z]{2,50}" title="Please enter 2-50 letters only">
                                        <label class="form-label" for="firstName">First Name</label>
                                        <?php echo showError('firstName'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="text" name="lastname" id="lastname" class="form-control form-control-lg" required
                                               pattern="[A-Za-z]{2,50}" title="Please enter 2-50 letters only">
                                        <label class="form-label" for="lastname">Last Name</label>
                                        <?php echo showError('lastname'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="date" name="birthday" id="birthday" class="form-control form-control-lg" required
                                               max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>">
                                        <label class="form-label" for="birthday">Birthday</label>
                                        <?php echo showError('birthday'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-2 pb-1">Gender:</h6>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" required checked>
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <?php echo showError('gender'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="email" name="email" id="email" class="form-control form-control-lg" required>
                                        <label class="form-label" for="email">Email</label>
                                        <?php echo showError('email'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control form-control-lg" required
                                               pattern="[0-9]{10,15}" title="Please enter a valid phone number (10-15 digits)">
                                        <label class="form-label" for="phoneNumber">Phone Number</label>
                                        <?php echo showError('phoneNumber'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="form-outline">
                                        <input type="text" name="address" id="address" class="form-control form-control-lg" required
                                               minlength="5" maxlength="200">
                                        <label class="form-label" for="address">Address</label>
                                        <?php echo showError('address'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="password" name="password" id="password" class="form-control form-control-lg" required
                                               minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                               title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 characters">
                                        <label class="form-label" for="password">Password</label>
                                        <?php echo showError('password'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control form-control-lg" required>
                                        <label class="form-label" for="confirmpassword">Confirm Password</label>
                                        <?php echo showError('confirmpassword'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                                <a href="login.php" class="btn btn-link">Already have an account? Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Client-side validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                // Check if passwords match
                var password = document.getElementById('password')
                var confirm = document.getElementById('confirmpassword')
                if (password.value !== confirm.value) {
                    confirm.setCustomValidity('Passwords do not match')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    confirm.setCustomValidity('')
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
    <?php clearMessages(); ?>
</body>
</html>