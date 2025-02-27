CREATE TABLE course_quizzes (
	quizID VARCHAR(5) PRIMARY KEY, -- Create an incrementing 'Q-001' and so on for the key (Backend)
    quiz_courseID VARCHAR(5), 
	quiz_number INT NOT NULL UNIQUE,
    quiz_mats MEDIUMBLOB NOT NULL,
    
    FOREIGN KEY (quiz_courseID) REFERENCES courses (courseID) ON DELETE CASCADE

);