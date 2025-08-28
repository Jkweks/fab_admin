CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'user';
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_dev BOOLEAN NOT NULL DEFAULT FALSE;

CREATE TABLE IF NOT EXISTS jobs (
    id SERIAL PRIMARY KEY,
    job_name VARCHAR(255) NOT NULL,
    job_number VARCHAR(50) UNIQUE NOT NULL,
    project_manager INTEGER REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS work_orders (
    id SERIAL PRIMARY KEY,
    job_id INTEGER REFERENCES jobs(id) ON DELETE CASCADE,
    work_order_number INTEGER NOT NULL,
    material_delivery_date DATE,
    pull_from_stock BOOLEAN DEFAULT FALSE,
    delivered BOOLEAN DEFAULT FALSE,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    UNIQUE (job_id, work_order_number)
);

ALTER TABLE work_orders ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'draft';

CREATE TABLE IF NOT EXISTS work_order_items (
    id SERIAL PRIMARY KEY,
    work_order_id INTEGER REFERENCES work_orders(id) ON DELETE CASCADE,
    item_type VARCHAR(50) NOT NULL,
    elevation VARCHAR(255),
    quantity INTEGER,
    scope VARCHAR(50),
    comments TEXT,
    date_required DATE,
    date_completed DATE,
    completed_by INTEGER REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS manufacturers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS systems (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    manufacturer_id INTEGER REFERENCES manufacturers(id) ON DELETE CASCADE,
    UNIQUE (name, manufacturer_id)
);

ALTER TABLE systems ADD COLUMN IF NOT EXISTS manufacturer_id INTEGER REFERENCES manufacturers(id) ON DELETE CASCADE;
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'systems_name_manufacturer_unique'
    ) THEN
        ALTER TABLE systems
            ADD CONSTRAINT systems_name_manufacturer_unique UNIQUE (name, manufacturer_id);
    END IF;
END $$;

CREATE TABLE IF NOT EXISTS door_parts (
    id SERIAL PRIMARY KEY,
    manufacturer VARCHAR(255) NOT NULL,
    system VARCHAR(255) NOT NULL,
    part_number VARCHAR(255) NOT NULL,
    lx NUMERIC,
    ly NUMERIC,
    lz NUMERIC,
    function VARCHAR(50) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'door'
);

CREATE TABLE IF NOT EXISTS door_part_functions (
    part_id INTEGER REFERENCES door_parts(id) ON DELETE CASCADE,
    function VARCHAR(50) NOT NULL,
    PRIMARY KEY (part_id, function)
);

CREATE TABLE IF NOT EXISTS door_part_requirements (
    id SERIAL PRIMARY KEY,
    part_id INTEGER REFERENCES door_parts(id) ON DELETE CASCADE,
    required_part_id INTEGER REFERENCES door_parts(id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS door_configurations (
    id SERIAL PRIMARY KEY,
    work_order_id INTEGER REFERENCES work_orders(id),
    name VARCHAR(255),
    has_transom BOOLEAN DEFAULT FALSE,
    opening_width NUMERIC,
    opening_height NUMERIC,
    frame_height NUMERIC,
    glazing_thickness VARCHAR(10),
    hinge_rail_id INTEGER REFERENCES door_parts(id),
    lock_rail_id INTEGER REFERENCES door_parts(id),
    top_rail_id INTEGER REFERENCES door_parts(id),
    bottom_rail_id INTEGER REFERENCES door_parts(id)
);

ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS work_order_id INTEGER REFERENCES work_orders(id);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS has_transom BOOLEAN DEFAULT FALSE;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS opening_width NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS opening_height NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS frame_height NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS glazing_thickness VARCHAR(10);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS top_gap NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS bottom_gap NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS hinge_gap NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS latch_gap NUMERIC;
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS handing VARCHAR(20);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS hinge_rail_2_id INTEGER REFERENCES door_parts(id);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS lock_rail_2_id INTEGER REFERENCES door_parts(id);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS top_rail_2_id INTEGER REFERENCES door_parts(id);
ALTER TABLE door_configurations ADD COLUMN IF NOT EXISTS bottom_rail_2_id INTEGER REFERENCES door_parts(id);

INSERT INTO users (email, password, first_name, last_name, role, is_dev) VALUES
('jonk@vosglass.com', '$2y$12$tjzQUJSfUPYl0zv78yK0PeB46dApBH3ox6xIndP4Fc6HgZV2XsODe', 'Jon', 'K', 'admin', TRUE),
('adama@example.com', '$2y$12$MmSdJZgZrqIbXU0cfGWL3OS9IEcGwxfYUIXjPZxCYTiPjsou6Ljce', 'Adam', 'A', 'project_manager', FALSE),
('kevink@example.com', '$2y$10$w1WAnbcWcCYwiVdc0GqORu0Yv7FpC18m0tHbdD.N14Q6gttYVOlBm', 'Kevin', 'K', 'fabricator', FALSE),
('jasonj@example.com', '$2y$10$w1WAnbcWcCYwiVdc0GqORu0Yv7FpC18m0tHbdD.N14Q6gttYVOlBm', 'Jason', 'J', 'fab_leader', FALSE),
('peted@example.com', '$2y$10$w1WAnbcWcCYwiVdc0GqORu0Yv7FpC18m0tHbdD.N14Q6gttYVOlBm', 'Pete', 'D', 'superintendent', FALSE)
ON CONFLICT (email) DO NOTHING;

INSERT INTO jobs (job_name, job_number, project_manager) VALUES
('Example Job', '1001', (SELECT id FROM users WHERE email='adama@example.com'))
ON CONFLICT (job_number) DO NOTHING;
