<?php
// add or remove favourite then go back to previous page
require_once 'includes/functions.php';
requireLogin();

$recipeId = (int)($_POST['recipe_id'] ?? 0);
// only allow going back to account page or the recipe, not any url
$back = ($_POST['back'] ?? '') === 'account.php' ? 'account.php' : "recipe.php?id=$recipeId";

if ($recipeId > 0) {
    // toggle, delete if already saved otherwise insert
    if (isFavourite($recipeId)) {
        $st = $pdo->prepare('DELETE FROM favourites WHERE user_id = ? AND recipe_id = ?');
    } else {
        $st = $pdo->prepare('INSERT INTO favourites (user_id, recipe_id) VALUES (?, ?)');
    }
    $st->execute([$_SESSION['user_id'], $recipeId]);
}
header("Location: $back");
exit;
