-- Seed data for Kizo SOP Manager (12 Comprehensive SOPs)

USE kizo_sop_manager;

-- Insert Users
INSERT INTO users (name, email, password, role) VALUES
('Admin George', 'admin@kizo.com', '$2y$10$PTXVlPWv6cEuEy0eAbwLauyxqkQwmigSpTGZzd4LbcFUwGytjg34G', 'admin'),
('John Barman', 'john@kizo.com', '$2y$10$PTXVlPWv6cEuEy0eAbwLauyxqkQwmigSpTGZzd4LbcFUwGytjg34G', 'staff_bar'),
('Sarah Chef', 'sarah@kizo.com', '$2y$10$PTXVlPWv6cEuEy0eAbwLauyxqkQwmigSpTGZzd4LbcFUwGytjg34G', 'staff_kitchen'),
('Emma Cafe', 'emma@kizo.com', '$2y$10$PTXVlPWv6cEuEy0eAbwLauyxqkQwmigSpTGZzd4LbcFUwGytjg34G', 'staff_cafe'),
('Mike Cleaner', 'mike@kizo.com', '$2y$10$PTXVlPWv6cEuEy0eAbwLauyxqkQwmigSpTGZzd4LbcFUwGytjg34G', 'staff_cleaning');

-- Insert SOPs
INSERT INTO sops (id, title, category, description, created_by) VALUES
(1, 'Opening SOP – Bar', 'Bar', 'Daily routine to prepare the bar for opening.', 1),
(2, 'Opening SOP – Café', 'Café', 'Morning procedures to open the café.', 1),
(3, 'Kitchen Opening SOP', 'Kitchen', 'Standards for opening the kitchen.', 1),
(4, 'Customer Service SOP', 'Service', 'Standard guidelines for serving customers.', 1),
(5, 'Customer Complaint Handling SOP', 'Service', 'Procedures for handling and logging customer complaints.', 1),
(6, 'Cleaning SOP – Front of House', 'Cleaning', 'Maintaining cleanliness in the customer area.', 1),
(7, 'Cleaning SOP – Bar Area', 'Cleaning', 'Keeping the bar area clean during and after shifts.', 1),
(8, 'Closing SOP – Bar', 'Bar', 'Nightly routine to close the bar.', 1),
(9, 'Closing SOP – Café', 'Café', 'Nightly routine to close the café.', 1),
(10, 'Inventory Check SOP', 'Inventory', 'Counting and recording stock items.', 1),
(11, 'Cash Handling SOP', 'Finance', 'Managing the cash register and daily float.', 1),
(12, 'Staff Check-In SOP', 'Staff', 'Required steps for staff upon arrival.', 1);

-- Insert SOP Steps
INSERT INTO sop_steps (sop_id, step_text, order_index) VALUES
(1, 'Unlock and inspect bar area', 1), (1, 'Turn on lights, POS system, and equipment', 2), (1, 'Check stock levels (alcohol, mixers, garnishes)', 3), (1, 'Refill ice bins', 4), (1, 'Clean bar counter and surfaces', 5), (1, 'Set up glassware (clean and polished)', 6), (1, 'Prepare garnishes (lemon, lime, mint, etc.)', 7), (1, 'Check cash float in register', 8), (1, 'Confirm music system is working', 9), (1, 'Report any shortages or issues', 10),
(2, 'Turn on coffee machine and warm up', 1), (2, 'Check water levels and refill if needed', 2), (2, 'Calibrate grinder', 3), (2, 'Clean counters and prep area', 4), (2, 'Arrange pastries/display items', 5), (2, 'Restock cups, lids, napkins', 6), (2, 'Check POS system', 7), (2, 'Confirm internet connection', 8), (2, 'Review daily menu availability', 9),
(3, 'Wash hands and wear proper uniform', 1), (3, 'Inspect kitchen cleanliness', 2), (3, 'Turn on cooking equipment', 3), (3, 'Check fridge/freezer temperatures', 4), (3, 'Verify ingredient availability', 5), (3, 'Prep basic ingredients (chopping, marination)', 6), (3, 'Label prep items with date/time', 7), (3, 'Ensure waste bins are clean and lined', 8),
(4, 'Greet customer within 10 seconds', 1), (4, 'Present menu clearly', 2), (4, 'Take order accurately', 3), (4, 'Repeat order for confirmation', 4), (4, 'Serve within acceptable time', 5), (4, 'Check back after serving', 6), (4, 'Handle complaints politely', 7), (4, 'Thank customer before departure', 8),
(5, 'Listen without interrupting', 1), (5, 'Apologize sincerely', 2), (5, 'Identify the issue clearly', 3), (5, 'Offer immediate solution (replace/refund/escalate)', 4), (5, 'Inform supervisor if needed', 5), (5, 'Log complaint in system', 6), (5, 'Follow up if necessary', 7),
(6, 'Clean tables and chairs', 1), (6, 'Sweep and mop floor', 2), (6, 'Sanitize high-touch surfaces', 3), (6, 'Empty trash bins', 4), (6, 'Clean restrooms', 5), (6, 'Refill tissue/soap', 6), (6, 'Check overall appearance', 7),
(7, 'Wipe bar counter', 1), (7, 'Clean spills immediately', 2), (7, 'Wash used glassware', 3), (7, 'Sanitize tools (shakers, jiggers)', 4), (7, 'Empty trash', 5), (7, 'Clean sink area', 6),
(8, 'Stop serving at closing time', 1), (8, 'Clean all surfaces', 2), (8, 'Wash and store glassware', 3), (8, 'Dispose of leftover garnishes', 4), (8, 'Count and record cash', 5), (8, 'Turn off equipment', 6), (8, 'Lock alcohol storage', 7), (8, 'Secure premises', 8),
(9, 'Clean coffee machine', 1), (9, 'Dispose of leftover food items', 2), (9, 'Clean display area', 3), (9, 'Wash utensils', 4), (9, 'Restock for next day', 5), (9, 'Turn off equipment', 6), (9, 'Lock storage', 7),
(10, 'Count all stock items', 1), (10, 'Record quantities', 2), (10, 'Identify low-stock items', 3), (10, 'Report shortages', 4), (10, 'Update inventory system', 5),
(11, 'Verify opening float', 1), (11, 'Record all transactions', 2), (11, 'Avoid mixing personal money', 3), (11, 'Count cash at end of shift', 4), (11, 'Report discrepancies', 5), (11, 'Submit report to manager', 6),
(12, 'Arrive on time', 1), (12, 'Clock in / log attendance', 2), (12, 'Wear proper uniform', 3), (12, 'Confirm assigned duties', 4), (12, 'Review daily SOPs', 5);

-- Smart Assignments
INSERT INTO sop_assignments (sop_id, assigned_to_role, frequency, shift) VALUES
(1, 'staff_bar', 'daily', 'morning'),
(2, 'staff_cafe', 'daily', 'morning'),
(3, 'staff_kitchen', 'daily', 'morning'),
(4, 'staff_cafe', 'daily', 'all'),
(5, 'staff_bar', 'daily', 'all'),
(5, 'staff_cafe', 'daily', 'all'),
(6, 'staff_cleaning', 'daily', 'all'),
(7, 'staff_bar', 'daily', 'evening'),
(8, 'staff_bar', 'daily', 'night'),
(9, 'staff_cafe', 'daily', 'night'),
(10, 'staff_kitchen', 'weekly', 'night'),
(11, 'staff_bar', 'daily', 'night'),
(12, 'staff_kitchen', 'daily', 'morning'),
(12, 'staff_cleaning', 'daily', 'morning');
