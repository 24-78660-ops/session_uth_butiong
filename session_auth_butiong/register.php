<?php
require_once __DIR__ . '/db.php';


if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}


$error = '';
$username = '';
$email = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';


    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Your form expired. Please try again.';
    } elseif ($username === '' || strlen($username) > 80) {
        $error = 'Enter a username (up to 80 characters).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);


        if ($check->fetch()) {
            $error = 'That username or email is already taken.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
            $insert->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);


            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'       => (int) $pdo->lastInsertId(),
                'username' => $username,
                'email'    => $email,
            ];
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
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
        <h1>Create Account</h1>
        <p class="sub">Sign up to get started</p>


        <?php if ($error !== ''): ?>
            <p class="error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>


        <form method="post" action="register.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">


            <label for="username">Username</label>
            <input id="username" name="username" type="text" maxlength="80" autocomplete="username" value="<?= e($username) ?>" required>


            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required>


            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Create a password" minlength="8" autocomplete="new-password" required>


            <button type="submit">Register</button>
        </form>


        <p class="link">Already have an account? <a href="index.php">Sign in</a></p>
    </main>
</body>
</html>