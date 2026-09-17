DROP DATABASE IF EXISTS recipe_app;
CREATE DATABASE recipe_app CHARACTER SET utf8mb4;
USE recipe_app;
 
CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50) NOT NULL UNIQUE
);
 
CREATE TABLE recipes (
    recipe_id   INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    total_mins  INT NOT NULL DEFAULT 0,
    servings    INT NOT NULL DEFAULT 4,
    difficulty  ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    cuisine     VARCHAR(50),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE TABLE recipe_categories (
    recipe_id   INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id)   REFERENCES recipes(recipe_id)      ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);
 
CREATE TABLE ingredients (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id     INT NOT NULL,
    name          VARCHAR(100) NOT NULL,
    quantity      VARCHAR(50),
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
 
CREATE TABLE steps (
    step_id       INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id     INT NOT NULL,
    step_no       INT NOT NULL,
    instruction   TEXT NOT NULL,
    duration_mins INT NOT NULL DEFAULT 0,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
 
CREATE TABLE ratings (
    rating_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    rating     INT NOT NULL,
    comment    VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, recipe_id),
    FOREIGN KEY (user_id)   REFERENCES users(user_id)     ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
 
CREATE TABLE favourites (
    user_id   INT NOT NULL,
    recipe_id INT NOT NULL,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id)   REFERENCES users(user_id)     ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
