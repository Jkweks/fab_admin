CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (email, password) VALUES
('jonk@vosglass.com', '$2y$12$tjzQUJSfUPYl0zv78yK0PeB46dApBH3ox6xIndP4Fc6HgZV2XsODe');
