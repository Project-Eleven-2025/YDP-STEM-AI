CREATE TABLE course_lessons (
	lessonID VARCHAR(5) PRIMARY KEY,  -- Create an incrementing L-001 and so on for the key (Backend)
    lesson_courseID VARCHAR(5),
    lesson_number INT NOT NULL UNIQUE,
    lesson_mats LONGBLOB NOT NULL,
    
    FOREIGN KEY (lesson_courseID) REFERENCES courses (courseID) ON DELETE CASCADE

);