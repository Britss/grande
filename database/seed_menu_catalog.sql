INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Americano', 'COFFEE.', 'Straightforward brewed coffee profile for all-day drinking.', 'public/images/menu-items/grande_coffee.jpg', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Americano' AND category = 'COFFEE.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Cappuccino', 'COFFEE.', 'Balanced espresso, milk, and foam.', 'public/images/menu-items/grande_coffee.jpg', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Cappuccino' AND category = 'COFFEE.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Spanish Latte', 'COFFEE.', 'Sweeter latte-style drink that stays popular on the existing menu.', 'public/images/menu-items/grande_coffee.jpg', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Spanish Latte' AND category = 'COFFEE.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Classic Pan De Sal', 'PANDESAL.', 'Soft bread rolls baked fresh throughout the day.', 'public/images/menu-items/classic_pan_de_sal.png', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Classic Pan De Sal' AND category = 'PANDESAL.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Cheese Pan De Sal', 'PANDESAL.', 'Savory variation with a richer filling profile.', 'public/images/menu-items/classic_pan_de_sal.png', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Cheese Pan De Sal' AND category = 'PANDESAL.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Ube Cheese Pan De Sal', 'PANDESAL.', 'Sweet and savory combination that matches the current product line.', 'public/images/menu-items/classic_pan_de_sal.png', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Ube Cheese Pan De Sal' AND category = 'PANDESAL.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Croissant', 'ENSAYMADA.', 'Buttery pastry that works well with coffee service.', '', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Croissant' AND category = 'ENSAYMADA.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Classic Ensaymada', 'ENSAYMADA.', 'Soft, sweet, and familiar bakery staple.', 'public/images/menu-items/classic_ensaymada.png', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Classic Ensaymada' AND category = 'ENSAYMADA.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Dark Chocolate Loaf', 'LOAF.', 'Loaf option positioned as a shareable baked item.', '', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Dark Chocolate Loaf' AND category = 'LOAF.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Grande Burger', 'SANDWICHES.', 'Casual savory item that broadens the menu beyond drinks and bread.', '', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Grande Burger' AND category = 'SANDWICHES.');

INSERT INTO menu_items (name, category, description, image_url, is_available)
SELECT 'Cheese and Ham Pan De Sal', 'SANDWICHES.', 'A simple grab-and-go savory option.', '', 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Cheese and Ham Pan De Sal' AND category = 'SANDWICHES.');

INSERT INTO menu_item_sizes (menu_item_id, size_label, price, is_default, sort_order, is_available)
SELECT mi.id, size_rows.size_label, size_rows.price, size_rows.is_default, size_rows.sort_order, 1
FROM menu_items mi
INNER JOIN (
    SELECT 'Americano' AS name, 'COFFEE.' AS category, '12oz' AS size_label, 79.00 AS price, 1 AS is_default, 10 AS sort_order
    UNION ALL SELECT 'Americano', 'COFFEE.', '16oz', 89.00, 0, 20
    UNION ALL SELECT 'Cappuccino', 'COFFEE.', '12oz', 99.00, 1, 10
    UNION ALL SELECT 'Cappuccino', 'COFFEE.', '16oz', 109.00, 0, 20
    UNION ALL SELECT 'Spanish Latte', 'COFFEE.', '12oz', 109.00, 1, 10
    UNION ALL SELECT 'Spanish Latte', 'COFFEE.', '16oz', 119.00, 0, 20
    UNION ALL SELECT 'Classic Pan De Sal', 'PANDESAL.', 'piece', 12.00, 1, 10
    UNION ALL SELECT 'Classic Pan De Sal', 'PANDESAL.', 'bundle', 60.00, 0, 20
    UNION ALL SELECT 'Cheese Pan De Sal', 'PANDESAL.', 'piece', 18.00, 1, 10
    UNION ALL SELECT 'Cheese Pan De Sal', 'PANDESAL.', 'bundle', 90.00, 0, 20
    UNION ALL SELECT 'Ube Cheese Pan De Sal', 'PANDESAL.', 'piece', 22.00, 1, 10
    UNION ALL SELECT 'Ube Cheese Pan De Sal', 'PANDESAL.', 'bundle', 110.00, 0, 20
    UNION ALL SELECT 'Croissant', 'ENSAYMADA.', 'regular', 75.00, 1, 10
    UNION ALL SELECT 'Classic Ensaymada', 'ENSAYMADA.', 'regular', 55.00, 1, 10
    UNION ALL SELECT 'Dark Chocolate Loaf', 'LOAF.', 'slice', 45.00, 1, 10
    UNION ALL SELECT 'Dark Chocolate Loaf', 'LOAF.', 'whole', 220.00, 0, 20
    UNION ALL SELECT 'Grande Burger', 'SANDWICHES.', 'regular', 129.00, 1, 10
    UNION ALL SELECT 'Cheese and Ham Pan De Sal', 'SANDWICHES.', 'regular', 69.00, 1, 10
) size_rows ON size_rows.name = mi.name AND size_rows.category = mi.category
WHERE NOT EXISTS (
    SELECT 1
    FROM menu_item_sizes mis
    WHERE mis.menu_item_id = mi.id
      AND mis.size_label = size_rows.size_label
);
