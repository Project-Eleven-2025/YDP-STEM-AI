CREATE TABLE course_lessons (
	lessonID VARCHAR(5) PRIMARY KEY, 
    lesson_courseID VARCHAR(5),
    lesson_number INT NOT NULL,
    lesson_mats LONGBLOB NOT NULL,
    
    FOREIGN KEY (lesson_courseID) REFERENCES courses (courseID) ON DELETE CASCADE

);