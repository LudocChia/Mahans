<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (isset($_SESSION['user_id'])) {
    header('Location: /mips/');
    exit();
}

$email = '';
$product_id = $_GET['pid'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitle = "Login Page - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <main class="login-form">
        <div class="container">
            <div class="wrapper">
                <div class="logo-container">
                    <img src="/mips/images/MIPS_logo.png" alt="MIPS_Logo">
                </div>
                <div class="title">
                    <h1>MIPS System</h1>
                </div>
                <div id="alert-container"></div>
                <form id="login-form-ajax" method="POST">
                    <!-- Hidden input for user_type -->
                    <input type="hidden" name="user_type" value="parent">
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <p>Please enter your email</p>
                    </div>
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <p>Please enter your password</p>
                    </div>
                    <div class="controls">
                        <button type="submit" class="btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form-ajax');

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                const userType = 'parent'; // Set the user type
                const productId = "<?php echo $product_id; ?>";
                const currentPage = "<?php echo $currentPage; ?>";

                fetch('/mips/php/ajax.php?action=login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            email: email,
                            password: password,
                            user_type: userType,
                            pid: productId,
                            current_page: currentPage
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            showAlert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred while processing the request.');
                    });
            });

            function showAlert(message) {
                const alertHtml = `<div class="mini-alert">${message}</div>`;
                document.getElementById('alert-container').innerHTML = alertHtml;
                setTimeout(function() {
                    const alertElement = document.querySelector('.mini-alert');
                    if (alertElement) {
                        alertElement.style.opacity = '0';
                        setTimeout(() => alertElement.remove(), 600);
                    }
                }, 3000);
            }
        });
    </script>
</body>

</html>