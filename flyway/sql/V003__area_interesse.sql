CREATE TABLE area_de_interesse (
    id INT AUTO_INCREMENT NOT NULL,
    titulo VARCHAR(25) NOT NULL,
    PRIMARY KEY(id)
)
DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE professor_area_de_interesse (
    professor_id INT NOT NULL,
    area_de_interesse_id INT NOT NULL,
    INDEX IDX_3AF58F007D2D84D5 (professor_id),
    INDEX IDX_3AF58F00D9034F57 (area_de_interesse_id),
    PRIMARY KEY(professor_id, area_de_interesse_id)
)
DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE professor_area_de_interesse
ADD CONSTRAINT FK_3AF58F007D2D84D5
FOREIGN KEY (professor_id)
REFERENCES professor (id);

ALTER TABLE professor_area_de_interesse
ADD CONSTRAINT FK_3AF58F00D9034F57
FOREIGN KEY (area_de_interesse_id)
REFERENCES area_de_interesse (id);
