CREATE TABLE course (
    courseID VARCHAR(50) PRIMARY KEY,
    label VARCHAR(255) NOT NULL,
    type ENUM('lesson', 'quiz', 'assessment', 'memo') NOT NULL DEFAULT 'quiz',
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(100) NOT NULL,
    access_control ENUM('public', 'private') NOT NULL DEFAULT 'public',
    quiz_data JSON NOT NULL
);