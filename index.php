<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'api.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $utente = new Utente($username, $password);
    $response = $utente->login();

    if ($response['success']) {
        $_SESSION['token'] = $utente->getToken();
        $_SESSION['ident'] = $utente->getIdent();
        header('Location: dashboard.php');
        exit();
    } else {
        $error = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
