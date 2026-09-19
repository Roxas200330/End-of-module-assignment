<?php
// shared helpers, included by every page
session_start();
require_once __DIR__ . '/../config/db.php';

// escape strings before printing to stop xss
function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// user id is put in the session on login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// send to login page if not logged in, used on account/favourite/rate
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// check if current user has saved this recipe
function isFavourite($recipeId) {
    global $pdo;
    if (!isLoggedIn()) return false;
    $st = $pdo->prepare('SELECT 1 FROM favourites WHERE user_id = ? AND recipe_id = ?');
    $st->execute([$_SESSION['user_id'], $recipeId]);
    return (bool)$st->fetch();
}

// all categories for the dropdown
function getCategories() {
    global $pdo;
    return $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

// get image for recipe, use placeholder if file not there
// image name is the slug so no need to store a path in the db
function recipeImage($recipe) {
    $path = 'images/recipes/' . $recipe['slug'] . '.jpg';
    return file_exists(__DIR__ . '/../' . $path) ? $path : 'images/placeholder.jpg';
}
