<?php
session_start();
require_once(__DIR__ . '/../../includes/functions.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
                        // Debug information
                        if (isset($_SESSION['debug'])) {
                            echo '<div class="alert alert-info">';
                            echo nl2br(htmlspecialchars($_SESSION['debug']));
                            echo '</div>';
                            unset($_SESSION['debug']);
                        }
                        
                        echo showGeneralError();
                        echo showSuccess();
                        ?>

                        <form action="../../public/auth/login.php" method="post" class="needs-validation" novalidate>
                            <!-- CSRF Protection -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            
                            <div class="mb-4">
                                <div class="form-outline">
                                    <input type="email" name="email" id="email" class="form-control form-control-lg" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
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
                                <a href="../auth/register.php" class="btn btn-link">Don't have an account? Register</a>
                                <br>
                                <a href="../auth/forgot-password.php" class="btn btn-link">Forgot Password?</a>
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