<?php
// save rating, update if user already rated this one
require_once 'includes/functions.php';
requireLogin();

$recipeId = (int)($_POST['recipe_id'] ?? 0);
$stars    = (int)($_POST['stars'] ?? 0);

// server side check, must be 1 to 5
if ($recipeId > 0 && $stars >= 1 && $stars <= 5) {
    // ratings table has unique key on user + recipe so this updates instead of duplicating
    $st = $pdo->prepare('INSERT INTO ratings (user_id, recipe_id, stars) VALUES (?, ?, ?)
                         ON DUPLICATE KEY UPDATE stars = VALUES(stars)');
    $st->execute([$_SESSION['user_id'], $recipeId, $stars]);
    $msg = 'Thanks, your rating was saved.';
} else {
    $msg = 'Pick a rating between 1 and 5.';
}
// message shown on the recipe page
header('Location: recipe.php?id=' . $recipeId . '&msg=' . urlencode($msg));
exit;
