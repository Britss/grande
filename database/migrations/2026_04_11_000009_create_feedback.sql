CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'in_review', 'resolved', 'archived') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY feedback_user_index (user_id),
    KEY feedback_status_index (status),
    KEY feedback_category_index (category),
    CONSTRAINT feedback_user_fk
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
