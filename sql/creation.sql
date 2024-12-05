-- Création de la table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    balance NUMERIC(10, 2) DEFAULT 0.00
);

-- Insertion d'utilisateurs avec des soldes
INSERT INTO users (username, password, balance)
VALUES 
('admin', crypt('password123', gen_salt('bf')), 5000000.00), -- Solde initial de 500
('alexandre', crypt('password123', gen_salt('bf')), 100.00), -- Solde initial de 100
('clement', crypt('password123', gen_salt('bf')), 200.00); -- Solde initial de 200

-- Création de la table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_give INTEGER REFERENCES users(id),
    user_get INTEGER REFERENCES users(id),
    amount NUMERIC(10, 2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT
);

-- Ajout d'une transaction entre l'utilisateur 1 et l'utilisateur 2
-- Transfert d'argent de 'user1@example.com' à 'user2@example.com'


INSERT INTO transactions (user_give, user_get, amount, description)
VALUES 
(2, 3, 50.00, 'Transfert de 50 de user 2 à user 3');, 


-- Création de la table des fichiers (pour Path Traversal)
CREATE TABLE IF NOT EXISTS files (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    content TEXT NOT NULL
);

-- Insertion de fichiers de test (pour Path Traversal)
INSERT INTO files (filename, content)
VALUES 
('Transactions 1.txt', 'Transfert de 50 de user 2 à user 3'),
('../../etc/passwd', 'root:x:0:0:root:/root:/bin/bash');
