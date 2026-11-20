<?php
session_start();

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '4000';
$db   = getenv('DB_DATABASE') ?: 'test';
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
];

$message = '';
$dbStatus = 'Disconnected';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $dbStatus = 'Connected to Cloud MySQL!';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($action === 'signup' && $username && $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed]);
            $message = "Account created for '$username'! You can now log in.";
        } elseif ($action === 'login') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['username'];
            } else {
                $message = "Invalid username or password.";
            }
        } elseif ($action === 'logout') {
            session_destroy();
            header("Location: /");
            exit;
        }
    }
} catch (PDOException $e) {
    $dbStatus = 'DB Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Test ni siya choi</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #f8fafc; display: flex; justify-content: center; padding-top: 50px; }
        .card { background: #1e293b; padding: 25px; border-radius: 12px; width: 340px; border: 1px solid #334155; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #475569; box-sizing: border-box; }
        input { background: #0f172a; color: white; }
        button { background: #3b82f6; color: white; font-weight: bold; border: none; cursor: pointer; }
        button.green { background: #10b981; }
        .status { font-size: 13px; color: #38bdf8; margin-bottom: 15px; }
        .msg { background: #334155; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="card">
    <div class="status">Status: <strong><?= htmlspecialchars($dbStatus) ?></strong></div>
    <?php if ($message): ?><div class="msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <?php if (isset($_SESSION['user'])): ?>
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
        <p>Your login was verified against the live MySQL database.</p>
        <form method="POST">
            <input type="hidden" name="action" value="logout">
            <button type="submit">Log Out</button>
        </form>
    <?php else: ?>
        <h3>Sign Up</h3>
        <form method="POST">
            <input type="hidden" name="action" value="signup">
            <input type="text" name="username" placeholder="New Username" required>
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit" class="green">Create Account</button>
        </form>
        <hr style="border-color: #334155; margin: 20px 0;">
        <h3>Log In</h3>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
