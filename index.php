<?php
// home page, search + list recipes
$pageTitle = 'Recipes';
require_once 'includes/header.php';

// get search values from the url, empty if not set
// cast numbers to int so nothing odd gets into the query
$q          = trim($_GET['q'] ?? '');
$category   = (int)($_GET['category'] ?? 0);
$difficulty = $_GET['difficulty'] ?? '';
$maxTime    = (int)($_GET['max_time'] ?? 0);
$sort       = $_GET['sort'] ?? 'title';

// build the query, avg rating joined in for sorting
// left join so recipes with no ratings still show
$sql = 'SELECT r.*, (r.prep_time + r.cook_time) AS total_time,
               ROUND(AVG(ra.stars), 1) AS avg_rating,
               COUNT(ra.id) AS rating_count
        FROM recipes r
        LEFT JOIN ratings ra ON ra.recipe_id = r.id';
$where  = [];   // where clauses added below
$params = [];   // values for the ? placeholders, same order

if ($q !== '') {
    // keyword matches title, description or ingredient
    $where[] = '(r.title LIKE ? OR r.description LIKE ?
                 OR r.id IN (SELECT recipe_id FROM ingredients WHERE name LIKE ?))';
    $like = "%$q%";
    array_push($params, $like, $like, $like);
}
if ($category > 0) {
    // recipe can have many categories so use the link table
    $where[] = 'r.id IN (SELECT recipe_id FROM recipe_categories WHERE category_id = ?)';
    $params[] = $category;
}
if (in_array($difficulty, ['easy', 'medium', 'hard'])) {
    $where[] = 'r.difficulty = ?';
    $params[] = $difficulty;
}
if ($maxTime > 0) {
    $where[] = '(r.prep_time + r.cook_time) <= ?';
    $params[] = $maxTime;
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
// group by needed because of the avg
$sql .= ' GROUP BY r.id';

// sort options, only these values allowed
// cant use a placeholder in order by so use a lookup array instead
$sortOptions = [
    'title'      => 'r.title ASC',
    'time_asc'   => 'total_time ASC',
    'time_desc'  => 'total_time DESC',
    'rating'     => 'avg_rating DESC, rating_count DESC',
    'newest'     => 'r.created_at DESC',
];
if (!isset($sortOptions[$sort])) $sort = 'title';
$sql .= ' ORDER BY ' . $sortOptions[$sort];

$st = $pdo->prepare($sql);
$st->execute($params);
$recipes = $st->fetchAll();
?>

<!-- two column layout, filters left and results right, stacks on mobile -->
<div class="search-layout">
<aside class="filters">
<h1>Find a recipe</h1>

<!-- get form so the search is in the url and can be bookmarked -->
<form method="get" action="index.php" class="search-form" id="searchForm">
    <div class="field">
        <label for="q">Keyword (name or ingredient)</label>
        <input type="text" id="q" name="q" value="<?= e($q) ?>" placeholder="e.g. mushroom">
    </div>
    <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="0">Any</option>
            <!-- categories from db, keep the chosen one selected after search -->
            <?php foreach (getCategories() as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $category ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="difficulty">Difficulty</label>
        <select id="difficulty" name="difficulty">
            <option value="">Any</option>
            <?php foreach (['easy', 'medium', 'hard'] as $d): ?>
                <option value="<?= $d ?>" <?= $d === $difficulty ? 'selected' : '' ?>><?= ucfirst($d) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="max_time">Max total time (minutes)</label>
        <input type="number" id="max_time" name="max_time" min="1" max="600" value="<?= $maxTime ?: '' ?>">
    </div>
    <div class="field">
        <label for="sort">Sort by</label>
        <select id="sort" name="sort">
            <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Name (A-Z)</option>
            <option value="time_asc" <?= $sort === 'time_asc' ? 'selected' : '' ?>>Quickest first</option>
            <option value="time_desc" <?= $sort === 'time_desc' ? 'selected' : '' ?>>Longest first</option>
            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Highest rated</option>
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
        </select>
    </div>
    <div class="field actions">
        <button type="submit" class="btn">Search</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </div>
</form>
</aside>

<section class="results">
<p class="result-count"><?= count($recipes) ?> recipe<?= count($recipes) === 1 ? '' : 's' ?> found</p>

<?php if (!$recipes): ?>
    <p>No recipes match. Try fewer filters.</p>
<?php endif; ?>

<!-- one row per recipe -->
<ul class="recipe-list">
<?php foreach ($recipes as $r): ?>
    <li class="recipe-row">
        <img src="<?= e(recipeImage($r)) ?>" alt="">
        <h2><a href="recipe.php?id=<?= $r['id'] ?>"><?= e($r['title']) ?></a></h2>
        <span class="col"><?= $r['total_time'] ?> min</span>
        <span class="col"><?= ucfirst($r['difficulty']) ?></span>
        <span class="col rating">
            <?= $r['avg_rating'] ? '&#9733; ' . $r['avg_rating'] . ' (' . $r['rating_count'] . ')' : 'Not rated' ?>
        </span>
    </li>
<?php endforeach; ?>
</ul>
</section>
</div>

<?php require_once 'includes/footer.php'; ?>
