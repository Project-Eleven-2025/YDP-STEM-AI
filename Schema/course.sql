CREATE TABLE `course` (
    `courseID` VARCHAR(50) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `type` ENUM('lesson', 'quiz', 'assessment', 'memo') NOT NULL,
    `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` VARCHAR(50) NOT NULL,
    `access_control` VARCHAR(50) NOT NULL,
    `quiz_data` JSON NOT NULL,
    `userID` VARCHAR(50) NOT NULL,
    `teacherID` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`courseID`),
    FOREIGN KEY (`userID`) REFERENCES `user_info`(`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`teacherID`) REFERENCES `teacher_info`(`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;