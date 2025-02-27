CREATE TABLE course_problem_solving (
	problemID VARCHAR (5) PRIMARY KEY, -- Create an incrementing 'P-001' and so on for the key (Backend)
    problem_courseID VARCHAR (5),
    problem_number INT NOT NULL UNIQUE,
    problem_mats MEDIUMBLOB NOT NULL,
    
    FOREIGN KEY (problem_courseID) REFERENCES courses (courseID) ON DELETE CASCADE



);