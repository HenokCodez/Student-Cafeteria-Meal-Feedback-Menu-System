CREATE DATABASE IF NOT EXISTS cafeteria_db;
USE cafeteria_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    day_of_week VARCHAR(20) NOT NULL,
    meal_type ENUM('Breakfast', 'Lunch', 'Dinner') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Default admin password is 'admin123'
INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@astu.edu', '$2y$10$QuNNF5SoPJgxVQ4ftNWX/.TU.GdauhQoEgj3diRW6XGz0XI6L7tde', 'admin');

-- Sample Menu Items
INSERT INTO menu_items (name, description, day_of_week, meal_type) VALUES
('Shiro Wot', 'Smooth chickpea stew (shiro) cooked with Ethiopian spices, served with fresh injera.', 'Monday', 'Lunch'),
('Kik Alicha', 'Mild yellow split pea stew simmered with onions, garlic, and turmeric, served with injera.', 'Tuesday', 'Lunch'),
('Misir Wot', 'Spicy red lentil stew cooked in berbere sauce, rich in flavor and served with injera.', 'Wednesday', 'Lunch'),
('Dinich Wot', 'Soft potato stew cooked with onions, tomatoes, and traditional Ethiopian spices.', 'Thursday', 'Lunch'),
('Rice', 'Steamed rice served with mild Ethiopian seasoning.', 'Friday', 'Lunch');