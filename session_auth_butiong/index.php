<?php
require_once __DIR__ . '/db.php';


if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}


$error = '';
$username = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Your form expired. Please try again.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, strtolower($username)]);
        $user = $stmt->fetch();


        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'       => (int) $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
            ];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            color: #222;
            background: #fff;
        }
        .box { width: 100%; max-width: 360px; }
        h1 { margin: 0; text-align: center; color: #1b7a3a; font-size: 1.8rem; }
        .sub { margin: 4px 0 24px; text-align: center; color: #777; }
        label { display: block; margin: 14px 0 6px; font-size: 0.85rem; font-weight: bold; color: #1b7a3a; }
        input {
            width: 100%;
            padding: 10px 12px;
            font: inherit;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input:focus { outline: none; border-color: #28a745; }
        button {
            width: 100%;
            margin-top: 22px;
            padding: 11px;
            font: inherit;
            font-weight: bold;
            color: #fff;
            background: #28a745;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #1e8a38; }
        .error { padding: 10px; color: #a12a2a; background: #fdecec; border-radius: 6px; font-size: 0.9rem; }
        .link { margin-top: 18px; text-align: center; font-size: 0.9rem; color: #777; }
        .link a { color: #1b7a3a; }
    </style>
</head>
<body>
    <main class="box">
        <h1>Sign In</h1>
        <p class="sub">Sign in with your regular account</p>


        <?php if ($error !== ''): ?>
            <p class="error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>


        <form method="post" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">


            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" value="<?= e($username) ?>" required>


            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>


            <button type="submit">Login</button>
        </form>


        <p class="link">Don't have an account? <a href="register.php">Register</a></p>
    </main>
</body>
</html>