<?php
// register page
$pageTitle = 'Register';
require_once 'includes/functions.php';
// already logged in, no need to register
if (isLoggedIn()) { header('Location: account.php'); exit; }

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // check inputs before inserting, same rules as main.js
    if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        // email is unique so check first for a nicer message
        $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            $errors[] = 'That email is already registered.';
        } else {
            // password_hash so plain text is never stored
            $st = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $st->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            // log them straight in
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: account.php');
            exit;
        }
    }
}
require 'includes/header.php';
?>

<h1>Create an account</h1>

<!-- server side errors -->
<?php if ($errors): ?>
    <ul class="errors">
        <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- novalidate so our own js messages show instead of the browser ones -->
<form method="post" action="register.php" class="auth-form" id="registerForm" novalidate>
    <div class="field">
        <label for="name">Name</label>
        <!-- value kept so the form isnt wiped on error -->
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required minlength="2">
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
    </div>
    <div class="field">
        <label for="password">Password (min 6 characters)</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>
    <div class="field">
        <label for="confirm">Confirm password</label>
        <input type="password" id="confirm" name="confirm" required>
    </div>
    <!-- js puts client side errors here -->
    <p class="js-error" id="formError" role="alert"></p>
    <button type="submit" class="btn">Register</button>
</form>
<p>Already have an account? <a href="login.php">Log in</a></p>

<?php require 'includes/footer.php'; ?>
