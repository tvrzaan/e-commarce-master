<?php
session_start();

// Include database connection
require_once(__DIR__ . '/../../includes/db.php');

class RegistrationValidator {
    private $errors = [];
    private $data = [];
    
    public function __construct($requestData) {
        $this->data = $requestData;
    }
    
    public function validate() {
        // Verify CSRF token
        if (!isset($this->data['csrf_token']) || 
            !hash_equals(hash_hmac('sha256', session_id(), 'secret_key'), $this->data['csrf_token'])) {
            $this->errors['general'] = "Invalid request";
            return false;
        }

        // Required fields validation
        $requiredFields = [
            'firstName' => 'First Name',
            'lastname' => 'Last Name',
            'birthday' => 'Birthday',
            'gender' => 'Gender',
            'email' => 'Email',
            'phoneNumber' => 'Phone Number',
            'address' => 'Address',
            'password' => 'Password',
            'confirmpassword' => 'Confirm Password'
        ];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($this->data[$field])) {
                $this->errors[$field] = "$label is required";
            }
        }
        
        // Email validation
        if (!empty($this->data['email'])) {
            $email = filter_var($this->data['email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = "Invalid email format";
            }
        }
        
        // Password validation
        if (!empty($this->data['password'])) {
            if (strlen($this->data['password']) < 8) {
                $this->errors['password'] = "Password must be at least 8 characters long";
            }
            if ($this->data['password'] !== $this->data['confirmpassword']) {
                $this->errors['confirmpassword'] = "Passwords do not match";
            }
        }
        
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getSanitizedData() {
        return [
            'firstName' => htmlspecialchars($this->data['firstName']),
            'lastname' => htmlspecialchars($this->data['lastname']),
            'username' => htmlspecialchars($this->data['firstName'] . ' ' . $this->data['lastname']),
            'birthday' => htmlspecialchars($this->data['birthday']),
            'gender' => htmlspecialchars($this->data['gender']),
            'email' => filter_var($this->data['email'], FILTER_SANITIZE_EMAIL),
            'phoneNumber' => htmlspecialchars($this->data['phoneNumber']),
            'address' => htmlspecialchars($this->data['address']),
            'password' => $this->data['password']
        ];
    }
}

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new RegistrationValidator($_REQUEST);
    
    if ($validator->validate()) {
        try {
            $data = $validator->getSanitizedData();
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Prepare SQL statement
            $sql = "INSERT INTO users (username, email, phone_number, address, password, gender, birthday) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", 
                $data['username'],
                $data['email'],
                $data['phoneNumber'],
                $data['address'],
                $hashedPassword,
                $data['gender'],
                $data['birthday']
            );
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login with your credentials.";
                header("Location: ../../views/auth/login.php");
                exit();
            } else {
                throw new Exception("Failed to register user");
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Registration failed: " . $th->getMessage();
            header("Location: ../../views/register.php");
            exit();
        }
    } else {
        $_SESSION['errors'] = $validator->getErrors();
        header("Location: ../../views/register.php");
        exit();
    }
}
?>