DELIMITER //

CREATE TRIGGER status_completion_trigger
BEFORE UPDATE ON user_milestone
FOR EACH ROW
BEGIN
    
    IF NEW.milestone_progress = 100 THEN
        SET NEW.milestone_status = 'Completed';

        
        SET NEW.milestone_certificate_userID = NEW.milestone_userID;
        SET NEW.milestone_certificate_courseID = NEW.milestone_courseID;
    END IF;
END //

DELIMITER ;
