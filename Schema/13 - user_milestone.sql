CREATE TABLE user_milestone (
    milestone_userID INT,  
    milestone_courseID VARCHAR(5), 
    milestone_lesson INT NOT NULL,  
    milestone_progress DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- make a computation in the backend where it will divide the completed lessons, quizzes, problem-solving  into total lessons, quizzes and problem-solving, then multiply it to 100.
    milestone_checkpoints INT DEFAULT NULL,
    milestone_status ENUM('In Progress', 'Completed') DEFAULT 'In Progress',  -- TRIGGER HERE (if milestone_progress == 100%)
    milestone_certificate_userID INT DEFAULT NULL, -- TRIGGER HERE (if milestone_status == completed)
    milestone_certificate_courseID VARCHAR(5) DEFAULT NULL, -- TRIGGER HERE (if milestone_status == completed)
    milestone_user_performance VARCHAR(255) DEFAULT NULL, -- PERFORMANCE BASED (Need materials for this)
    milestone_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
	
    PRIMARY KEY (milestone_userID, milestone_courseID),
    FOREIGN KEY (milestone_userID) REFERENCES user_info(userID) ON DELETE CASCADE,
    FOREIGN KEY (milestone_courseID) REFERENCES courses(courseID) ON DELETE CASCADE, 
    FOREIGN KEY (milestone_lesson) REFERENCES course_lessons(lesson_number) ON UPDATE CASCADE,
    
    FOREIGN KEY (milestone_certificate_userID, milestone_certificate_courseID) REFERENCES course_certificates(cert_userID, cert_courseID) ON DELETE SET NULL
);
