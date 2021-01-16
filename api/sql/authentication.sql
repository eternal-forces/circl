CREATE TABLE AUTH (
    KEY_NO char(16) NOT NULL,
    USER_ID char(16) NOT NULL,
    PERMISSIONS int(255) NOT NULL,
    UNTIL_DATE DATETIME NOT NULL,
    PRIMARY KEY(KEY_NO, USER_ID),
    FOREIGN KEY(USER_ID) REFERENCES USERS(ID)  
)