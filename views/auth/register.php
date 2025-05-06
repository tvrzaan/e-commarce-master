<?php
session_start();
require_once('../../includes/functions.php');
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

                        <form action="../../process_register.php" method="post" id="registrationForm" class="needs-validation" novalidate>
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
                                        <div class="password-requirements small text-muted mt-1">
                                            Password must contain:
                                            <ul class="ps-3 mb-0">
                                                <li id="length">At least 8 characters</li>
                                                <li id="uppercase">One uppercase letter</li>
                                                <li id="lowercase">One lowercase letter</li>
                                                <li id="number">One number</li>
                                            </ul>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <?php echo showError('password'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control form-control-lg" required>
                                        <label class="form-label" for="confirmpassword">Confirm Password</label>
                                        <div id="password-match" class="small mt-1"></div>
                                        <?php echo showError('confirmpassword'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                                <a href="../auth/login.php" class="btn btn-link">Already have an account? Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Password validation and strength meter
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById(req);
            if (requirements[req]) {
                element.classList.add('text-success');
                element.classList.remove('text-muted');
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
            }
        });

        // Calculate password strength
        let strength = 0;
        if (requirements.length) strength += 25;
        if (requirements.uppercase) strength += 25;
        if (requirements.lowercase) strength += 25;
        if (requirements.number) strength += 25;

        const strengthBar = document.getElementById('password-strength');
        strengthBar.style.width = strength + '%';
        
        if (strength <= 25) {
            strengthBar.className = 'progress-bar bg-danger';
        } else if (strength <= 50) {
            strengthBar.className = 'progress-bar bg-warning';
        } else if (strength <= 75) {
            strengthBar.className = 'progress-bar bg-info';
        } else {
            strengthBar.className = 'progress-bar bg-success';
        }
    });

    // Password match validation
    document.getElementById('confirmpassword').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;
        const matchIndicator = document.getElementById('password-match');
        
        if (confirm === '') {
            matchIndicator.textContent = '';
            matchIndicator.className = 'small mt-1';
        } else if (password === confirm) {
            matchIndicator.textContent = 'Passwords match';
            matchIndicator.className = 'small mt-1 text-success';
            this.setCustomValidity('');
        } else {
            matchIndicator.textContent = 'Passwords do not match';
            matchIndicator.className = 'small mt-1 text-danger';
            this.setCustomValidity('Passwords do not match');
        }
    });

    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
    <?php clearMessages(); ?>
</body>
</html>