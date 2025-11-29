<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Business</title>
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
            max-width: 600px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .login-header { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; padding: 2.5rem 2rem; text-align: center; }
        .login-header h2 { margin: 0; font-size: 1.75rem; font-weight: 700; }
        .login-body { padding: 2rem; }
        .form-label { font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
        .form-control { padding: 0.8rem 1rem; border-radius: 10px; }
        .btn-login { width: 100%; padding: 0.9rem; border-radius: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; font-weight: 600; }
        .login-footer { background: #f8f9fa; padding: 1.2rem 1.5rem; text-align: center; border-top: 1px solid #e2e8f0; }
        .back-home { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; padding: 0.6rem 1rem; border-radius: 50px; background: rgba(255,255,255,0.15); }
    </style>
</head>

<body>
    <a href="../home.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-briefcase"></i> Register as a Business</h2>
                <p class="mb-0">Create your Eventify business account</p>
            </div>

            <div class="login-body">
                <form method="POST" action="" id="register-form">
                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="fa fa-user"></i> Business Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="fa fa-envelope"></i> Company Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="fa fa-lock"></i> Password</label>
                        <input type="password" autocomplete="new-password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="phone_number" class="form-label">Company Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_image" class="form-label">Business Logo</label>
                        <input type="file" class="form-control" id="user_image" name="user_image">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Register As</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="eventManager" value="1" checked>
                                <label class="form-check-label" for="eventManager">Event Manager</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="vendor" value="2">
                                <label class="form-check-label" for="vendor">Vendor</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">Register</button>
                </form>
            </div>

            <div class="login-footer">
                <p class="mb-1">Already have an account? <a href="login.php">Login here</a>.</p>
                <p class="mb-0">Are you a customer? <a href="register_customer.php">Register here</a>.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/register.js?v=<?php echo time(); ?>"></script>
</body>

</html>