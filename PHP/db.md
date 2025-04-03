CREATE DATABASE national_exam;

USE national_exam;

-- Table for students
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    is_blind BOOLEAN DEFAULT FALSE,
    is_deaf BOOLEAN DEFAULT FALSE,
    exam_center VARCHAR(100),
    school VARCHAR(100),
    admission_number VARCHAR(20) UNIQUE,
    enrollment_type VARCHAR(50)
);

-- Table for subjects
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Table for exam years
CREATE TABLE exam_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    subject_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Table for questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_year_id INT,
    question_text TEXT NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    explanation TEXT,
    FOREIGN KEY (exam_year_id) REFERENCES exam_years(id)
);

-- Table for options
CREATE TABLE options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    option_value CHAR(1) NOT NULL,
    option_text TEXT NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Sample data
INSERT INTO students (full_name, exam_center, school, admission_number, enrollment_type) 
VALUES ('JOHN SMITH', 'ADDIS ABABA', 'TULDIM(R)', '921231410', 'Regular');

INSERT INTO subjects (name) VALUES ('Chemistry'), ('Physics'), ('Biology'), ('Mathematics');

INSERT INTO exam_years (year, subject_id) 
VALUES (2016, 1), (2015, 1), (2014, 1);

INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) 
VALUES 
(1, 'Which one of the following is correct about a chemical reaction?', 'c', 'A chemical reaction often involves an intermediate complex...'),
(1, 'What is the primary source of energy for Earth''s climate system?', 'b', 'Solar radiation is the primary source...'),
(1, 'Which gas is most abundant in Earth''s atmosphere?', 'c', 'Nitrogen is the most abundant gas...'),
(1, 'What is the pH of a neutral solution at 25°C?', 'b', 'At 25°C, a neutral solution has a pH of 7.'),
(1, 'Which element is a noble gas?', 'b', 'Helium is a noble gas...');

INSERT INTO options (question_id, option_value, option_text) 
VALUES 
(1, 'a', 'a. Chemical reaction is a reaction that follows only one path'),
(1, 'b', 'b. Reactions that follow double pass rate form high rate of product'),
(1, 'c', 'c. Reaction that passes through intermediate complex in pass rate'),
(1, 'd', 'd. It is a kind of reaction which uses activation energy at any time'),
(2, 'a', 'a. Geothermal heat'),
(2, 'b', 'b. Solar radiation'),
(2, 'c', 'c. Tidal forces'),
(2, 'd', 'd. Nuclear fusion in Earth''s core'),
(3, 'a', 'a. Oxygen'),
(3, 'b', 'b. Carbon dioxide'),
(3, 'c', 'c. Nitrogen'),
(3, 'd', 'd. Argon'),
(4, 'a', 'a. 0'),
(4, 'b', 'b. 7'),
(4, 'c', 'c. 14'),
(4, 'd', 'd. 10'),
(5, 'a', 'a. Hydrogen'),
(5, 'b', 'b. Helium'),
(5, 'c', 'c. Sodium'),
(5, 'd', 'd. Chlorine');
