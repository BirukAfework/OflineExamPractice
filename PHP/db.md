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

-- Sample data for students
INSERT INTO students (full_name, exam_center, school, admission_number, enrollment_type) 
VALUES ('JOHN SMITH', 'ADDIS ABABA', 'TULDIM(R)', '921231410', 'Regular');

-- Insert Subjects
INSERT INTO subjects (name) VALUES 
('Chemistry'), 
('Physics'), 
('Biology'), 
('Mathematics');

-- Insert Exam Years for all subjects
INSERT INTO exam_years (year, subject_id) VALUES 
-- Chemistry
(2016, 1), (2015, 1), (2014, 1),
-- Physics
(2016, 2), (2015, 2), (2014, 2),
-- Biology
(2016, 3), (2015, 3), (2014, 3),
-- Mathematics
(2016, 4), (2015, 4), (2014, 4);

-- Insert Questions and Options
-- Chemistry 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(1, 'Which one of the following is correct about a chemical reaction?', 'c', 'A chemical reaction often involves an intermediate complex, which is a temporary species formed during the reaction.'),
(1, 'What is the primary source of energy for Earth''s climate system?', 'b', 'Solar radiation is the primary source of energy for Earth''s climate system.'),
(1, 'Which gas is most abundant in Earth''s atmosphere?', 'c', 'Nitrogen is the most abundant gas in Earth''s atmosphere, making up about 78% of the total volume.'),
(1, 'What is the pH of a neutral solution at 25°C?', 'b', 'At 25°C, a neutral solution has a pH of 7.'),
(1, 'Which element is a noble gas?', 'b', 'Helium is a noble gas, belonging to Group 18 of the periodic table.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(1, 'a', 'a. Chemical reaction is a reaction that follows only one path'), (1, 'b', 'b. Reactions that follow double pass rate form high rate of product'), (1, 'c', 'c. Reaction that passes through intermediate complex in pass rate'), (1, 'd', 'd. It is a kind of reaction which uses activation energy at any time'),
(2, 'a', 'a. Geothermal heat'), (2, 'b', 'b. Solar radiation'), (2, 'c', 'c. Tidal forces'), (2, 'd', 'd. Nuclear fusion in Earth''s core'),
(3, 'a', 'a. Oxygen'), (3, 'b', 'b. Carbon dioxide'), (3, 'c', 'c. Nitrogen'), (3, 'd', 'd. Argon'),
(4, 'a', 'a. 0'), (4, 'b', 'b. 7'), (4, 'c', 'c. 14'), (4, 'd', 'd. 10'),
(5, 'a', 'a. Hydrogen'), (5, 'b', 'b. Helium'), (5, 'c', 'c. Sodium'), (5, 'd', 'd. Chlorine');

-- Chemistry 2015
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(2, 'What is the atomic number of Carbon?', 'b', 'Carbon has an atomic number of 6.'),
(2, 'Which of these is a strong acid?', 'c', 'Hydrochloric acid (HCl) is a strong acid.'),
(2, 'What gas do plants produce during photosynthesis?', 'a', 'Plants produce oxygen during photosynthesis.'),
(2, 'What is the main component of natural gas?', 'd', 'Methane (CH4) is the main component of natural gas.'),
(2, 'Which element has the symbol Fe?', 'b', 'Iron has the symbol Fe.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(6, 'a', 'a. 5'), (6, 'b', 'b. 6'), (6, 'c', 'c. 7'), (6, 'd', 'd. 8'),
(7, 'a', 'a. H2O'), (7, 'b', 'b. NaOH'), (7, 'c', 'c. HCl'), (7, 'd', 'd. CH3COOH'),
(8, 'a', 'a. Oxygen'), (8, 'b', 'b. Carbon dioxide'), (8, 'c', 'c. Nitrogen'), (8, 'd', 'd. Hydrogen'),
(9, 'a', 'a. Oxygen'), (9, 'b', 'b. Nitrogen'), (9, 'c', 'c. Carbon'), (9, 'd', 'd. Methane'),
(10, 'a', 'a. Gold'), (10, 'b', 'b. Iron'), (10, 'c', 'c. Silver'), (10, 'd', 'd. Copper');

-- Chemistry 2014
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(3, 'What is the chemical formula for water?', 'b', 'The chemical formula for water is H2O.'),
(3, 'Which element is a halogen?', 'c', 'Chlorine is a halogen, found in Group 17.'),
(3, 'What type of bond is formed between Na and Cl?', 'a', 'An ionic bond forms between sodium (Na) and chlorine (Cl).'),
(3, 'What is the molar mass of CO2?', 'd', 'The molar mass of CO2 is 44 g/mol (12 + 16 + 16).'),
(3, 'Which gas is used in respiration?', 'b', 'Oxygen is used in cellular respiration.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(11, 'a', 'a. HO'), (11, 'b', 'b. H2O'), (11, 'c', 'c. H2O2'), (11, 'd', 'd. H3O'),
(12, 'a', 'a. Sodium'), (12, 'b', 'b. Potassium'), (12, 'c', 'c. Chlorine'), (12, 'd', 'd. Magnesium'),
(13, 'a', 'a. Ionic'), (13, 'b', 'b. Covalent'), (13, 'c', 'c. Metallic'), (13, 'd', 'd. Hydrogen'),
(14, 'a', 'a. 28 g/mol'), (14, 'b', 'b. 32 g/mol'), (14, 'c', 'c. 40 g/mol'), (14, 'd', 'd. 44 g/mol'),
(15, 'a', 'a. Nitrogen'), (15, 'b', 'b. Oxygen'), (15, 'c', 'c. Carbon dioxide'), (15, 'd', 'd. Helium');

-- Physics 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(4, 'What is the SI unit of force?', 'c', 'The SI unit of force is the Newton (N).'),
(4, 'Which law states that F = ma?', 'b', 'Newton''s Second Law states that force equals mass times acceleration.'),
(4, 'What is the speed of light in a vacuum?', 'd', 'The speed of light in a vacuum is approximately 3 × 10^8 m/s.'),
(4, 'What is the unit of electric current?', 'a', 'The unit of electric current is the Ampere (A).'),
(4, 'What type of energy does a moving car have?', 'c', 'A moving car has kinetic energy.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(16, 'a', 'a. Joule'), (16, 'b', 'b. Watt'), (16, 'c', 'c. Newton'), (16, 'd', 'd. Volt'),
(17, 'a', 'a. First Law'), (17, 'b', 'b. Second Law'), (17, 'c', 'c. Third Law'), (17, 'd', 'd. Law of Gravitation'),
(18, 'a', 'a. 3 × 10^6 m/s'), (18, 'b', 'b. 3 × 10^7 m/s'), (18, 'c', 'c. 3 × 10^9 m/s'), (18, 'd', 'd. 3 × 10^8 m/s'),
(19, 'a', 'a. Ampere'), (19, 'b', 'b. Ohm'), (19, 'c', 'c. Volt'), (19, 'd', 'd. Watt'),
(20, 'a', 'a. Potential'), (20, 'b', 'b. Thermal'), (20, 'c', 'c. Kinetic'), (20, 'd', 'd. Chemical');

-- Physics 2015
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(5, 'What is the acceleration due to gravity on Earth?', 'b', 'The acceleration due to gravity on Earth is approximately 9.8 m/s².'),
(5, 'Which device measures voltage?', 'd', 'A voltmeter measures voltage.'),
(5, 'What is the formula for work?', 'c', 'Work is calculated as force times distance (W = F × d).'),
(5, 'What is the unit of power?', 'a', 'The unit of power is the Watt (W).'),
(5, 'What causes objects to fall to the ground?', 'b', 'Gravity causes objects to fall to the ground.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(21, 'a', 'a. 8.8 m/s²'), (21, 'b', 'b. 9.8 m/s²'), (21, 'c', 'c. 10.8 m/s²'), (21, 'd', 'd. 11.8 m/s²'),
(22, 'a', 'a. Ammeter'), (22, 'b', 'b. Ohmmeter'), (22, 'c', 'c. Galvanometer'), (22, 'd', 'd. Voltmeter'),
(23, 'a', 'a. W = m × g'), (23, 'b', 'b. W = P × t'), (23, 'c', 'c. W = F × d'), (23, 'd', 'd. W = V × I'),
(24, 'a', 'a. Watt'), (24, 'b', 'b. Joule'), (24, 'c', 'c. Newton'), (24, 'd', 'd. Volt'),
(25, 'a', 'a. Friction'), (25, 'b', 'b. Gravity'), (25, 'c', 'c. Magnetism'), (25, 'd', 'd. Tension');

-- Physics 2014
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(6, 'What is the primary source of energy for the Sun?', 'd', 'Nuclear fusion powers the Sun.'),
(6, 'Which color has the longest wavelength?', 'c', 'Red light has the longest wavelength in the visible spectrum.'),
(6, 'What is the unit of resistance?', 'b', 'The unit of resistance is the Ohm (Ω).'),
(6, 'What is the formula for kinetic energy?', 'a', 'Kinetic energy is calculated as KE = ½mv².'),
(6, 'What type of wave is sound?', 'c', 'Sound is a longitudinal wave.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(26, 'a', 'a. Fission'), (26, 'b', 'b. Combustion'), (26, 'c', 'c. Radiation'), (26, 'd', 'd. Fusion'),
(27, 'a', 'a. Blue'), (27, 'b', 'b. Green'), (27, 'c', 'c. Red'), (27, 'd', 'd. Violet'),
(28, 'a', 'a. Ampere'), (28, 'b', 'b. Ohm'), (28, 'c', 'c. Volt'), (28, 'd', 'd. Watt'),
(29, 'a', 'a. KE = ½mv²'), (29, 'b', 'b. KE = mgh'), (29, 'c', 'c. KE = Fd'), (29, 'd', 'd. KE = Pt'),
(30, 'a', 'a. Transverse'), (30, 'b', 'b. Electromagnetic'), (30, 'c', 'c. Longitudinal'), (30, 'd', 'd. Standing');

-- Biology 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(7, 'What is the powerhouse of the cell?', 'c', 'The mitochondrion is known as the powerhouse of the cell.'),
(7, 'Which gas do plants take in during photosynthesis?', 'b', 'Plants take in carbon dioxide during photosynthesis.'),
(7, 'What is the basic unit of life?', 'a', 'The cell is the basic unit of life.'),
(7, 'Which organ pumps blood in the human body?', 'd', 'The heart pumps blood in the human body.'),
(7, 'What molecule carries genetic information?', 'b', 'DNA carries genetic information.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(31, 'a', 'a. Nucleus'), (31, 'b', 'b. Ribosome'), (31, 'c', 'c. Mitochondrion'), (31, 'd', 'd. Chloroplast'),
(32, 'a', 'a. Oxygen'), (32, 'b', 'b. Carbon dioxide'), (32, 'c', 'c. Nitrogen'), (32, 'd', 'd. Hydrogen'),
(33, 'a', 'a. Cell'), (33, 'b', 'b. Tissue'), (33, 'c', 'c. Organ'), (33, 'd', 'd. Organism'),
(34, 'a', 'a. Lungs'), (34, 'b', 'b. Liver'), (34, 'c', 'c. Kidney'), (34, 'd', 'd. Heart'),
(35, 'a', 'a. RNA'), (35, 'b', 'b. DNA'), (35, 'c', 'c. Protein'), (35, 'd', 'd. Lipid');

-- Biology 2015
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(8, 'What process produces gametes?', 'c', 'Meiosis produces gametes (sperm and egg cells).'),
(8, 'Which part of the plant conducts photosynthesis?', 'b', 'Leaves conduct photosynthesis in plants.'),
(8, 'What is the largest organ in the human body?', 'd', 'The skin is the largest organ in the human body.'),
(8, 'Which blood cells fight infection?', 'a', 'White blood cells fight infection.'),
(8, 'What gas is released during respiration?', 'c', 'Carbon dioxide is released during respiration.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(36, 'a', 'a. Mitosis'), (36, 'b', 'b. Binary fission'), (36, 'c', 'c. Meiosis'), (36, 'd', 'd. Budding'),
(37, 'a', 'a. Roots'), (37, 'b', 'b. Leaves'), (37, 'c', 'c. Stem'), (37, 'd', 'd. Flowers'),
(38, 'a', 'a. Liver'), (38, 'b', 'b. Heart'), (38, 'c', 'c. Lungs'), (38, 'd', 'd. Skin'),
(39, 'a', 'a. White blood cells'), (39, 'b', 'b. Red blood cells'), (39, 'c', 'c. Platelets'), (39, 'd', 'd. Plasma'),
(40, 'a', 'a. Oxygen'), (40, 'b', 'b. Nitrogen'), (40, 'c', 'c. Carbon dioxide'), (40, 'd', 'd. Helium');

-- Biology 2014
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(9, 'What is the green pigment in plants?', 'b', 'Chlorophyll is the green pigment in plants.'),
(9, 'Which system controls body functions?', 'c', 'The nervous system controls body functions.'),
(9, 'What is the primary source of energy for living organisms?', 'd', 'The Sun is the primary source of energy for living organisms.'),
(9, 'Which organ filters blood?', 'a', 'The kidneys filter blood.'),
(9, 'What process converts food into energy?', 'c', 'Cellular respiration converts food into energy.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(41, 'a', 'a. Carotene'), (41, 'b', 'b. Chlorophyll'), (41, 'c', 'c. Hemoglobin'), (41, 'd', 'd. Melanin'),
(42, 'a', 'a. Digestive'), (42, 'b', 'b. Circulatory'), (42, 'c', 'c. Nervous'), (42, 'd', 'd. Respiratory'),
(43, 'a', 'a. Water'), (43, 'b', 'b. Soil'), (43, 'c', 'c. Air'), (43, 'd', 'd. Sun'),
(44, 'a', 'a. Kidneys'), (44, 'b', 'b. Liver'), (44, 'c', 'c. Lungs'), (44, 'd', 'd. Heart'),
(45, 'a', 'a. Photosynthesis'), (45, 'b', 'b. Digestion'), (45, 'c', 'c. Cellular respiration'), (45, 'd', 'd. Fermentation');

-- Mathematics 2016
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(10, 'What is 2 + 3?', 'c', '2 + 3 equals 5.'),
(10, 'What is the square root of 16?', 'b', 'The square root of 16 is 4.'),
(10, 'What is the value of π (pi) approximately?', 'd', 'The value of π is approximately 3.14.'),
(10, 'What is 5 × 6?', 'a', '5 × 6 equals 30.'),
(10, 'What is the slope of a horizontal line?', 'c', 'The slope of a horizontal line is 0.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(46, 'a', 'a. 4'), (46, 'b', 'b. 6'), (46, 'c', 'c. 5'), (46, 'd', 'd. 7'),
(47, 'a', 'a. 3'), (47, 'b', 'b. 4'), (47, 'c', 'c. 5'), (47, 'd', 'd. 6'),
(48, 'a', 'a. 3.12'), (48, 'b', 'b. 3.13'), (48, 'c', 'c. 3.15'), (48, 'd', 'd. 3.14'),
(49, 'a', 'a. 30'), (49, 'b', 'b. 25'), (49, 'c', 'c. 35'), (49, 'd', 'd. 40'),
(50, 'a', 'a. 1'), (50, 'b', 'b. -1'), (50, 'c', 'c. 0'), (50, 'd', 'd. Undefined');

-- Mathematics 2015
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(11, 'What is 10 - 4?', 'b', '10 - 4 equals 6.'),
(11, 'What is the area of a square with side 5?', 'd', 'The area of a square is side², so 5² = 25.'),
(11, 'What is 12 ÷ 3?', 'c', '12 ÷ 3 equals 4.'),
(11, 'What is the value of 2³?', 'a', '2³ equals 8.'),
(11, 'What is the perimeter of a rectangle with length 4 and width 3?', 'c', 'Perimeter = 2(length + width) = 2(4 + 3) = 14.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(51, 'a', 'a. 5'), (51, 'b', 'b. 6'), (51, 'c', 'c. 7'), (51, 'd', 'd. 8'),
(52, 'a', 'a. 15'), (52, 'b', 'b. 20'), (52, 'c', 'c. 30'), (52, 'd', 'd. 25'),
(53, 'a', 'a. 3'), (53, 'b', 'b. 5'), (53, 'c', 'c. 4'), (53, 'd', 'd. 6'),
(54, 'a', 'a. 8'), (54, 'b', 'b. 6'), (54, 'c', 'c. 4'), (54, 'd', 'd. 10'),
(55, 'a', 'a. 12'), (55, 'b', 'b. 16'), (55, 'c', 'c. 14'), (55, 'd', 'd. 10');

-- Mathematics 2014
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES 
(12, 'What is 7 + 8?', 'c', '7 + 8 equals 15.'),
(12, 'What is the value of 3² + 4²?', 'd', '3² + 4² = 9 + 16 = 25.'),
(12, 'What is 20 ÷ 5?', 'b', '20 ÷ 5 equals 4.'),
(12, 'What is the area of a circle with radius 2? (use π = 3.14)', 'a', 'Area = πr² = 3.14 × 2² = 12.56.'),
(12, 'What is the value of x in 2x = 10?', 'c', '2x = 10, so x = 5.');

INSERT INTO options (question_id, option_value, option_text) VALUES 
(56, 'a', 'a. 14'), (56, 'b', 'b. 16'), (56, 'c', 'c. 15'), (56, 'd', 'd. 17'),
(57, 'a', 'a. 20'), (57, 'b', 'b. 22'), (57, 'c', 'c. 24'), (57, 'd', 'd. 25'),
(58, 'a', 'a. 3'), (58, 'b', 'b. 4'), (58, 'c', 'c. 5'), (58, 'd', 'd. 6'),
(59, 'a', 'a. 12.56'), (59, 'b', 'b. 12.00'), (59, 'c', 'c. 13.00'), (59, 'd', 'd. 14.00'),
(60, 'a', 'a. 4'), (60, 'b', 'b. 6'), (60, 'c', 'c. 5'), (60, 'd', 'd. 7');
