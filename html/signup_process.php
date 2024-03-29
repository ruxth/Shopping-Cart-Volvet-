<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $dob = $_POST['date_of_birth']; 

    // Check if the email is already registered
    $check_existing_email_sql = "SELECT email FROM users WHERE email = ?";
    $stmt_check_email = $conn->prepare($check_existing_email_sql);
    $stmt_check_email->bind_param('s', $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {

        echo '<script>alert("You already have an account with us! Please log in!");</script>';
        echo '<script>window.location.href="login.php";</script>';
        exit();
    }


    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    if ($conn) {
        $sql = "INSERT INTO users (email, password, dob) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);


        $stmt->bind_param('sss', $email, $hashedPassword, $dob);

        if ($stmt->execute()) {
            // Registration successful
            echo '<script>alert("You have successfully registered! Please login!"); window.location.href = "login.php";</script>';
            exit();
        
        } else {

            header('Location: signup.php?error=registrationfailed');
            exit();
        }
    } else {

        header('Location: signup.php?error=dbconnectionfailed');
        exit();
    }
} else {

    header('Location: signup.php');
    exit();
}
?>
