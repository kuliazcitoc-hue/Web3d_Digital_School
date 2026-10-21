ALTER TABLE users
  ADD COLUMN last_purchase_seen_id BIGINT UNSIGNED NOT NULL DEFAULT 0;

UPDATE users
SET last_purchase_seen_id = COALESCE(
  (SELECT MAX(p.id) FROM purchases p WHERE p.buyer_id = users.id OR p.seller_id = users.id),
  0
);

ALTER TABLE messages
  ADD COLUMN image_data LONGTEXT DEFAULT NULL;
