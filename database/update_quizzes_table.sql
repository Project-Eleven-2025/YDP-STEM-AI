-- Add a new column 'quiz_id' to the 'quizzes' table
ALTER TABLE quizzes ADD COLUMN quiz_id VARCHAR(255) NOT NULL UNIQUE;

-- Populate 'quiz_id' with unique values (e.g., UUIDs)
UPDATE quizzes SET quiz_id = UUID();

-- Ensure 'quiz_id' is not null and unique
ALTER TABLE quizzes MODIFY quiz_id VARCHAR(255) NOT NULL UNIQUE;

-- Add columns to store file data and metadata in the 'quizzes' table
ALTER TABLE quizzes 
ADD COLUMN file_name VARCHAR(255),
ADD COLUMN file_type VARCHAR(50),
ADD COLUMN file_size INT,
ADD COLUMN file_data LONGBLOB;

-- Create a new table 'uploads' to store files
CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    file_data LONGBLOB NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
