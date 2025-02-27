CREATE TABLE quizzes_log (
    qlog_userID INT,
    qlog_quizID VARCHAR(5),
    qlog_attempt INT, -- the increment should be done in the backend
    qlog_score VARCHAR (5) NOT NULL, -- scores should be converted to string first then concatenate with "/(ITEMS IN A QUIZ)" before insert
    qlog_timeattempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (qlog_userID, qlog_quizID, qlog_attempt),
    FOREIGN KEY (qlog_userID) REFERENCES user_info (userID) ON DELETE CASCADE,
    FOREIGN KEY (qlog_quizID) REFERENCES course_quizzes (quizID) ON DELETE CASCADE
	

);

