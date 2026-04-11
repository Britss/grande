CREATE TABLE IF NOT EXISTS menu_item_sizes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT UNSIGNED NOT NULL,
    size_label VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY menu_item_sizes_menu_item_index (menu_item_id),
    KEY menu_item_sizes_available_index (is_available),
    CONSTRAINT menu_item_sizes_menu_item_fk
        FOREIGN KEY (menu_item_id) REFERENCES menu_items (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
