<?php
require_once __DIR__ . '/includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/config/database.php';
$pdo = db();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both your username and password.';
    } else {
        $stmt = $pdo->prepare("
            SELECT user_id, full_name, username, password_hash, role, is_active
            FROM users
            WHERE username = :username
            LIMIT 1
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ((int)$user['is_active'] !== 1) {
                $error = 'Your account is currently inactive. Please contact an administrator.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];

                header("Location: dashboard.php");
                exit;
            }
        } else {
            $error = 'Invalid username and password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login | Barangay Surong Guisad DRRM</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">

    <?php require __DIR__ . '/includes/public-header.php'; ?>

    <main class="login-main">
        <div class="login-card">
            <div class="login-eyebrow">Barangay Surong Guisad · Staff Access</div>
            <h2>DRRM Staff Portal</h2>
            <p class="login-desc">
                Sign in with your authorized barangay or BDRRMC credentials to manage incidents, evacuation centers, and relief operations.
            </p>

            <?php if ($error): ?>
                <div class="error-msg">
                    <strong>Login failed:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">Sign In</button>
                <a href="index.php" class="back-link">Back to Public Portal</a>
            </form>
        </div>
    </main>

    <?php require __DIR__ . '/includes/public-footer.php'; ?>

</body>
</html>
