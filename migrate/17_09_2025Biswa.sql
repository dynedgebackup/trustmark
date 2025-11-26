DELIMITER $$

CREATE TRIGGER trg_update_barangays_from_regions
AFTER UPDATE ON regions
FOR EACH ROW
BEGIN
    -- Only run when reg_region or is_active actually changes
    IF OLD.reg_region <> NEW.reg_region
       OR OLD.is_active <> NEW.is_active THEN

        UPDATE barangays b
        SET b.reg_region = NEW.reg_region,
            b.is_active = NEW.is_active
        WHERE b.reg_no = NEW.id;

    END IF;
END$$

DELIMITER ;
DELIMITER $$

CREATE TRIGGER trg_update_barangays_from_provinces
AFTER UPDATE ON provinces
FOR EACH ROW
BEGIN
    -- Only execute if prov_desc or is_active has changed
    IF OLD.prov_desc <> NEW.prov_desc
       OR OLD.is_active <> NEW.is_active THEN

        UPDATE barangays b
        SET b.prov_desc = NEW.prov_desc,
            b.is_active = NEW.is_active
        WHERE b.prov_no = NEW.id;

    END IF;
END$$

DELIMITER ;
DELIMITER $$

CREATE TRIGGER trg_update_barangays_from_municipalities
AFTER UPDATE ON municipalities
FOR EACH ROW
BEGIN
    -- Run only if mun_desc or is_active actually changed
    IF OLD.mun_desc <> NEW.mun_desc
       OR OLD.is_active <> NEW.is_active THEN

        UPDATE barangays b
        SET b.mun_desc = NEW.mun_desc,
            b.is_active = NEW.is_active
        WHERE b.mun_no = NEW.id;

    END IF;
END$$

DELIMITER ;

