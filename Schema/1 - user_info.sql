CREATE TABLE user_info(    
	userID INT PRIMARY KEY AUTO_INCREMENT,    
	username VARCHAR (50) UNIQUE NOT NULL,    
	pass_hash VARCHAR (64) NOT NULL, /*use *SHA2* function to hash the encrypted password – SHA-256*/
	user_emailadd VARCHAR (50) UNIQUE NOT NULL,    
	user_phonenum VARCHAR(10) UNIQUE NOT NULL,    
	user_fname VARCHAR (50) NOT NULL,    
	user_lname VARCHAR (50) NOT NULL,    
	user_mname VARCHAR (50) DEFAULT NULL,    
	user_nickname VARCHAR (25) DEFAULT NULL,
	user_birthdate DATE NOT NULL,    /*use *DATEDIFF* function to calculate age*/
	user_address VARCHAR (50) NOT NULL,    
	user_gender VARCHAR (10) NOT NULL,
	user_school VARCHAR (50) NOT NULL
);
