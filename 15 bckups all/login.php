<?php
session_start();
include("./database/connection.php");

// Handle form submission
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to fetch admin data based on email
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password and check role
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];  // Ensure role is correctly set

        // Redirect based on role
        if ($user['role'] == 'super_admin') {
            header("Location: admin/index.php");
        } elseif ($user['role'] == 'plagiarism_checker') {
            header("Location: checker/index.php");
        } else {
            $error_message = "Invalid user role.";
        }
        exit();
    } else {
        // Invalid login or password
        $error_message = "Invalid email or password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #05143b 5%, #05143b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 500px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2d3748;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #4a5568;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #80091d;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(to right, #05143b, #80091d);
            border: none;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            background: linear-gradient(to right, #80091d, #05143b);
        }

        .alert-danger {
            border-radius: 8px;
            border-left: 4px solid #f56565;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .input-group .form-control {
            border-left: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="text-center mb-4">
                        <img src="https://www.bms.ac.lk/Alumni-2024-Registration-Form/admin/img/logo4.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
                    </div>
                    <h2>Login</h2>

                    <!-- Error message -->
                    <?php if (isset($error_message)) { ?>
                        <div class="alert alert-danger" id="error-message">
                            <?php echo $error_message; ?>
                        </div>
                    <?php } ?>

                    <form action="" method="POST">
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <script>
                            const togglePassword = document.querySelector('#togglePassword');
                            const password = document.querySelector('#password');

                            togglePassword.addEventListener('click', function() {
                                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                                password.setAttribute('type', type);
                                this.querySelector('i').classList.toggle('bi-eye');
                                this.querySelector('i').classList.toggle('bi-eye-slash');
                            });
                        </script>

                        <button type="submit" class="btn btn-primary w-100" name="login">
                            Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
