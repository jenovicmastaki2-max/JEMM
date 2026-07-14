-- Add purchases table for premium access
ALTER TABLE IF EXISTS `documents` ADD COLUMN IF NOT EXISTS `price_cents` INT DEFAULT 0;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `document_id` INT NOT NULL,
  `amount_cents` INT NOT NULL,
  `provider` VARCHAR(100) DEFAULT 'stripe',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
