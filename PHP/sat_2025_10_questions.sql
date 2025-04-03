-- Insert SAT exam for 2025
INSERT INTO exam_years (year, subject_id, duration_minutes) 
VALUES (2025, 6, 180); -- Assuming subject_id 6 is SAT, 180 minutes duration

-- Get the last inserted exam_year_id
SET @exam_year_id = LAST_INSERT_ID();

-- Insert 10 SAT questions
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation, image_path) VALUES 
-- Math (No Calculator)
(@exam_year_id, 'If 2x + 3 = 11, what is the value of x?', 'c', 'Solve: 2x + 3 = 11 => 2x = 8 => x = 4.', NULL),
(@exam_year_id, 'What is the area of a circle with radius 5?', 'b', 'Area = πr^2 = π(5)^2 = 25π.', NULL),

-- Math (Calculator)
(@exam_year_id, 'If a car travels 60 miles in 1.5 hours, what is its average speed in miles per hour?', 'a', 'Speed = Distance / Time = 60 / 1.5 = 40 mph.', NULL),
(@exam_year_id, 'Solve the system: y = 2x + 1, y = x + 3.', 'd', 'Set equal: 2x + 1 = x + 3 => x = 2, y = 5.', NULL),

-- Reading
(@exam_year_id, 'In the sentence "The quick brown fox jumps over the lazy dog," what does "quick" modify?', 'c', '"Quick" is an adjective modifying "fox."', NULL),
(@exam_year_id, 'Based on the image, what is the primary subject of the passage?', 'b', 'The image of a forest suggests the passage discusses ecosystems.', 'uploads/sat_forest_image.jpg'),

-- Writing and Language
(@exam_year_id, 'Which word best replaces "good" in "She did a good job"?', 'a', '"Excellent" is a stronger, more precise synonym for "good" here.', NULL),
(@exam_year_id, 'Identify the grammatically correct sentence:', 'd', '"She runs every day" is simple, correct, and clear.', NULL),

-- Math (Grid-In Style Adapted to Multiple Choice)
(@exam_year_id, 'If x^2 - 9 = 0, what is one possible value of x?', 'c', 'x^2 - 9 = 0 => x^2 = 9 => x = 3 or -3; 3 is listed.', NULL),
(@exam_year_id, 'What is the value of 2^3 * 3^2?', 'b', '2^3 = 8, 3^2 = 9, so 8 * 9 = 72.', NULL);

-- Insert options for each question
-- Q1: 2x + 3 = 11
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'If 2x + 3 = 11, what is the value of x?' AND exam_year_id = @exam_year_id), 'a', '2'),
((SELECT id FROM questions WHERE question_text = 'If 2x + 3 = 11, what is the value of x?' AND exam_year_id = @exam_year_id), 'b', '3'),
((SELECT id FROM questions WHERE question_text = 'If 2x + 3 = 11, what is the value of x?' AND exam_year_id = @exam_year_id), 'c', '4'),
((SELECT id FROM questions WHERE question_text = 'If 2x + 3 = 11, what is the value of x?' AND exam_year_id = @exam_year_id), 'd', '5');

-- Q2: Area of circle
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'What is the area of a circle with radius 5?' AND exam_year_id = @exam_year_id), 'a', '5π'),
((SELECT id FROM questions WHERE question_text = 'What is the area of a circle with radius 5?' AND exam_year_id = @exam_year_id), 'b', '25π'),
((SELECT id FROM questions WHERE question_text = 'What is the area of a circle with radius 5?' AND exam_year_id = @exam_year_id), 'c', '50π'),
((SELECT id FROM questions WHERE question_text = 'What is the area of a circle with radius 5?' AND exam_year_id = @exam_year_id), 'd', '10π');

-- Q3: Average speed
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'If a car travels 60 miles in 1.5 hours, what is its average speed in miles per hour?' AND exam_year_id = @exam_year_id), 'a', '40'),
((SELECT id FROM questions WHERE question_text = 'If a car travels 60 miles in 1.5 hours, what is its average speed in miles per hour?' AND exam_year_id = @exam_year_id), 'b', '45'),
((SELECT id FROM questions WHERE question_text = 'If a car travels 60 miles in 1.5 hours, what is its average speed in miles per hour?' AND exam_year_id = @exam_year_id), 'c', '30'),
((SELECT id FROM questions WHERE question_text = 'If a car travels 60 miles in 1.5 hours, what is its average speed in miles per hour?' AND exam_year_id = @exam_year_id), 'd', '50');

-- Q4: System of equations
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'Solve the system: y = 2x + 1, y = x + 3.' AND exam_year_id = @exam_year_id), 'a', 'x = 1, y = 4'),
((SELECT id FROM questions WHERE question_text = 'Solve the system: y = 2x + 1, y = x + 3.' AND exam_year_id = @exam_year_id), 'b', 'x = 3, y = 6'),
((SELECT id FROM questions WHERE question_text = 'Solve the system: y = 2x + 1, y = x + 3.' AND exam_year_id = @exam_year_id), 'c', 'x = 0, y = 3'),
((SELECT id FROM questions WHERE question_text = 'Solve the system: y = 2x + 1, y = x + 3.' AND exam_year_id = @exam_year_id), 'd', 'x = 2, y = 5');

-- Q5: Reading - Modifier
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'In the sentence "The quick brown fox jumps over the lazy dog," what does "quick" modify?' AND exam_year_id = @exam_year_id), 'a', 'brown'),
((SELECT id FROM questions WHERE question_text = 'In the sentence "The quick brown fox jumps over the lazy dog," what does "quick" modify?' AND exam_year_id = @exam_year_id), 'b', 'jumps'),
((SELECT id FROM questions WHERE question_text = 'In the sentence "The quick brown fox jumps over the lazy dog," what does "quick" modify?' AND exam_year_id = @exam_year_id), 'c', 'fox'),
((SELECT id FROM questions WHERE question_text = 'In the sentence "The quick brown fox jumps over the lazy dog," what does "quick" modify?' AND exam_year_id = @exam_year_id), 'd', 'dog');

-- Q6: Reading - Image-based
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'Based on the image, what is the primary subject of the passage?' AND exam_year_id = @exam_year_id), 'a', 'Urban development'),
((SELECT id FROM questions WHERE question_text = 'Based on the image, what is the primary subject of the passage?' AND exam_year_id = @exam_year_id), 'b', 'Ecosystems'),
((SELECT id FROM questions WHERE question_text = 'Based on the image, what is the primary subject of the passage?' AND exam_year_id = @exam_year_id), 'c', 'Technology'),
((SELECT id FROM questions WHERE question_text = 'Based on the image, what is the primary subject of the passage?' AND exam_year_id = @exam_year_id), 'd', 'History');

-- Q7: Writing - Synonym
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'Which word best replaces "good" in "She did a good job"?' AND exam_year_id = @exam_year_id), 'a', 'excellent'),
((SELECT id FROM questions WHERE question_text = 'Which word best replaces "good" in "She did a good job"?' AND exam_year_id = @exam_year_id), 'b', 'poor'),
((SELECT id FROM questions WHERE question_text = 'Which word best replaces "good" in "She did a good job"?' AND exam_year_id = @exam_year_id), 'c', 'average'),
((SELECT id FROM questions WHERE question_text = 'Which word best replaces "good" in "She did a good job"?' AND exam_year_id = @exam_year_id), 'd', 'terrible');

-- Q8: Writing - Grammar
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'Identify the grammatically correct sentence:' AND exam_year_id = @exam_year_id), 'a', 'She run every day.'),
((SELECT id FROM questions WHERE question_text = 'Identify the grammatically correct sentence:' AND exam_year_id = @exam_year_id), 'b', 'She running every day.'),
((SELECT id FROM questions WHERE question_text = 'Identify the grammatically correct sentence:' AND exam_year_id = @exam_year_id), 'c', 'She runs every days.'),
((SELECT id FROM questions WHERE question_text = 'Identify the grammatically correct sentence:' AND exam_year_id = @exam_year_id), 'd', 'She runs every day.');

-- Q9: Math - Quadratic
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'If x^2 - 9 = 0, what is one possible value of x?' AND exam_year_id = @exam_year_id), 'a', '0'),
((SELECT id FROM questions WHERE question_text = 'If x^2 - 9 = 0, what is one possible value of x?' AND exam_year_id = @exam_year_id), 'b', '9'),
((SELECT id FROM questions WHERE question_text = 'If x^2 - 9 = 0, what is one possible value of x?' AND exam_year_id = @exam_year_id), 'c', '3'),
((SELECT id FROM questions WHERE question_text = 'If x^2 - 9 = 0, what is one possible value of x?' AND exam_year_id = @exam_year_id), 'd', '6');

-- Q10: Math - Exponents
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'What is the value of 2^3 * 3^2?' AND exam_year_id = @exam_year_id), 'a', '18'),
((SELECT id FROM questions WHERE question_text = 'What is the value of 2^3 * 3^2?' AND exam_year_id = @exam_year_id), 'b', '72'),
((SELECT id FROM questions WHERE question_text = 'What is the value of 2^3 * 3^2?' AND exam_year_id = @exam_year_id), 'c', '24'),
((SELECT id FROM questions WHERE question_text = 'What is the value of 2^3 * 3^2?' AND exam_year_id = @exam_year_id), 'd', '48');