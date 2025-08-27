CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'user';

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
    UNIQUE (job_id, work_order_number)
);

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

CREATE TABLE IF NOT EXISTS door_parts (
    id SERIAL PRIMARY KEY,
    manufacturer VARCHAR(255) NOT NULL,
    system VARCHAR(255) NOT NULL,
    part_number VARCHAR(255) NOT NULL,
    lx NUMERIC,
    ly NUMERIC,
    lz NUMERIC,
    function VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS door_part_requirements (
    id SERIAL PRIMARY KEY,
    part_id INTEGER REFERENCES door_parts(id) ON DELETE CASCADE,
    required_part_id INTEGER REFERENCES door_parts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS door_configurations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    hinge_rail_id INTEGER REFERENCES door_parts(id),
    lock_rail_id INTEGER REFERENCES door_parts(id),
    top_rail_id INTEGER REFERENCES door_parts(id),
    bottom_rail_id INTEGER REFERENCES door_parts(id)
);

INSERT INTO users (email, password, first_name, last_name, role) VALUES
('jonk@vosglass.com', '$2y$12$tjzQUJSfUPYl0zv78yK0PeB46dApBH3ox6xIndP4Fc6HgZV2XsODe', 'Jon', 'K', 'admin'),
('adama@example.com', '$2y$12$MmSdJZgZrqIbXU0cfGWL3OS9IEcGwxfYUIXjPZxCYTiPjsou6Ljce', 'Adam', 'A', 'project_manager'),
('kevink@example.com', '$2y$10$w1WAnbcWcCYwiVdc0GqORu0Yv7FpC18m0tHbdD.N14Q6gttYVOlBm', 'Kevin', 'K', 'fabricator')
ON CONFLICT (email) DO NOTHING;

INSERT INTO jobs (job_name, job_number, project_manager) VALUES
('Example Job', '1001', (SELECT id FROM users WHERE email='adama@example.com'))
ON CONFLICT (job_number) DO NOTHING;
