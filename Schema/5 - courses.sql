CREATE TABLE courses(
	courseID VARCHAR(5) PRIMARY KEY,  -- Create an incrementing C-001 and so on for the key (Backend)
    course_name VARCHAR(25) NOT NULL,
    course_description VARCHAR(255) DEFAULT NULL

);
