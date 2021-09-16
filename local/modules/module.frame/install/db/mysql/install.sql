CREATE TABLE IF NOT EXISTS b_module_frame_table
(
	ID int(11) NOT NULL AUTO_INCREMENT,
	CODE varchar(255) NULL,
	DATE_INSERT datetime NOT NULL,
    NAME varchar(255) NULL,
    DESCRIPTION varchar(255) NULL,
    PRIMARY KEY (ID)
);
