
CREATE DATABASE IF NOT EXISTS canteen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE canteen_db;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS dishes;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dishes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)   NOT NULL,
    description TEXT           NOT NULL,
    price       DECIMAL(8,2)   NOT NULL,
    image       VARCHAR(255)   DEFAULT NULL,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT         NOT NULL,
    dish_id     INT         NOT NULL,
    rating      TINYINT(1)  NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT        NOT NULL,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, dish_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO dishes (name, description, price, image) VALUES
('Momos',       'Steamed dumplings stuffed with spiced vegetables, served with spicy red chutney. A campus favourite!', 30.00, NULL),
('Fried Rice',  'Wok-tossed rice with fresh vegetables, soy sauce, and aromatic spices. Filling and delicious.',        60.00, NULL),
('Maggi Noodles','Classic instant noodles cooked to perfection with extra veggies and the iconic Maggi masala.',        25.00, NULL),
('Samosa',      'Crispy golden pastry filled with spiced potato and peas, served hot with tamarind chutney.',           15.00, NULL),
('Sandwich',    'Grilled multi-layer sandwich with fresh vegetables, cheese, and tangy sauce on toasted bread.',        40.00, NULL),
('Tea',         'Hot aromatic Indian chai brewed with ginger, cardamom, and milk. The perfect energiser!',              10.00, NULL),
('Coffee',      'Rich and creamy hot coffee, available as black or with milk. Freshly brewed to order.',               20.00, NULL),
('Veg Chowmein','Stir-fried noodles with colourful vegetables, garlic, and Indo-Chinese seasonings.',                   50.00, NULL),
('Aloo Paratha','Soft whole-wheat flatbread stuffed with spiced mashed potato, served with pickle and curd.',           35.00, NULL),
('Cold Coffee', 'Chilled blended coffee with ice cream, milk, and a hint of chocolate syrup. Super refreshing!',       35.00, NULL);

-- Demo user  (password: password123)
INSERT INTO users (name, email, password) VALUES
('Demo Student', 'demo@adbu.ac.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample reviews
INSERT INTO reviews (user_id, dish_id, rating, review_text) VALUES
(1, 1, 5, 'Best momos on campus! The chutney is absolutely amazing. I order these every single day!'),
(1, 2, 4, 'Really good fried rice. Portions are generous and it stays hot for a long time.'),
(1, 3, 4, 'Perfect comfort food for a quick snack between classes. Love the extra veggies!'),
(1, 6, 5, 'The chai here is the real deal. Perfectly sweet with just the right amount of ginger.');

SELECT 'Database setup complete!' AS Status;


-- Update image filenames
UPDATE dishes SET image='momos.svg'      WHERE LOWER(name)='momos';
UPDATE dishes SET image='fried_rice.svg' WHERE LOWER(name)='fried rice';
UPDATE dishes SET image='maggi.svg'      WHERE LOWER(name)='maggi';
UPDATE dishes SET image='samosa.svg'     WHERE LOWER(name)='samosa';
UPDATE dishes SET image='sandwich.svg'   WHERE LOWER(name)='sandwich';
UPDATE dishes SET image='tea.svg'        WHERE LOWER(name)='tea';
UPDATE dishes SET image='coffee.svg'     WHERE LOWER(name)='coffee';
