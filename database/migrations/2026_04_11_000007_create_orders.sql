CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    reservation_id INT UNSIGNED DEFAULT NULL,
    order_number VARCHAR(50) DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'preparing', 'ready', 'completed', 'cancelled', 'rejected') NOT NULL DEFAULT 'pending',
    payment_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    receipt_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY orders_order_number_unique (order_number),
    KEY orders_user_index (user_id),
    KEY orders_reservation_index (reservation_id),
    KEY orders_status_index (status),
    CONSTRAINT orders_user_fk
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE SET NULL,
    CONSTRAINT orders_reservation_fk
        FOREIGN KEY (reservation_id) REFERENCES reservations (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
