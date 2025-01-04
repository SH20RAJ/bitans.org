-- Add post_order column to users table
ALTER TABLE users ADD COLUMN post_order ENUM('latest', 'random') NOT NULL DEFAULT 'random';

-- Update existing users to have random order by default
UPDATE users SET post_order = 'random';
