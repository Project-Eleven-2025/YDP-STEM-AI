DELIMITER $$

CREATE TRIGGER subs_endtime_trigger
BEFORE INSERT ON subscription_status
FOR EACH ROW
BEGIN
   
    IF NEW.subs_endtime IS NULL THEN
        SET NEW.subs_endtime = DATE_ADD(NEW.subs_starttime, INTERVAL 30 DAY);
    END IF;
END$$

DELIMITER ;
