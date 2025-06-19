    -- Let's disable foreign key checks , but making sure they are enabled after
    SET FOREIGN_KEY_CHECKS = 0;

    -- Let's create the user table
    CREATE TABLE IF NOT EXISTS `User` (
        `User_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `UserName` VARCHAR(30) NOT NULL,
        `UserEmail` VARCHAR(30) NOT NULL UNIQUE,
        `UserSex` ENUM('Male', 'Female') NOT NULL,
        `UserAge` INT,
        `HashedPassword` VARCHAR(16),
        PRIMARY KEY (`User_Id`)
        );

    -- Let's create the Card Table as of Bank Card
    CREATE TABLE IF NOT EXISTS `Card` (
        `BankCard_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `BankSequence` VARCHAR(16),
        `BankProvider` VARCHAR(20),
        `DatePeremption` DATE,
        `CVV` INT(3),
        PRIMARY KEY (`BankCard_Id`)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

    -- Let's create the User_Card Table
    CREATE TABLE IF NOT EXISTS `User_Card` (
    `FK1_User_Id` CHAR(36) NOT NULL,
    `FK2_BankCard_Id` CHAR(36) NOT NULL,
    PRIMARY KEY (`FK1_User_Id`, `FK2_BankCard_Id`),
    INDEX `fk_User_Card_Card1_idx` (`FK2_BankCard_Id` ASC) VISIBLE,
    CONSTRAINT `fk_User_Card_User`
        FOREIGN KEY (`FK1_User_Id`)
        REFERENCES `User` (`User_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_User_Card_Card`
        FOREIGN KEY (`FK2_BankCard_Id`)
        REFERENCES `Card` (`BankCard_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the Event Table
    CREATE TABLE IF NOT EXISTS `Event` (
        `Event_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `FK_User_Id` CHAR(36) NOT NULL,
        `EventName` VARCHAR(30) NOT NULL,
        `EventDesc` TEXT,
        `EventDate` DATE NOT NULL,
        `EventTime` TIME,
        `EventVenue` VARCHAR(200) NOT NULL,
        `EventLocation` VARCHAR(255),
        `EventStatus` ENUM('Waiting', 'In Progress', 'Finished') NOT NULL,
        `EventPrice` DECIMAL(10, 2) NOT NULL,
        PRIMARY KEY (`Event_Id`),
        INDEX `fk_Event_User1_idx` (`FK_User_Id` ASC) VISIBLE,
        CONSTRAINT `fk_Event_User1`
        FOREIGN KEY (`FK_User_Id`)
        REFERENCES `User` (`User_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the book table
    CREATE TABLE IF NOT EXISTS `Book` (
        `FK1_User_Id` CHAR(36) NOT NULL,
        `FK2_Event_Id` CHAR(36) NOT NULL,
        `Status` TINYINT(1) DEFAULT 0,
        PRIMARY KEY (`FK1_User_Id`, `FK2_Event_Id`),
        INDEX `fk_Book_Event1_idx` (`FK2_Event_Id` ASC) VISIBLE,
        CONSTRAINT `fk_Book_User1`
        FOREIGN KEY (`FK1_User_Id`)
        REFERENCES `User` (`User_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_Book_Event1`
        FOREIGN KEY (`FK2_Event_Id`)
        REFERENCES `Event` (`Event_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the Ticket table
    CREATE TABLE IF NOT EXISTS `Ticket` (
        `Ticket_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `FK_User_Id` CHAR(36) NOT NULL,
        `FK_Event_Id` CHAR(36) NOT NULL,
        `TicketType` VARCHAR(14),
        PRIMARY KEY (`Ticket_Id`),
        INDEX `fk_Ticket_User1_idx` (`FK_User_Id` ASC) VISIBLE,
        INDEX `fk_Ticket_Event1_idx` (`FK_Event_Id` ASC) VISIBLE,
        CONSTRAINT `fk_Ticket_User1`
        FOREIGN KEY (`FK_User_Id`)
        REFERENCES `User` (`User_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_Ticket_Event1`
        FOREIGN KEY (`FK_Event_Id`)
        REFERENCES `Event` (`Event_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the Image table
    CREATE TABLE IF NOT EXISTS `Image` (
        `Image_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `ImageUrl` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`Image_Id`)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;



    -- Let's create the Event_Images table
    CREATE TABLE IF NOT EXISTS `Event_Images` (
        `FK1_Event_Id` CHAR(36) NOT NULL,
        `FK2_Image_Id` CHAR(36) NOT NULL,
        PRIMARY KEY (`FK1_Event_Id`, `FK2_Image_Id`),
        INDEX `fk_Event_Images_Image1_idx` (`FK2_Image_Id` ASC) VISIBLE,
        CONSTRAINT `fk_Event_Images_Event1`
        FOREIGN KEY (`FK1_Event_Id`)
        REFERENCES `Event` (`Event_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_Event_Images_Image1`
        FOREIGN KEY (`FK2_Image_Id`)
        REFERENCES `Image` (`Image_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the Cart table
    CREATE TABLE IF NOT EXISTS `Cart` (
        `Cart_Id` CHAR(36) NOT NULL DEFAULT (UUID()),
        `CartState` VARCHAR(10),
        `CreationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `CartTotalPrize` DECIMAL(10, 2),
        PRIMARY KEY (`Cart_Id`)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


    -- Let's create the Cart_Event table
    CREATE TABLE IF NOT EXISTS `Cart_Event` (
        `FK1_Cart_Id` CHAR(36) NOT NULL,
        `FK2_Event_Id` CHAR(36) NOT NULL,
        PRIMARY KEY (`FK1_Cart_Id`, `FK2_Event_Id`),
        INDEX `fk_Cart_Event_Event1_idx` (`FK2_Event_Id` ASC) VISIBLE,
        CONSTRAINT `fk_Cart_Event_Cart1`
        FOREIGN KEY (`FK1_Cart_Id`)
        REFERENCES `Cart` (`Cart_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_Cart_Event_Event1`
        FOREIGN KEY (`FK2_Event_Id`)
        REFERENCES `Event` (`Event_Id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;

    -- docker exec -it a6c7ba739375 mysql -uroot -p