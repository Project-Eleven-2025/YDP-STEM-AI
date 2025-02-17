CREATE TABLE masterlist (
    userID VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    userbirthday DATE NOT NULL,
    datecreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);
