USE spk_ac;

CREATE TABLE users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama     VARCHAR(60)  NOT NULL
);

INSERT INTO users (username, password, nama) VALUES
('admin', '$2y$12$8nll1LtfsWO7KfBaTBuUluh6BCDWb6hmehf5ERmlP/l.dFgxhoa8a', 'Administrator');
