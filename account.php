<?php
// account page, user details + favourites
$pageTitle = 'My account';
require_once 'includes/functions.php';
requireLogin();

// current user from the session id
$st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$st->execute([$_SESSION['user_id']]);
$user = $st->fetch();

// favourites joined to recipes, newest saved first
$st = $pdo->prepare('SELECT r.*, (r.prep_time + r.cook_time) AS total_time
                     FROM recipes r
                     JOIN favourites f ON f.recipe_id = r.id
                     WHERE f.user_id = ?
                     ORDER BY f.created_at DESC');
$st->execute([$user['id']]);
$favourites = $st->fetchAll();

require 'includes/header.php';
?>

<h1>My account</h1>
<p><strong>Name:</strong> <?= e($user['name']) ?><br>
   <strong>Email:</strong> <?= e($user['email']) ?><br>
   <strong>Member since:</strong> <?= date('j M Y', strtotime($user['created_at'])) ?></p>

<h2>My favourite recipes</h2>

<?php if (!$favourites): ?>
    <p>You have no favourites yet. <a href="index.php">Browse recipes</a> and save some.</p>
<?php endif; ?>

<!-- same row layout as the home page -->
<ul class="recipe-list">
<?php foreach ($favourites as $r): ?>
    <li class="recipe-row">
        <img src="<?= e(recipeImage($r)) ?>" alt="">
        <h3><a href="recipe.php?id=<?= $r['id'] ?>"><?= e($r['title']) ?></a></h3>
        <span class="col"><?= $r['total_time'] ?> min</span>
        <span class="col"><?= ucfirst($r['difficulty']) ?></span>
        <!-- back field tells favourite.php to return here not the recipe page -->
        <form method="post" action="favourite.php">
            <input type="hidden" name="recipe_id" value="<?= $r['id'] ?>">
            <input type="hidden" name="back" value="account.php">
            <button type="submit" class="btn btn-secondary">Remove</button>
        </form>
    </li>
<?php endforeach; ?>
</ul>

<?php require 'includes/footer.php'; ?>
