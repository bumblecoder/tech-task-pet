DELIMITER //

CREATE PROCEDURE GetArticleById(
    IN p_article_id INT,
    OUT p_title VARCHAR(255),
    OUT p_content TEXT,
    OUT p_created_at DATETIME
)
BEGIN
    -- Извлечение статьи по её ID
    SELECT title, content, created_at
    INTO p_title, p_content, p_created_at
    FROM articles
    WHERE id = p_article_id;

    IF ROW_COUNT() = 0 THEN
        SET p_title = NULL;
        SET p_content = NULL;
        SET p_created_at = NULL;
    END IF;
END //

DELIMITER ;
