<?php
declare(strict_types=1);
require 'db.php';
 
$recipes = $pdo->query(
    'SELECT recipe_id, title, total_mins, difficulty FROM recipes ORDER BY title'
)->fetchAll();
 
$counts = $pdo->query(
    'SELECT
        (SELECT COUNT(*) FROM users)       AS users,
        (SELECT COUNT(*) FROM categories)  AS categories,
        (SELECT COUNT(*) FROM ingredients) AS ingredients,
        (SELECT COUNT(*) FROM steps)       AS steps,
        (SELECT COUNT(*) FROM ratings)     AS ratings,
        (SELECT COUNT(*) FROM favourites)  AS favourites'
)->fetch();
 
function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB connection test</title>
</head>
<body>
    <h1>Connection OK</h1>
 
    <p>Server: <?= e($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)) ?></p>
 
    <h2>Recipes (<?= count($recipes) ?>)</h2>
    <ul>
        <?php foreach ($recipes as $r): ?>
            <li>
                <?= e($r['title']) ?>
                &mdash; <?= (int) $r['total_mins'] ?> mins,
                <?= e($r['difficulty']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
 
    <h2>Row counts</h2>
    <ul>
        <?php foreach ($counts as $table => $n): ?>
            <li><?= e($table) ?>: <?= (int) $n ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
