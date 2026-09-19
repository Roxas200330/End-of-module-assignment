<?php
// single recipe page
require_once 'includes/functions.php';

// id from url, int cast so its always a number
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM recipes WHERE id = ?');
$st->execute([$id]);
$recipe = $st->fetch();
if (!$recipe) {
    // bad id, show a simple 404
    http_response_code(404);
    $pageTitle = 'Not found';
    require 'includes/header.php';
    echo '<h1>Recipe not found</h1><p><a href="index.php">Back to recipes</a></p>';
    require 'includes/footer.php';
    exit;
}

$pageTitle = $recipe['title'];
require 'includes/header.php';

// categories via the link table
$st = $pdo->prepare('SELECT c.name FROM categories c
                     JOIN recipe_categories rc ON rc.category_id = c.id
                     WHERE rc.recipe_id = ? ORDER BY c.name');
$st->execute([$id]);
$cats = $st->fetchAll(PDO::FETCH_COLUMN);

// ingredients in the order they were added
$st = $pdo->prepare('SELECT * FROM ingredients WHERE recipe_id = ? ORDER BY id');
$st->execute([$id]);
$ingredients = $st->fetchAll();

// steps in order
$st = $pdo->prepare('SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_no');
$st->execute([$id]);
$steps = $st->fetchAll();

// average rating and how many
$st = $pdo->prepare('SELECT ROUND(AVG(stars),1) AS avg_rating, COUNT(*) AS n FROM ratings WHERE recipe_id = ?');
$st->execute([$id]);
$rating = $st->fetch();

// this users own rating so the dropdown shows it
$myRating = null;
if (isLoggedIn()) {
    $st = $pdo->prepare('SELECT stars FROM ratings WHERE recipe_id = ? AND user_id = ?');
    $st->execute([$id, $_SESSION['user_id']]);
    $myRating = $st->fetchColumn();
}
$total = $recipe['prep_time'] + $recipe['cook_time'];
?>

<article class="recipe">
    <img class="recipe-hero" src="<?= e(recipeImage($recipe)) ?>" alt="<?= e($recipe['title']) ?>">
    <h1><?= e($recipe['title']) ?></h1>
    <p class="meta">
        <?= e(implode(', ', $cats)) ?> &middot; Serves <?= $recipe['servings'] ?> &middot;
        Prep <?= $recipe['prep_time'] ?> min &middot; Cook <?= $recipe['cook_time'] ?> min &middot;
        Total <?= $total ?> min &middot; <?= ucfirst($recipe['difficulty']) ?>
    </p>
    <p class="meta">
        Rating: <?= $rating['n'] ? $rating['avg_rating'] . ' / 5 (' . $rating['n'] . ' ratings)' : 'Not rated yet' ?>
    </p>
    <p><?= e($recipe['description']) ?></p>

    <!-- message from rate.php after saving -->
    <?php if (isset($_GET['msg'])): ?>
        <p class="flash"><?= e($_GET['msg']) ?></p>
    <?php endif; ?>

    <!-- favourite + rating only for logged in users -->
    <?php if (isLoggedIn()): ?>
        <div class="recipe-actions">
            <!-- post so the favourite toggle isnt triggered by just visiting a url -->
            <form method="post" action="favourite.php">
                <input type="hidden" name="recipe_id" value="<?= $id ?>">
                <button type="submit" class="btn">
                    <?= isFavourite($id) ? 'Remove from favourites' : 'Save to favourites' ?>
                </button>
            </form>
            <form method="post" action="rate.php" class="rate-form" id="rateForm">
                <input type="hidden" name="recipe_id" value="<?= $id ?>">
                <label for="stars">Your rating</label>
                <select id="stars" name="stars" required>
                    <option value="">Choose</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= $myRating == $i ? 'selected' : '' ?>><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn">Rate</button>
            </form>
        </div>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to save or rate this recipe.</p>
    <?php endif; ?>

    <h2>Ingredients</h2>
    <ul class="ingredients">
        <!-- quantity and unit can be blank e.g. "salt and pepper" so trim the space -->
        <?php foreach ($ingredients as $ing): ?>
            <li><?= e(trim($ing['quantity'] . ' ' . $ing['unit'])) ?> <?= e($ing['name']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Method</h2>
    <ol class="steps">
        <?php foreach ($steps as $s): ?>
            <li>
                <p><?= e($s['instruction']) ?></p>
                <span class="step-time"><?= $s['minutes'] ?> min</span>
            </li>
        <?php endforeach; ?>
    </ol>
</article>

<?php require 'includes/footer.php'; ?>
