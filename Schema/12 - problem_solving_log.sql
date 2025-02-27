CREATE TABLE problem_solving_log (
    plog_userID INT,
    plog_problemID VARCHAR(5),
    plog_attempt INT, -- the increment should be done in the backend
    plog_score VARCHAR (10) NOT NULL, -- scores should be converted to string first then concatenate with "/(POINTS PER PROBLEM)" before insert
    plog_timeattempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (plog_userID, plog_problemID, plog_attempt),
    FOREIGN KEY (plog_userID) REFERENCES user_info (userID) ON DELETE CASCADE,
    FOREIGN KEY (plog_problemID) REFERENCES course_problem_solving (problemID) ON DELETE CASCADE
	

);

