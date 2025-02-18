CREATE TABLE course_progress (
	prog_userID INT,
    prog_courseID VARCHAR(5),
	prog_checkpoints INT DEFAULT NULL,
    prog_quizzes INT DEFAULT NULL,
    prog_certificate VARCHAR(25) DEFAULT NULL,
    
    PRIMARY KEY (prog_userID, prog_courseID),
    FOREIGN KEY (prog_userID) REFERENCES user_info (userID) ON DELETE CASCADE,
    FOREIGN KEY (prog_courseID) REFERENCES courses (courseID) ON DELETE CASCADE
    
    
    
    
    /*Make an increment function for variables of prog_quizzes and prog_checkpoints column before INSERT (Can't make 		2 AUTO_INCREMENTS in the same table.). Increment every time previous checkpoint is clear, same with quizzes.*/
    
    
    
    /*FOR prog_certificate COLUMN -- use to check the completeness of a course, if it is null or not. If NULL, the 		  course can be opened. Else, show a message, "COURSE ALREADY COMPLETED" ++ present a PDF copy of a certificate         for that course (Refer to course_certificates table) */
	
);
