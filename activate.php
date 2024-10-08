<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'parent') {
    header('Location: /mips/login.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /mips/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userType = 'parent';
$table = 'Parent';

$pageTitle = "Activate Account - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body>
    <main class="set-new-password-form">
        <div class="container">
            <div class="wrapper">
                <div class="logo-container">
                    <img src="/mips/images/MIPS_logo.png" alt="MIPS_Logo">
                </div>
                <div class="title">
                    <h1>Activate Account</h1>
                </div>
                <div id="alert-container"></div>
                <form method="POST" id="activate-form">
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" name="user_name" placeholder="Parent Name" required>
                        </div>
                        <p>Please enter your name as on your Identification Card (IC)</p>
                    </div>
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
                        </div>
                        <p>Please enter your new password</p>
                        <p>&bull; At least one number and one special symbol</p>
                        <p>&bull; Must be at least 6 characters long</p>
                        <p>&bull; Cannot begin or end with a space</p>
                    </div>
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <p>Please confirm your new password</p>
                    </div>
                    <div class="controls">
                        <button type="submit" name="submit" class="btn">Activate</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.querySelector('#activate-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const userName = document.querySelector('#name').value;
            const newPassword = document.querySelector('#new_password').value;
            const confirmPassword = document.querySelector('#confirm_password').value;

            fetch('/mips/php/ajax.php?action=activate_account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        user_name: userName,
                        new_password: newPassword,
                        confirm_password: confirmPassword
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
    </script>
</body>

</html>