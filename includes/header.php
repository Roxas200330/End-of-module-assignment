<?php
// top of every page, set $pageTitle before including
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Recipes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<!-- viewport needed for mobile layout -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> - Recipe Web App</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- skip link for keyboard users -->
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <a class="logo" href="index.php">Recipe Web App</a>
    <!-- hamburger button, only shows on mobile, handled in main.js -->
    <button class="menu-toggle" id="menuToggle" aria-label="Open menu" aria-expanded="false">&#9776;</button>
    <nav class="site-nav" id="siteNav" aria-label="Main navigation">
        <a href="index.php">Recipes</a>
        <!-- different links depending on login state -->
        <?php if (isLoggedIn()): ?>
            <a href="account.php">My account</a>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main id="main" class="container">
