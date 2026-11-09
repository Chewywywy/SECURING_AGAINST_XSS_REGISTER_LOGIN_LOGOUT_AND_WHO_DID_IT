CREATE DATABASE IF NOT EXISTS coffeeshop;
USE coffeeshop;

DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- USER ENTITY
CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    address     VARCHAR(255),
    age         INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PARENT TABLE (the "one" side)
CREATE TABLE categories (
    category_id     INT AUTO_INCREMENT PRIMARY KEY,
    category_name   VARCHAR(100) NOT NULL,
    description     VARCHAR(255),
    added_by        INT,
    last_updated_by INT,
    last_updated    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- CHILD TABLE (the "many" side)
CREATE TABLE menu_items (
    item_id         INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT NOT NULL,
    item_name       VARCHAR(100) NOT NULL,
    price           DECIMAL(8,2) NOT NULL,
    size            VARCHAR(20),
    added_by        INT,
    last_updated_by INT,
    last_updated    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL
);

INSERT INTO categories (category_name, description) VALUES
('Espresso', 'Classic espresso-based drinks'),
('Frappuccino', 'Blended iced drinks'),
('Tea', 'Hot and iced teas'),
('Pastries', 'Baked fresh every morning'),
('Cold Brew', 'Slow-steeped for 20 hours');

INSERT INTO menu_items (category_id, item_name, price, size) VALUES
(1, 'Americano', 120.00, 'Tall'),
(1, 'Caffe Latte', 150.00, 'Grande'),
(1, 'Cappuccino', 150.00, 'Grande'),
(1, 'Caramel Macchiato', 175.00, 'Grande'),
(2, 'Mocha Frappuccino', 195.00, 'Grande'),
(2, 'Caramel Frappuccino', 195.00, 'Grande'),
(2, 'Java Chip Frappuccino', 205.00, 'Venti'),
(3, 'Green Tea Latte', 160.00, 'Grande'),
(3, 'Iced Peach Tea', 130.00, 'Venti'),
(3, 'Chai Latte', 155.00, 'Tall'),
(4, 'Butter Croissant', 95.00, 'Regular'),
(4, 'Blueberry Muffin', 110.00, 'Regular'),
(4, 'Chocolate Chip Cookie', 85.00, 'Regular'),
(5, 'Classic Cold Brew', 140.00, 'Grande'),
(5, 'Vanilla Sweet Cream Cold Brew', 170.00, 'Venti');
