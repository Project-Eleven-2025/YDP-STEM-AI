CREATE TABLE course (
    courseID VARCHAR(255) PRIMARY KEY, -- Unique identifier for the course
    label VARCHAR(255) NOT NULL,       -- Name or title of the course
    type ENUM('essay', 'multiple choice', 'quiz', 'lesson', 'assessment', 'memo') NOT NULL, -- Type of course content
    date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Timestamp of course creation
    created_by VARCHAR(255) NOT NULL, -- Reference to the user who created the course
    access_control ENUM('available', 'hidden', 'locked', 'deleted') NOT NULL DEFAULT 'available', -- Access control status
    quiz_data LONGBLOB,               -- Updated to match quizzes.file_data data type
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE, -- Reference to users table
    FOREIGN KEY (quiz_data) REFERENCES quizzes(file_data) ON DELETE SET NULL -- Reference to quizzes table
);
