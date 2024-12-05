-- Création de la table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    balance NUMERIC(10, 2) DEFAULT 0.00,
    iban numeric(27, 0) DEFAULT 0
);

-- Insertion d'utilisateurs avec des soldes
INSERT INTO users (username, password, balance, iban)
VALUES 
('admin', 'password123', 500.00, 12345), -- Solde initial de 500
('alexandre', 'password123', 100.00, 12345), -- Solde initial de 100
('clement', 'password123', 200.00, 12345);   -- Solde initial de 200

-- Création de la table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_give INTEGER REFERENCES users(id),
    user_get INTEGER REFERENCES users(id),
    amount NUMERIC(10, 2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT
);

INSERT INTO transactions (user_give, user_get, amount, description)
VALUES 
(2, 3, 50.00, 'Transfert de 50 de user 2 à user 3');


CREATE TABLE IF NOT EXISTS files (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    content TEXT NOT NULL
);

INSERT INTO files (filename, content)
VALUES 
('Transactions 1.txt', 'Transfert de 50 de user 2 à user 3'),
('../../etc/passwd', 'root:x:0:0:root:/root:/bin/bash');
