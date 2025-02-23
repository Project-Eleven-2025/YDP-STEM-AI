CREATE TABLE teachers_info (
	teacherID INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR (50) UNIQUE NOT NULL,    
	pass_hash VARCHAR (64) NOT NULL, /*use *SHA2* function to hash the encrypted password – SHA-256*/
	teacher_emailadd VARCHAR (50) UNIQUE NOT NULL,    
	teacher_phonenum VARCHAR(10) UNIQUE NOT NULL,    
	teacher_fname VARCHAR (50) NOT NULL,    
	teacher_lname VARCHAR (50) NOT NULL,    
	teacher_mname VARCHAR (50) NOT NULL,    
	teacher_post_nominal VARCHAR (25) DEFAULT NULL,
	teacher_birthdate DATE NOT NULL,    /*use *DATEDIFF* function to calculate age*/
	teacher_address VARCHAR (50) NOT NULL,    
	teacher_gender VARCHAR (10) NOT NULL,
	teacher_faculty VARCHAR (50) NOT NULL

);
