<?php
// login page
$pageTitle = 'Log in';
require_once 'includes/functions.php';
if (isLoggedIn()) { header('Location: account.php'); exit; }

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $user = $st->fetch();

    // password_verify checks against the hash from register
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: account.php');
        exit;
    }
    // same message for wrong email or wrong password
    $error = 'Email or password is incorrect.';
}
require 'includes/header.php';
?>

<h1>Log in</h1>

<?php if ($error): ?>
    <ul class="errors"><li><?= e($error) ?></li></ul>
<?php endif; ?>

<form method="post" action="login.php" class="auth-form" id="loginForm" novalidate>
    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <p class="js-error" id="formError" role="alert"></p>
    <button type="submit" class="btn">Log in</button>
</form>
<p>No account yet? <a href="register.php">Register</a></p>

<?php require 'includes/footer.php'; ?>
