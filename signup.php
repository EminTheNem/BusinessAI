<?php
session_start();

include("connection.php");
include("functions.php");

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est déjà connecté
$user_data = check_login($con);
if($user_data) {
    header("Location: ozark_dashboard.php");
    die;
}

$error_message = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $user_name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if(!empty($user_name) && !empty($password) && !empty($email) && !is_numeric($user_name)) {
        // Vérifier si les mots de passe correspondent
        if ($password !== $confirm_password) {
            $error_message = "Les mots de passe ne correspondent pas!";
        } else {
            // Vérifier si l'email existe déjà
            $check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
            $check_result = mysqli_query($con, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $error_message = "Cet email est déjà utilisé!";
            } else {
                // Hacher le mot de passe et sauvegarder
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $user_id = random_num(20);
                $query = "INSERT INTO users (user_id, user_name, password, email) VALUES ('$user_id', '$user_name', '$hashed_password', '$email')";
                
                if(mysqli_query($con, $query)) {
    // After successful registration, log the user in automatically
    $_SESSION['user_id'] = $user_id;
    header("Location: ozark_dashboard.php");
    die;
} else {
    $error_message = "Erreur lors de l'inscription: " . mysqli_error($con);
}
            }
        }
    } else {
        $error_message = "Veuillez saisir des informations valides!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - BusinessAI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Votre CSS existant ici */
        :root {
            --primary-color: #4a6bff;
            --primary-hover: #3a5bef;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
            --text-light: #ffffff;
            --text-muted: #aaaaaa;
            --input-bg: #2d2d2d;
            --error-color: #ff4d4d;
            --success-color: #4caf50;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: radial-gradient(circle at 10% 20%, rgba(74, 107, 255, 0.1) 0%, transparent 20%);
        }

        .container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 100%;
            max-width: 420px;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 5px 0;
            border: 1px solid #333;
            border-radius: 8px;
            background-color: var(--input-bg);
            color: var(--text-light);
            font-size: 1rem;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 107, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: none;
            border-radius: 8px;
            background-color: var(--primary-color);
            color: var(--text-light);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        button:active {
            transform: scale(0.98);
        }

        .top-right {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .top-right button {
            background-color: transparent;
            color: var(--text-light);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            border: 1px solid var(--text-muted);
            width: auto;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .top-right button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--text-light);
        }

        .loader {
            display: none;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-top: 3px solid var(--text-light);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toggle-form {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .toggle-form a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .toggle-form a:hover {
            text-decoration: underline;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        .success-message {
            color: var(--success-color);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #333;
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--input-bg);
            color: var(--text-light);
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .social-btn:hover {
            background-color: #333;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
        }
        
        .server-error {
            color: var(--error-color);
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-align: center;
            display: <?php echo !empty($error_message) ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="top-right">
        <button onclick="exitToHomepage()"><i class="fas fa-times"></i> Exit</button>
    </div>

    <div class="container">
        <h1>Sign Up</h1>
        
        <?php if(!empty($error_message)): ?>
            <div class="server-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" id="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                <div class="error-message" id="username-error"></div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <div class="error-message" id="email-error"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye password-toggle" id="toggle-password"></i>
                </div>
                <div class="error-message" id="password-error"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                    <i class="fas fa-eye password-toggle" id="toggle-confirm-password"></i>
                </div>
                <div class="error-message" id="confirm-password-error"></div>
            </div>
            
            <button type="submit" id="submitBtn">
                <span id="button-text">Sign Up</span>
            </button>
            <div class="loader" id="loader"></div>
            
            <div class="success-message" id="success-message"></div>
        </form>

        <div class="divider">OR</div>
        
        <div class="social-login">
            <button class="social-btn" title="Sign up with Google">
                <i class="fab fa-google"></i>
            </button>
            <button class="social-btn" title="Sign up with Facebook">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="social-btn" title="Sign up with Twitter">
                <i class="fab fa-twitter"></i>
            </button>
        </div>

        <div class="toggle-form">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        function exitToHomepage() {
            window.location.href = "index.php";
        }

        // Empêcher la soumission du formulaire si validation échoue
        document.getElementById('auth-form').addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault();
            } else {
                // Show loader
                document.getElementById("submitBtn").style.display = "none";
                document.getElementById("loader").style.display = "block";
            }
        });

        function validateForm() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            // Clear previous errors
            document.getElementById('username-error').style.display = 'none';
            document.getElementById('email-error').style.display = 'none';
            document.getElementById('password-error').style.display = 'none';
            document.getElementById('confirm-password-error').style.display = 'none';
            
            // Simple validation
            let isValid = true;
            
            if (username.trim().length < 2) {
                document.getElementById('username-error').textContent = 'Please enter a valid username (min 2 characters)';
                document.getElementById('username-error').style.display = 'block';
                isValid = false;
            }
            
            if (!email.includes('@') || !email.includes('.')) {
                document.getElementById('email-error').textContent = 'Please enter a valid email address';
                document.getElementById('email-error').style.display = 'block';
                isValid = false;
            }
            
            if (password.length < 6) {
                document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
                document.getElementById('password-error').style.display = 'block';
                isValid = false;
            }
            
            if (password !== confirmPassword) {
                document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
                document.getElementById('confirm-password-error').style.display = 'block';
                isValid = false;
            }
            
            return isValid;
        }

        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Toggle confirm password visibility
        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirm-password');
            const icon = this;
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Add animation to social buttons on hover
        const socialBtns = document.querySelectorAll('.social-btn');
        socialBtns.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                const icon = btn.querySelector('i');
                icon.style.transform = 'scale(1.2)';
            });
            
            btn.addEventListener('mouseleave', () => {
                const icon = btn.querySelector('i');
                icon.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>