CREATE TABLE course_certificates(
	cert_userID INT,
  	cert_courseID VARCHAR(5),
    cert_course MEDIUMBLOB, 
    
    PRIMARY KEY (cert_userID, cert_courseID),
    FOREIGN KEY (cert_userID) REFERENCES user_info (userID) ON DELETE CASCADE,
    FOREIGN KEY (cert_courseID) REFERENCES courses (courseID) ON DELETE CASCADE
    
    /*We can insert PDFs and PNGs on the 'cert_course' column - Maximum size of 16MB*/
    
);
