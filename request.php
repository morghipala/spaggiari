<?php
// Includi il file api.php che contiene la definizione della classe Utente
require_once 'api.php';

// Imposta l'intestazione per dire al browser che il contenuto è in formato JSON
header('Content-Type: application/json');

// Verifica che i parametri siano presenti
if (!isset($_GET['u'], $_GET['p'], $_GET['a'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti.']);
    exit;
}

// Recupera i parametri dalla query string
$username = $_GET['u'];
$password = $_GET['p'];
$azione = $_GET['a'];

// Recupera le date startDate e endDate, se fornite
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Crea un'istanza della classe Utente
$utente = new Utente($username, $password);

// Esegui il login
$loginResult = $utente->login();

// Se il login non ha avuto successo, restituisci un errore
if (!$loginResult['success']) {
    echo json_encode($loginResult);
    exit;
}

// In base all'azione richiesta, esegui la funzione corrispondente
switch ($azione) {
    case 'assenze':
        $result = $utente->getAssenze();
        break;
    case 'agenda':
        // Se le date non sono valide o mancanti, restituisci un errore
        if (!$startDate || !$endDate) {
            echo json_encode(['success' => false, 'message' => 'Le date di inizio e fine sono obbligatorie per l\'agenda.']);
            exit;
        }
        // Verifica che le date siano nel formato corretto (YYYYMMDD) e siano ragionevoli
        if ($startDate > $endDate) {
            echo json_encode(['success' => false, 'message' => 'La data iniziale non può essere successiva alla data finale.']);
            exit;
        }
        if ($startDate < '20240901' || $endDate > '20251231') {
            echo json_encode(['success' => false, 'message' => 'Le date devono essere comprese tra il 1° settembre 2024 e il 31 dicembre 2025.']);
            exit;
        }
        $result = $utente->getAgenda($startDate, $endDate);
        break;
    case 'bacheca':
        $result = $utente->getBacheca();
        break;
    case 'didattica':
        $result = $utente->getDidattica();
        break;
    case 'libri':
        $result = $utente->getLibri();
        break;
    case 'calendario':
        $result = $utente->getCalendario();
        break;
    case 'voti':
        $result = $utente->getVoti();
        break;
    case 'lezioni_oggi':
        $result = $utente->getLezioniOggi();
        break;
    case 'note':
        $result = $utente->getNote();
        break;
    case 'materie':
        $result = $utente->getMaterie();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Azione non valida']);
        exit;
}

// Restituisci il risultato come JSON
echo json_encode($result);
?>
