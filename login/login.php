
<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
        }

        .login-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer p {
            margin: 0 0 1rem 0;
            color: #4a5568;
        }

        .register-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .register-links a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .register-links a:hover {
            background: #fff7ed;
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
    </style>
</head>

<body>
    <a href="../index.php" class="back-home">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
                <p>Login to your Eventify account</p>
            </div>

            <div class="login-body">
                <form method="POST" action="" id="login-form">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p>Don't have an account?</p>
                <div class="register-links">
                    <a href="register_customer.php">
                        <i class="fas fa-user"></i> Register as Customer
                    </a>
                    <a href="register_business.php">
                        <i class="fas fa-briefcase"></i> Register as Vendor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js?v=<?php echo time(); ?>"></script>

    
</body>

</html>
