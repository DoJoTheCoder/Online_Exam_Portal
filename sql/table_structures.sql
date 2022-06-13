CREATE TABLE user_info(
    uid INT PRIMARY KEY AUTO_INCREMENT,
    userName VARCHAR(128) UNIQUE NOT NULL,
    fullName VARCHAR(128) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(128) NOT NULL,
    pwd VARCHAR(128) NOT NULL,
);

CREATE TABLE subjects(
    subid INT PRIMARY KEY AUTO_INCREMENT,
    subName VARCHAR(128) NOT NULL
);

CREATE TABLE quizes(
    quizid INT PRIMARY KEY AUTO_INCREMENT,
    quizName VARCHAR(256) NOT NULL,
    subid INT NOT NULL,
    FOREIGN KEY (subid) 
    REFERENCES subjects(subid)
    ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TABLE question_answer(
    qid INT PRIMARY KEY AUTO_INCREMENT,
    quizid INT NOT NULL,
    question VARCHAR(1024) NOT NULL,
    opt1 VARCHAR(64) NOT NULL,
    opt2 VARCHAR(64) NOT NULL,
    opt3 VARCHAR(64) NOT NULL,
    opt4 VARCHAR(64) NOT NULL,
    answer INT(2) NOT NULL,
    FOREIGN KEY (quizid) 
    REFERENCES quizes(quizid)
    ON DELETE CASCADE ON UPDATE RESTRICT

);

CREATE TABLE tests(
    testid INT PRIMARY KEY AUTO_INCREMENT,
    uid INT NOT NULL,
    quizid INT NOT NULL,
    startTime DATETIME NOT NULL,
    endTime DATETIME,
    FOREIGN KEY (uid)
    REFERENCES user_info(uid)
    ON DELETE CASCADE ON UPDATE RESTRICT,
    FOREIGN KEY (quizid)
    REFERENCES quizes(quizid)
    ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TABLE user_answer(
    u_ansid INT PRIMARY KEY AUTO_INCREMENT,
    testid INT NOT NULL,
    qid INT NOT NULL,
    u_answer INT(2) NOT NULL,
    FOREIGN KEY (testid) 
    REFERENCES tests(testid)
    ON DELETE CASCADE ON UPDATE RESTRICT,
    FOREIGN KEY (qid)
    REFERENCES question_answer(qid)
    ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TABLE proctor_info(
    procId INT PRIMARY KEY AUTO_INCREMENT,
    testid INT NOT NULL,
    minute INT NOT NULL,
    imgFile varchar(32000) NOT NULL,
    latitude decimal(10,6) NOT NULL,
    longitude decimal(10,6) NOT NULL,
    FOREIGN KEY (testid) 
    REFERENCES tests(testid)
    ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TABLE invigilator_info(
    invigil_id int PRIMARY KEY,
    quizid int UNIQUE NOT NULL,
    pwd varchar(128),
    FOREIGN KEY (quizid)
    REFERENCES quizes(quizid)
);

CREATE TABLE admins(
    adminid INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    pwd VARCHAR(128) NOT NULL
);

-- Insertion into tables
-- main admin login credentials
-- login name : admin
-- login password : 123 
INSERT INTO admins (adminid, name, pwd,) VALUES (1, 'admin', '$2y$10$G0FJ0EvYBzcuLt.u3RmrROqMDAIWPMwvYiEnk6oPzXOa2HJzlEsXi');

