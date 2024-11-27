<?php
session_start();

if (!isset($_SESSION['token']) || !isset($_SESSION['ident'])) {
    header('Location: index.php');
    exit();
}

require_once 'api.php';

$utente = new Utente();
$utente->setToken($_SESSION['token']);
$utente->setIdent($_SESSION['ident']);

echo $_SESSION['ident'];

// Esempio di richiesta API
$response = $utente->getBacheca();

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Benvenuto</h1>
    <pre><?php echo htmlspecialchars(json_encode($response, JSON_PRETTY_PRINT)); ?></pre>
    <a href="logout.php">Logout</a>
</body>
</html>
