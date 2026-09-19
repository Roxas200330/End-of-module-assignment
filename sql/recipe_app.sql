-- recipe_app database, tables then sample data

DROP DATABASE IF EXISTS recipe_app;
CREATE DATABASE recipe_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipe_app;

-- password stored as a hash not plain text
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- slug is the image filename, times in minutes
CREATE TABLE recipes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    slug        VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    prep_time   INT NOT NULL,
    cook_time   INT NOT NULL,
    servings    INT NOT NULL DEFAULT 4,
    difficulty  ENUM('easy','medium','hard') NOT NULL DEFAULT 'easy',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

-- link table, recipe can have many categories
CREATE TABLE recipe_categories (
    recipe_id   INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id)   REFERENCES recipes(id)    ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- quantity/unit are text so things like 'pinch' or blank work
CREATE TABLE ingredients (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    quantity  VARCHAR(20),
    unit      VARCHAR(30),
    name      VARCHAR(120) NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- step_no is the order, minutes is time for that step
CREATE TABLE steps (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id   INT NOT NULL,
    step_no     INT NOT NULL,
    instruction TEXT NOT NULL,
    minutes     INT NOT NULL DEFAULT 5,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- user + recipe as primary key so cant save twice
CREATE TABLE favourites (
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- unique key means one rating per user per recipe, rate.php updates it
CREATE TABLE ratings (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    stars      TINYINT NOT NULL CHECK (stars BETWEEN 1 AND 5),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY one_rating_per_user (user_id, recipe_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- sample data
-- ---------------------------------------------------------------

-- test users, password for both is password123
INSERT INTO users (name, email, password_hash) VALUES
('Jan Hoang', 'jan.hoang@example.com', '$2y$10$.cAe4TvBD15LNbjkrotBIuEFL84EDbwai5aDTj4aZ1b.qD8GofsjC'),
('Gabriel Andino', 'gabriel.andino@example.com', '$2y$10$.cAe4TvBD15LNbjkrotBIuEFL84EDbwai5aDTj4aZ1b.qD8GofsjC');

INSERT INTO categories (name) VALUES
('Starter'), ('Main'), ('Dessert'), ('Breakfast'), ('Salad'),
('Meat'), ('Vegetarian'), ('Vegan'), ('Italian'), ('Healthy'), ('Quick');

INSERT INTO recipes (id, title, slug, description, prep_time, cook_time, servings, difficulty) VALUES
(1, 'Spaghetti Bolognese', 'spaghetti-bolognese',
 'A slow-simmered beef ragu with smoked streaky bacon, red wine, marinated mushrooms and sun-dried tomatoes. Leftovers taste even better the next day.', 20, 105, 8, 'easy'),
(2, 'Vegan Pancakes', 'vegan-pancakes',
 'Light, fluffy American-style pancakes made without eggs or dairy. Serve with fresh berries, maple syrup or chocolate sauce.', 10, 20, 2, 'easy'),
(3, 'Healthy Pizza', 'healthy-pizza',
 'A yeast-free wholemeal yoghurt base spread with passata and piled with roasted pepper, courgette and red onion. On the table in about half an hour.', 15, 30, 2, 'medium'),
(4, 'Couscous Salad', 'couscous-salad',
 'A colourful vegan couscous salad with preserved lemon, dried cranberries, toasted pine nuts and pistachios, finished with plenty of parsley and rocket.', 20, 5, 6, 'easy'),
(5, 'Mushroom Doner', 'mushroom-doner',
 'A meat-free take on a doner kebab. Spiced oyster mushrooms in warm pitta with shredded cabbage, pickled onion, chilli sauce and minted yoghurt.', 15, 20, 4, 'medium');

-- category ids match the insert order above
INSERT INTO recipe_categories (recipe_id, category_id) VALUES
(1, 2), (1, 6), (1, 9),
(2, 4), (2, 7), (2, 8), (2, 11),
(3, 2), (3, 7), (3, 9), (3, 10),
(4, 5), (4, 7), (4, 8), (4, 10), (4, 11),
(5, 2), (5, 7), (5, 10);

-- ingredients
INSERT INTO ingredients (recipe_id, quantity, unit, name) VALUES
(1, '2', 'tbsp', 'olive oil, or sun-dried tomato oil from the jar'),
(1, '6', '', 'rashers smoked streaky bacon, chopped'),
(1, '2', '', 'large onions, chopped'),
(1, '3', '', 'garlic cloves, crushed'),
(1, '1', 'kg', 'lean minced beef'),
(1, '2', 'large glasses', 'red wine'),
(1, '2', 'x 400g cans', 'chopped tomatoes'),
(1, '1', 'x 290g jar', 'antipasti marinated mushrooms, drained'),
(1, '2', '', 'bay leaves, fresh or dried'),
(1, '1', 'tsp', 'dried oregano, or a small handful of fresh leaves, chopped'),
(1, '1', 'tsp', 'dried thyme, or a small handful of fresh leaves, chopped'),
(1, '', '', 'balsamic vinegar, a drizzle'),
(1, '12-14', '', 'sun-dried tomato halves in oil'),
(1, '', '', 'salt and freshly ground black pepper'),
(1, '1', 'good handful', 'fresh basil leaves, torn into small pieces'),
(1, '800-1000', 'g', 'dried spaghetti'),
(1, '', '', 'freshly grated parmesan, to serve'),

(2, '125', 'g', 'self-raising flour'),
(2, '2', 'tbsp', 'caster sugar'),
(2, '1', 'tsp', 'baking powder'),
(2, '1', 'pinch', 'sea salt'),
(2, '150', 'ml', 'soya milk or almond milk'),
(2, '1/4', 'tsp', 'vanilla extract'),
(2, '4', 'tsp', 'sunflower oil, for frying'),

(3, '125', 'g', 'self-raising brown or self-raising wholemeal flour, plus extra for dusting'),
(3, '1', 'pinch', 'fine sea salt'),
(3, '125', 'g', 'full-fat plain yoghurt'),
(3, '1', '', 'yellow or orange pepper, seeds removed and thinly sliced'),
(3, '1', '', 'courgette, cut into 1cm slices'),
(3, '1', '', 'red onion, cut into thin wedges'),
(3, '1', 'tbsp', 'extra virgin olive oil, plus extra for drizzling'),
(3, '1/2', 'tsp', 'dried chilli flakes'),
(3, '50', 'g', 'ready-grated mozzarella or cheddar, goats'' cheese broken into small chunks, or 1 mozzarella ball, torn'),
(3, '', '', 'freshly ground black pepper'),
(3, '', '', 'fresh basil leaves, to serve (optional)'),
(3, '6', 'tbsp', 'passata (approximately 100g)'),
(3, '1', 'tsp', 'dried oregano'),

(4, '225', 'g', 'couscous, prepared according to the packet instructions'),
(4, '8', '', 'small preserved lemons, flesh and rind finely chopped'),
(4, '180', 'g', 'dried cranberries'),
(4, '120', 'g', 'pine nuts, toasted'),
(4, '160', 'g', 'unsalted shelled pistachio nuts, roughly chopped'),
(4, '125', 'ml', 'olive oil'),
(4, '60', 'g', 'flatleaf parsley, finely chopped'),
(4, '4', '', 'garlic cloves, crushed'),
(4, '4', 'tbsp', 'red wine vinegar'),
(4, '1', '', 'red onion, finely chopped'),
(4, '1', 'tsp', 'salt, or to taste'),
(4, '80', 'g', 'rocket leaves'),

(5, '400', 'g', 'tin chopped tomatoes'),
(5, '2', 'tbsp', 'rose harissa'),
(5, '2', 'tsp', 'caster sugar'),
(5, '', '', 'lemon juice, a good squeeze'),
(5, '1', '', 'onion, very thinly sliced into half moons'),
(5, '2', 'level tsp', 'white wine vinegar'),
(5, '20', 'g', 'flatleaf parsley, finely chopped'),
(5, '150', 'g', 'plain yoghurt'),
(5, '1', 'heaped tsp', 'dried mint'),
(5, '', '', 'salt and freshly ground black pepper'),
(5, '500', 'g', 'oyster mushrooms, very thinly sliced lengthways'),
(5, '2', 'tsp', 'garlic oil'),
(5, '2', 'tsp', 'sweet paprika'),
(5, '2', 'heaped tsp', 'ground coriander'),
(5, '2', 'tsp', 'celery salt'),
(5, '3', 'tsp', 'garlic granules'),
(5, '1/2', 'tsp', 'freshly ground black pepper'),
(5, '4', '', 'white pitta breads'),
(5, '1/4', '', 'small white cabbage, very finely shredded'),
(5, '2', '', 'tomatoes, cut into half moons'),
(5, '4-6', '', 'pickled chillies, thinly sliced (optional)');

-- steps
INSERT INTO steps (recipe_id, step_no, instruction, minutes) VALUES
(1, 1, 'Heat the oil in a large heavy-based saucepan and fry the bacon over a medium heat until golden. Add the onions and garlic and fry until softened. Turn the heat up, add the minced beef and fry until browned all over, breaking up any chunks with a wooden spoon. Pour in the wine and boil until about a third of it has cooked away. Lower the heat and stir in the tomatoes, drained mushrooms, bay leaves, oregano, thyme and a drizzle of balsamic vinegar.', 25),
(1, 2, 'Blitz the sun-dried tomatoes in a small blender with a little of the oil to loosen them, or just chop them finely, then add to the pan. Season well with salt and pepper. Cover with a lid and simmer over a gentle heat for 1 to 1.5 hours, stirring occasionally, until rich and thickened. Stir in the basil at the end and add extra seasoning if needed.', 80),
(1, 3, 'Take the pan off the heat to settle while you cook the spaghetti in plenty of boiling salted water for the time stated on the packet. Drain and divide between warmed plates. Scatter a little parmesan over the spaghetti, add a good ladleful of the sauce and finish with more cheese and a twist of black pepper.', 12),

(2, 1, 'Put the flour, sugar, baking powder and salt in a bowl and mix thoroughly. Add the milk and vanilla extract and whisk until smooth.', 5),
(2, 2, 'Put a large non-stick frying pan over a medium heat. Add 2 teaspoons of the oil and wipe it around the pan with a heatproof brush, or carefully with a thick wad of kitchen paper.', 2),
(2, 3, 'Once the pan is hot, pour a small ladleful of batter (around two dessert spoons) into one side of the pan and spread it with the back of the spoon to about 10cm across. Make a second pancake in exactly the same way, greasing the pan with the remaining oil before adding the batter.', 3),
(2, 4, 'Cook for about a minute, until bubbles are popping on the surface and only the edges look dry and slightly shiny. Quickly and carefully flip over and cook the other side for another minute, until light, fluffy and pale golden brown. Turn them too late and they will be too set to rise evenly, though you can always flip again if the first side needs more colour.', 3),
(2, 5, 'Transfer to a plate and keep warm in a single layer, so they do not get squished, on a baking tray in a low oven while the rest are cooked in exactly the same way. Serve with your preferred toppings.', 10),

(3, 1, 'Preheat the oven to 220C/200C Fan/Gas 7.', 5),
(3, 2, 'Put the pepper, courgette, red onion and oil in a bowl, season with lots of black pepper and mix. Scatter over a large baking tray and roast for 15 minutes.', 15),
(3, 3, 'Meanwhile, mix the flour and salt in a large bowl. Add the yoghurt and 1 tablespoon of cold water, stir with a spoon, then bring it together with your hands into a soft, spongy dough and knead on a lightly floured surface for about a minute.', 5),
(3, 4, 'With a floured rolling pin, roll the dough into a rough oval about 3mm thick, turning it regularly. Aim for roughly 30cm long by 20cm wide so it fits the tray the vegetables were roasted on.', 5),
(3, 5, 'Tip the roasted vegetables into a bowl. Slide the dough onto the baking tray, bake for 5 minutes, then take it out and turn the dough over.', 5),
(3, 6, 'Mix the passata with the oregano and spread it over the dough. Top with the roasted vegetables, sprinkle over the chilli flakes and then the cheese, and bake for a further 8-10 minutes until the base is cooked through and the cheese is starting to brown.', 10),
(3, 7, 'Season with black pepper, drizzle with olive oil and scatter over fresh basil leaves, if using, just before serving.', 2),

(4, 1, 'In a large bowl mix all the ingredients together except the rocket, then taste and adjust the seasoning, adding more salt if necessary. Toss in the rocket and serve immediately.', 10),

(5, 1, 'Preheat the oven to 180C/200C Fan/Gas 4.', 5),
(5, 2, 'For the chilli sauce, put the chopped tomatoes, rose harissa, sugar and lemon juice in a small saucepan over a medium heat. Bring to a gentle boil and cook for 10 minutes, stirring regularly, until reduced and thick. Set aside to cool, then blend with a hand blender if you want it smooth, or leave it chunky.', 10),
(5, 3, 'For the onion, mix the onion slices with the vinegar and parsley and set aside.', 3),
(5, 4, 'For the yoghurt sauce, stir the dried mint into the yoghurt, season with salt and pepper and set aside.', 2),
(5, 5, 'Put the pittas in the oven to warm through for 5 minutes.', 5),
(5, 6, 'To make the doner, heat a frying pan over a medium-high heat. Add the mushrooms and dry-fry for 2 minutes, stirring once or twice. Add the garlic oil, paprika, coriander, celery salt, garlic granules and black pepper and quickly coat the mushrooms. Add 2 to 3 tablespoons of water to the pan and stir-fry for 1 minute before removing from the heat.', 4),
(5, 7, 'Split the warmed pittas. Spoon a little cabbage into each one, add some tomato and onion, divide the mushrooms between them, then add a little more cabbage and tomato and drizzle with the chilli and yoghurt sauces. Serve immediately, topped with the pickled chillies, if using.', 5);

-- some favourites and ratings so sorting has data
INSERT INTO favourites (user_id, recipe_id) VALUES (1, 1), (1, 5), (2, 2);

INSERT INTO ratings (user_id, recipe_id, stars) VALUES
(1, 1, 5), (2, 1, 4),
(1, 2, 4),
(2, 3, 3),
(1, 4, 4), (2, 4, 5),
(2, 5, 5);
