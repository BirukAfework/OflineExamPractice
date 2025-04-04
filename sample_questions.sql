-- Sample SQL file to insert an exam with questions for Chemistry 2025

-- Insert exam year
INSERT INTO exam_years (year, subject_id, duration_minutes) VALUES (2025, 1, 120);

-- Get the last inserted exam_year_id (assumes it's the highest ID; adjust if needed)
SET @exam_year_id = LAST_INSERT_ID();

-- Insert questions
INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation, image_path) VALUES 
(@exam_year_id, 'What is the chemical formula for water?', 'b', 'H2O represents two hydrogen atoms and one oxygen atom.', NULL),
(@exam_year_id, 'Identify the element in the image.', 'c', 'The periodic table shows Carbon at atomic number 6.', 'uploads/carbon_image.jpg');

-- Insert options for first question
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'What is the chemical formula for water?' AND exam_year_id = @exam_year_id), 'a', 'HO'),
((SELECT id FROM questions WHERE question_text = 'What is the chemical formula for water?' AND exam_year_id = @exam_year_id), 'b', 'H2O'),
((SELECT id FROM questions WHERE question_text = 'What is the chemical formula for water?' AND exam_year_id = @exam_year_id), 'c', 'H2O2'),
((SELECT id FROM questions WHERE question_text = 'What is the chemical formula for water?' AND exam_year_id = @exam_year_id), 'd', 'H3O');

-- Insert options for second question
INSERT INTO options (question_id, option_value, option_text) VALUES 
((SELECT id FROM questions WHERE question_text = 'Identify the element in the image.' AND exam_year_id = @exam_year_id), 'a', 'Hydrogen'),
((SELECT id FROM questions WHERE question_text = 'Identify the element in the image.' AND exam_year_id = @exam_year_id), 'b', 'Oxygen'),
((SELECT id FROM questions WHERE question_text = 'Identify the element in the image.' AND exam_year_id = @exam_year_id), 'c', 'Carbon'),
((SELECT id FROM questions WHERE question_text = 'Identify the element in the image.' AND exam_year_id = @exam_year_id), 'd', 'Nitrogen');