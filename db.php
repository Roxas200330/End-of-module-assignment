<?php
$connection = mysqli_connect("localhost", "root", "", "recipe_app");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=recipe_app", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$results = [];
$search_term = '';

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_term = trim($_GET['query']);

    $sql = "SELECT * FROM recipes WHERE name LIKE :keyword OR category LIKE :keyword OR name LIKE :keyword OR id LIKE :keyword";
    $stmt = $pdo->prepare($sql);

    $wildcard = '%' . $search_term . '%';
    $stmt->bindValue(':keyword', $wildcard, PDO::PARAM_STR);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!empty($search_term) > 0) {
    echo "<h2>Search Results for: " . htmlspecialchars($search_term) . "</h2>";
    if (!empty($results)) {
        foreach ($results as $recipe) {
            echo "<div>";
            echo "<h3>" . htmlspecialchars($recipe['id']) . "</h3>";
            echo "<p>Category: " . htmlspecialchars($recipe['name']) . "</p>";
            echo "<p>Ingredients: " . htmlspecialchars($recipe['category']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No results found for: " . htmlspecialchars($search_term) . "</p>";
    }
}
