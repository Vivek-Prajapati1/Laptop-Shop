-- Add new columns to users table
ALTER TABLE users
ADD COLUMN city VARCHAR(100) DEFAULT NULL,
ADD COLUMN state VARCHAR(100) DEFAULT NULL,
ADD COLUMN zip VARCHAR(20) DEFAULT NULL; 