
-- Table structure for `quizzes`
CREATE TABLE IF NOT EXISTS `quizzes` (
    `id` INT AUTO_INCREMENT,
    `userID` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

-- Table structure for `lessons`
CREATE TABLE IF NOT EXISTS `lessons` (
    `id` INT AUTO_INCREMENT,
    `userID` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

-- Table structure for `assessments`
CREATE TABLE IF NOT EXISTS `assessments` (
    `id` INT AUTO_INCREMENT,
    `userID` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

-- Table structure for `certificates`
CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT AUTO_INCREMENT,
    `userID` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);
