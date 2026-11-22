-- Vehicle Rental System: seed.sql  (run AFTER schema.sql)
-- Demo logins (change them before any real use):
--   admin  / Admin@1234   (role: admin)
--   staff1 / Staff@1234   (role: staff)
-- The password_hash values are real bcrypt hashes, so password_verify() works.
USE vehicle_rental;

INSERT INTO users (username, email, password_hash, role) VALUES
('admin',  'admin@example.com',  '$2b$12$0.WlWr3u0u.gb.Y72XnVJ.JSglSlrr9Mg8HvG8bqHAXTsq9Q8rc1O', 'admin'),
('staff1', 'staff1@example.com', '$2b$12$MX/SYFWD5FFWYDBVAE0cBuvTJe9FKgPzoVOGCantoly/IRcrSbQYK', 'staff');

INSERT INTO vehicles (plate_no, model, type, daily_rate, status) VALUES
('RAB 123 A', 'Toyota RAV4',    'suv',       60.00,  'available'),   -- 1
('RAC 456 B', 'Toyota Corolla', 'car',       35.00,  'rented'),      -- 2 (active rental 1)
('RAD 789 C', 'Toyota HiAce',   'van',       80.00,  'available'),   -- 3
('RAE 321 D', 'Honda CR-V',     'suv',       55.00,  'maintenance'), -- 4
('RAF 654 E', 'Suzuki Swift',   'car',       30.00,  'available'),   -- 5
('RDG 987 F', 'Yamaha MT-15',   'motorbike', 15.00,  'available'),   -- 6
('RAG 111 G', 'Toyota Coaster', 'bus',      150.00,  'rented'),      -- 7 (active rental 5)
('RAH 222 H', 'Hyundai Tucson', 'suv',       65.00,  'available'),   -- 8
('RAJ 333 J', 'Nissan Note',    'car',       32.00,  'available'),   -- 9
('RDK 444 K', 'Bajaj Boxer',    'motorbike', 12.00,  'available');   -- 10

INSERT INTO customers (full_name, license_no, phone, email) VALUES
('Uwase Aline',        'DL-100234', '0788000001', 'aline@example.com'),    -- 1
('Mugisha Eric',       'DL-100235', '0788000002', 'eric@example.com'),     -- 2
('Ineza Grace',        'DL-100236', '0788000003', NULL),                   -- 3
('Nshuti David',       'DL-100237', '0788000004', 'david@example.com'),    -- 4
('Mukamana Chantal',   'DL-100238', '0788000005', 'chantal@example.com'),  -- 5
('Habimana Patrick',   'DL-100239', '0788000006', 'patrick@example.com'),  -- 6
('Uwimana Sandrine',   'DL-100240', '0788000007', NULL),                   -- 7
('Niyonzima Jean',     'DL-100241', '0788000008', 'jean@example.com');     -- 8

-- total_cost = daily_rate x number of days (end - start, minimum 1)
INSERT INTO rentals (vehicle_id, customer_id, start_date, end_date, total_cost, status) VALUES
(2, 1, '2026-11-14', '2026-11-18', 140.00, 'active'),
(1, 2, '2026-11-01', '2026-11-03', 120.00, 'returned'),
(3, 3, '2026-10-20', '2026-10-22', 160.00, 'returned'),
(5, 4, '2026-11-10', '2026-11-11',  30.00, 'cancelled'),
(7, 5, '2026-11-15', '2026-11-20', 750.00, 'active'),
(8, 6, '2026-10-05', '2026-10-08', 195.00, 'returned'),
(6, 7, '2026-10-12', '2026-10-13',  15.00, 'returned'),
(9, 8, '2026-09-28', '2026-10-02', 128.00, 'returned'),
(1, 6, '2026-11-05', '2026-11-07', 120.00, 'returned');
