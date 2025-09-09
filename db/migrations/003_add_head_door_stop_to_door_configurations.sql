ALTER TABLE door_configurations ADD COLUMN head_door_stop_id INTEGER REFERENCES door_parts(id);
