DELIMITER //

CREATE TRIGGER user_nickname_trigger
BEFORE INSERT ON user_info
FOR EACH ROW
BEGIN
    
    IF NEW.user_nickname IS NULL THEN
        SET NEW.user_nickname = NEW.user_fname;
    END IF;
END //

DELIMITER ;
