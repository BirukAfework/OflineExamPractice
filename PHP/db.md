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
    duration_minutes INT DEFAULT 120,
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

-- Sample data for students
INSERT INTO students (full_name, exam_center, school, admission_number, enrollment_type) 
VALUES ('JOHN SMITH', 'ADDIS ABABA', 'TULDIM(R)', '921231410', 'Regular');

-- Insert Subjects
INSERT INTO subjects (name) VALUES 
('Chemistry'), 
('Mathematics'), 
('Biology'), 
('Physics'), 
('English'), 
('SAT');

-- Insert Exam Years for all subjects (2016 only for simplicity, durations vary)
INSERT INTO exam_years (year, subject_id, duration_minutes) VALUES 
-- Chemistry
(2016, 1, 120),
-- Mathematics
(2016, 2, 60),
-- Biology
(2016, 3, 100),
-- Physics
(2016, 4, 90),
-- English
(2016, 5, 90),
-- SAT
(2016, 6, 180);

-- Sample Questions and Options (2016 only, 1 question per subject for brevity)
-- Chemistry 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(1, 'Which gas is most abundant in Earth''s atmosphere?', 'c', 'Nitrogen is the most abundant gas, about 78%.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(1, 'a', 'a. Oxygen'), (1, 'b', 'b. Carbon dioxide'), (1, 'c', 'c. Nitrogen'), (1, 'd', 'd. Argon');

-- Mathematics 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(2, 'What is 2 + 3?', 'c', '2 + 3 equals 5.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(2, 'a', 'a. 4'), (2, 'b', 'b. 6'), (2, 'c', 'c. 5'), (2, 'd', 'd. 7');

-- Biology 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(3, 'What is the powerhouse of the cell?', 'c', 'The mitochondrion generates ATP.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(3, 'a', 'a. Nucleus'), (3, 'b', 'b. Ribosome'), (3, 'c', 'c. Mitochondrion'), (3, 'd', 'd. Chloroplast');

-- Physics 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(4, 'What is the SI unit of force?', 'c', 'The Newton (N) is the SI unit of force.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(4, 'a', 'a. Joule'), (4, 'b', 'b. Watt'), (4, 'c', 'c. Newton'), (4, 'd', 'd. Volt');

-- English 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(5, 'Which word is a synonym for "happy"?', 'b', 'Joyful means the same as happy.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(5, 'a', 'a. Sad'), (5, 'b', 'b. Joyful'), (5, 'c', 'c. Angry'), (5, 'd', 'd. Tired');

-- SAT 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(6, 'If x + 3 = 7, what is x?', 'a', 'x + 3 = 7, so x = 4.');
INSERT INTO options (question_id, option_value, option_text) VALUES 
(6, 'a', 'a. 4'), (6, 'b', 'b. 5'), (6, 'c', 'c. 6'), (6, 'd', 'd. 3');
