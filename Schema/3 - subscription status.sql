CREATE TABLE subscription_status(
    subsID INT PRIMARY KEY AUTO_INCREMENT,
    subs_userID INT,
    subs_type VARCHAR (25) DEFAULT 'BASIC', /*Update Accordingly*/
    subs_courses INT DEFAULT '1', /*Update Accordingly (decrement if user picked a course) ---- if 0, users can't open 		another course*/
    subs_starttime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subs_endtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subs_userID) REFERENCES user_info (userID) ON DELETE CASCADE
);
