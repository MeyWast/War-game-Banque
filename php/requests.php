<?php
require_once('database.php');
$db = dbConnect();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$request = substr($_SERVER['PATH_INFO'], 1);
$request = explode('/', $request);
$requestRessource = array_shift($request);

$data = false;

// Définir la logique des différents points de l'API en fonction de la ressource et de la méthode HTTP
switch ($requestRessource) {
    case 'authentification':
        if ($requestMethod == 'GET') {
            authenticateUser($db);
        }
        break;

    case 'register':
        if ($requestMethod == 'POST') {
            registerUser($db);
        }
        break;

    case 'transaction':
        if ($requestMethod == 'POST') {
            handleTransaction($db);
        }
        break;

    case 'generate-transactions-file':
        if ($requestMethod == 'GET') {
            generateTransactionsFile($db);
        }
        break;

    case 'create-transaction':
        if ($requestMethod == 'POST') {
            handleDeserializationForTransaction($db);
        }
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(['ok' => false, 'messages' => ['Ressource non trouvée']]);
        break;
}

// Fonction pour authentifier un utilisateur
function authenticateUser($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (!$username || !$password) {
        echo json_encode(['ok' => false, 'messages' => ['Username ou mot de passe manquant']]);
        return;
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(['ok' => true, 'messages' => ['Connexion réussie']]);
    } else {
        echo json_encode(['ok' => false, 'messages' => ['username ou mot de passe incorrect']]);
    }
}

// Fonction pour enregistrer un utilisateur
function registerUser($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (!$username || !$password) {
        echo json_encode(['ok' => false, 'messages' => ['username ou mot de passe manquant']]);
        return;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

    echo json_encode(['ok' => true, 'messages' => ['Utilisateur enregistré avec succès']]);
}

// Fonction pour effectuer une transaction
function handleTransaction($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userGive = $data['user_give'] ?? null;
    $userGet = $data['user_get'] ?? null;
    $amount = $data['amount'] ?? null;
    $description = $data['description'] ?? null;

    if (!$userGive || !$userGet || !$amount || !$description) {
        echo json_encode(['ok' => false, 'messages' => ['Données manquantes pour la transaction']]);
        return;
    }

    // Vérifier que l'utilisateur a suffisamment de fonds
    $stmt = $db->prepare("SELECT balance FROM users WHERE id = :userGive");
    $stmt->execute(['userGive' => $userGive]);
    $userGiveBalance = $stmt->fetch(PDO::FETCH_ASSOC)['balance'];

    if ($userGiveBalance < $amount) {
        echo json_encode(['ok' => false, 'messages' => ['Solde insuffisant']]);
        return;
    }

    // Démarrer une transaction pour les mises à jour atomiques
    try {
        $db->beginTransaction();

        // Insérer la transaction dans la table transactions
        $stmt = $db->prepare("INSERT INTO transactions (user_give, user_get, amount, description) 
                               VALUES (:userGive, :userGet, :amount, :description)");
        $stmt->execute([
            'userGive' => $userGive,
            'userGet' => $userGet,
            'amount' => $amount,
            'description' => $description
        ]);

        // Mettre à jour les soldes des utilisateurs
        $stmt = $db->prepare("UPDATE users SET balance = balance - :amount WHERE id = :userGive");
        $stmt->execute(['amount' => $amount, 'userGive' => $userGive]);

        $stmt = $db->prepare("UPDATE users SET balance = balance + :amount WHERE id = :userGet");
        $stmt->execute(['amount' => $amount, 'userGet' => $userGet]);

        // Commit de la transaction
        $db->commit();

        echo json_encode(['ok' => true, 'messages' => ['Transaction effectuée avec succès']]);
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $db->rollBack();
        echo json_encode(['ok' => false, 'messages' => ['Erreur lors de la transaction']]);
    }
}

// Fonction pour générer un fichier de transactions (Path Traversal)
function generateTransactionsFile($db) {
    // Récupérer toutes les transactions
    $stmt = $db->query("SELECT * FROM transactions");
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Construire le contenu du fichier
    $fileContent = "ID,UserGive,UserGet,Amount,Date,Description\n";
    foreach ($transactions as $transaction) {
        $fileContent .= "{$transaction['id']},{$transaction['user_give']},{$transaction['user_get']},{$transaction['amount']},{$transaction['transaction_date']},\"{$transaction['description']}\"\n";
    }

    // Enregistrer le fichier dans 'uploads' pour Path Traversal
    $filePath = __DIR__ . '/uploads/transactions.txt';
    file_put_contents($filePath, $fileContent);

    echo json_encode(['ok' => true, 'messages' => ['Fichier transactions généré'], 'file' => 'transactions.txt']);
}

// Fonction pour désérialiser des données et ajouter une transaction (Insecure Deserialization)
function handleDeserializationForTransaction($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $serializedData = $data['serializedData'] ?? null;
    if (!$serializedData) {
        echo json_encode(['ok' => false, 'messages' => ['Données sérialisées manquantes']]);
        return;
    }

    // Désérialisation vulnérable
    $transaction = json_decode($serializedData, true);

    // Insertion dans la base de données sans validation
    $stmt = $db->prepare("INSERT INTO transactions (user_give, user_get, amount, description) 
                           VALUES (:userGive, :userGet, :amount, :description)");
    $stmt->execute([
        'userGive' => $transaction['user_give'],
        'userGet' => $transaction['user_get'],
        'amount' => $transaction['amount'],
        'description' => $transaction['description']
    ]);

    echo json_encode(['ok' => true, 'messages' => ['Transaction créée avec succès']]);
}

?>
