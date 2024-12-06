<?php
// phpinfo();
require_once('database.php');

session_start();

$db = dbConnect();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$request = substr($_SERVER['PATH_INFO'], 1);
$request = explode('/', $request);
$requestRessource = array_shift($request);

$data = false;

switch ($requestRessource) {

    case 'adminPanel':
        if ($requestMethod == 'GET') {
            getadminPanel($db);
        }
        break;

    case 'authentification':
        if ($requestMethod == 'GET') {
            authenticateUser($db, $_GET['username'], $_GET['password']);
        }
        break;

    case 'uploadFile': 
        echo 'je suis un fichier';
        if ($requestMethod == 'POST') {
            echo "je suis dans le if";
            uploadFile($db);
        }
        break;

    case 'register':
        if ($requestMethod == 'POST') {
            registerUser($db, $_POST['username'], $_POST['password']);
        }
        break;

    case 'synthese':
        if ($requestMethod == 'GET') {
            getinfoUser($db, $_SESSION['username']);
        }
        break;

    case 'transaction':
        if ($requestMethod == 'POST') {
            handleTransaction($db, $_POST['user_give'], $_POST['user_get'], $_POST['amount'], $_POST['description']);
        }
        break;

    case 'downloadLogs':
        if ($requestMethod == 'GET') {
            dLFile($db, $_GET['file']);
        }
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(['ok' => false, 'messages' => ['Ressource non trouvée']]);
        break;
}

function authenticateUser($db, $username, $password) {
    if (!$username || !$password) {
        echo json_encode(['ok' => false, 'messages' => ['Veuillez renseigner l\'username et le mot de passe']]);
        return;
    }

    if (base64_encode($username) === 'YWRtaW4=' && base64_encode($password) === 'cGFzc3dvcmQxMjM=') {
        $_SESSION['username'] = $username;
        echo json_encode(['ok' => true, 'messages' => ['flag{do_not_hardcode_credentials}']]);
        return;
    }

    $stmt = $db->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->bindparam(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['ok' => false, 'messages' => ['Utilisateur introuvable']]);
        return;
    }


    if (strlen($row['password']) == 32) {

        $hashedPassword = md5($password);
        if ($hashedPassword == $row['password']) {
            echo json_encode(['ok' => true, 'messages' => ['Authentification réussie']]);
        } else {
            echo json_encode(['ok' => false, 'messages' => ['Authentification échouée']]);
        }
    } else {

        if ($password == $row['password']) {
            echo json_encode(['ok' => true, 'messages' => ['Authentification réussie']]);
        } else {
            echo json_encode(['ok' => false, 'messages' => ['Authentification échouée']]);
        }
    }


    $_SESSION['username'] = $username;
}

function getinfoUser($db, $username) {
    $stmt = $db->prepare("SELECT id, balance FROM users WHERE username = :username");
    $stmt->bindparam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt_tr = $db->prepare("SELECT user_give, amount, description FROM transactions WHERE user_give = :id OR user_get = :id");
    $stmt_tr->bindparam(':id', $result['id']);
    $stmt_tr->execute();
    $row = $stmt_tr->rowCount();
    $transactions = $stmt_tr->fetchAll(PDO::FETCH_ASSOC);

    if($result) {
        echo json_encode(['ok' => true, 'nbTr' => $row, 'user' => $username, 'userid' => $result['id'], 'transactions' => $transactions, 'balance' => $result['balance']]);
    } else {
        echo json_encode(['ok' => false, 'messages' => ['Erreur lors de la récupération du solde']]);
}
}

function registerUser($db, $username, $password) {

    if (!$username || !$password) {
        echo json_encode(['ok' => false, 'messages' => ['username ou mot de passe manquant']]);
        return;
    }

    // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    // $hashedPassword = base64_encode($password);
    $hashedPassword = md5($password);

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindparam(':username', $username);
    $stmt->execute();
    $row = $stmt->rowCount();

    if($row > 0)
    {
        echo json_encode(['ok' => false, 'messages' => ['Cet utilisateur existe déjà']]);
    }
    else
    {
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindparam(':username', $username);
        $stmt->bindparam(':password', $hashedPassword);
        $stmt->execute();
        echo json_encode(['ok' => true, 'messages' => ['Utilisateur enregistré avec succès']]);

        // crée un fichier iban pour l'utilisateur avec un numéro aléatoire 
        $file = fopen("../iban/iban_$username.txt", "w");
        $iban = rand(1000000000000000, 9999999999999999);
        fwrite($file, $iban);
        fclose($file);
    }
}


function dLFile($db, $filePath) {
    // echo $filePath;
    if (file_exists($filePath)) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename=" ' . $filePath . '"');
        readfile($filePath);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo "Fichier introuvable.";
    }
}

function handleTransaction($db, $user_give, $user_get, $amount, $description) {

    $query = "SELECT id, balance FROM users WHERE username = '$user_give'";
    $result = $db->query($query)->fetch(PDO::FETCH_ASSOC);

    $query2 = "SELECT id, balance FROM users WHERE username = '$user_get'";
    $result2 = $db->query($query2)->fetch(PDO::FETCH_ASSOC);

    if ($result && $result2) {
        if ($result['balance'] >= $amount) {

            $query3 = "INSERT INTO transactions (user_give, user_get, amount, description) 
                       VALUES ({$result['id']}, {$result2['id']}, $amount, '$description')";
            $db->exec($query3);

            $query4 = "UPDATE users SET balance = balance - $amount WHERE id = {$result['id']}";
            $db->exec($query4);

            $query5 = "UPDATE users SET balance = balance + $amount WHERE id = {$result2['id']}";
            $db->exec($query5);

            echo json_encode(['ok' => true, 'messages' => ['Transaction effectuée']]);
        } else {
            echo json_encode(['ok' => false, 'messages' => ['Solde insuffisant']]);
        }
    } else {
        echo json_encode(['ok' => false, 'messages' => ['Utilisateur inexistant']]);
    }
}

function getadminPanel($db) {
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($result) {
        echo json_encode(['ok' => true, 'users' => $result]);
    } else {
        echo json_encode(['ok' => false, 'messages' => ['Erreur lors de la récupération des utilisateurs']]);
    }
}

function uploadFile($db) {
    echo 'je suis un fichier upload';
    echo $_FILES['clientFile'];
    if (isset($_FILES['clientFile'])) {
        $file = $_FILES['clientFile'];
        $uploadDir = '../uploads/';
        $uploadPath = $uploadDir . basename($file['name']);
        
        // echo 'je suis dans le isset';
        if (pathinfo($file['name'], PATHINFO_EXTENSION) === 'xml') {
            $xmlContent = file_get_contents($file['tmp_name']);
            echo 'je suis dans le xml 1';
            libxml_disable_entity_loader(false); 
            try
            {
                echo 'avant le dom';
                $dom = new DOMDocument();
                echo 'après le dom';
            }
            catch (Exception $e)
            {
                echo 'je suis dans une erreur';
    
            }

            try {
                $dom->loadXML($xmlContent, LIBXML_NOENT | LIBXML_DTDLOAD);
                $data = simplexml_import_dom($dom);

                $dataArray = json_decode(json_encode($data), true);

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    echo json_encode([
                        'ok' => true, 
                        'message' => 'Fichier XML traité et téléchargé.', 
                        'data' => $dataArray,
                        'filePath' => $uploadPath
                    ]);
                } else {
                    echo json_encode([
                        'ok' => false, 
                        'message' => 'Fichier XML traité mais non téléchargé.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'ok' => false, 
                    'message' => 'Erreur lors du traitement du fichier XML.', 
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                echo json_encode(['ok' => true, 'message' => 'Fichier téléchargé avec succès.']);
            } else {
                echo json_encode(['ok' => false, 'message' => 'Erreur lors du téléchargement du fichier.']);
            }
        }
    } else {
        echo json_encode(['ok' => false, 'message' => 'Aucun fichier fourni.']);
    }
}
