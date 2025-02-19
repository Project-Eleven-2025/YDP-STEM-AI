CREATE TABLE course_quizzes (
	quizID VARCHAR(5) PRIMARY KEY,
    quiz_courseID VARCHAR(5), 
	quiz_number INT NOT NULL,
    quiz_mats MEDIUMBLOB NOT NULL,
    
    FOREIGN KEY (quiz_courseID) REFERENCES courses (courseID) ON DELETE CASCADE

);