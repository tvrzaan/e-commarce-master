<?php
session_start();
require_once(__DIR__ . '/../../includes/functions.php');
if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Login</h2>
                        
                        <?php 
                        echo showGeneralError();
                        echo showSuccess();
                        ?>

                        <form action="/e-commarce-master/public/auth/login.php" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <div class="form-outline">
                                    <input type="email" name="email" id="email" class="form-control form-control-lg" required>
                                    <label class="form-label" for="email">Email</label>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-outline">
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" required>
                                    <label class="form-label" for="password">Password</label>
                                    <div class="invalid-feedback">
                                        Please enter your password
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="../register.php" class="btn btn-link">Don't have an account? Register</a>
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
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
    <?php clearMessages(); ?>
</body>
</html> 