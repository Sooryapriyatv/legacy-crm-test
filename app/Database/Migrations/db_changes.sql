-- Create users table
CREATE TABLE `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'manager', 'sales') NOT NULL DEFAULT 'sales',
    `manager_id` INT(11) UNSIGNED NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    CONSTRAINT `fk_users_manager`
        FOREIGN KEY (`manager_id`)
        REFERENCES `users` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Add assigned user to customers
ALTER TABLE `customers`
    ADD COLUMN `assigned_to` INT(11) UNSIGNED NULL AFTER `city`;

ALTER TABLE `customers`
    ADD CONSTRAINT `fk_customers_assigned_to`
    FOREIGN KEY (`assigned_to`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

-- Prevent duplicate customer email and phone
ALTER TABLE `customers`
    ADD UNIQUE KEY `customers_email_unique` (`email`),
    ADD UNIQUE KEY `customers_phone_unique` (`phone`);