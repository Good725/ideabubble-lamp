/*
ts:2015-01-01 00:00:43
*/

/*
 * Function for converting names to url-friendly names
 * Loop through each character
 * If the character is alphanumeric, append it to the result
 * If the character is a space or dash, trim the result (to prevent double spaces) and append a space
 * Replace all spaces with dashes
 * Convert to lowercase
 */
DROP FUNCTION IF EXISTS  format_url_name;
DELIMITER |
CREATE FUNCTION format_url_name(name1 VARCHAR(255)) RETURNS VARCHAR(255)
  BEGIN
    DECLARE i, length1 SMALLINT DEFAULT 1;
    DECLARE return1 VARCHAR(255) DEFAULT '';
    DECLARE c CHAR(1);
    SET length1 = CHAR_LENGTH(name1);
    REPEAT
    BEGIN
      SET c = MID(name1, i, 1 );
      IF c = ' ' THEN
        SET return1 = CONCAT(TRIM(return1), ' ');
      END IF;
      IF c = '-' THEN
        SET return1 = CONCAT(TRIM(return1), ' ');
      END IF;
      IF c REGEXP '[[:alnum:]]' THEN
        SET return1 = CONCAT(return1, c);
      END IF;
      SET i = i + 1;
    END;
    UNTIL i > length1
    END REPEAT;
    RETURN LOWER(REPLACE(return1, ' ', '-'));
  END |
DELIMITER ;
