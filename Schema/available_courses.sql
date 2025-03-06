CREATE TABLE available_courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) NOT NULL,
    course_description TEXT,
    start_date DATE,
    end_date DATE,
    instructor_name VARCHAR(255)
);