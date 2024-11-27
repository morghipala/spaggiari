<?php
class Utente {
    private $uid;
    private $pwd;
    private $ident;
    private $token;

    private $headers = [
        "User-Agent: CVVS/std/4.1.7 Android/10",
        "Content-Type: application/json",
        "Z-Dev-ApiKey: Tg1NWEwNGIgIC0K"
    ];

    public function __construct($uid = '', $pwd = '') {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    public function login() {
        $url = RequestURLs::LOGIN[0];
        $data = json_encode([
            'ident' => null,
            'pass' => $this->pwd,
            'uid' => $this->uid
        ]);

        $response = $this->sendRequest($url, 'POST', $data);

        if ($response['status_code'] === 200) {
            $this->ident = preg_replace('/\D/', '', $response['data']['ident']);
            $this->token = $response['data']['token'];
            return ['success' => true];
        }

        return ['success' => false, 'message' => $response['data']['message'] ?? 'Errore di login'];
    }

    public function request($urlTuple, $params = null) {
        if (!$this->token || !$this->ident) {
            return ['success' => false, 'message' => 'Non autenticato'];
        }

        $url = sprintf($urlTuple[0], $this->ident, ...($params ?? []));
        return $this->sendRequest($url, strtoupper($urlTuple[1]));
    }

    private function sendRequest($url, $method, $data = null) {
        $ch = curl_init($url);

        $headers = $this->headers;
        if ($this->token) {
            $headers[] = "Z-Auth-Token: $this->token";
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Aggiungi un timeout per evitare blocchi
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Timeout di 30 secondi

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['status_code' => 0, 'data' => ['message' => 'Errore cURL: ' . curl_error($ch)]];
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status_code' => $statusCode,
            'data' => json_decode($response, true)
        ];
    }

    public function getToken() {
        return $this->token;
    }

    public function getIdent() {
        return $this->ident;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setIdent($ident) {
        $this->ident = $ident;
    }

    // === Funzioni per tutti i campi supportati === //
    public function getAssenze() {
        return $this->request(RequestURLs::ASSENZE);
    }

    public function getAgenda($startDate, $endDate) {
        if ($startDate > $endDate) {
            return ['success' => false, 'message' => 'La data iniziale non può essere successiva alla data finale.'];
        }
        if ($startDate < '20240901' || $endDate > '20251231') {
            return ['success' => false, 'message' => 'Le date devono essere comprese tra il 1° settembre 2024 e il 31 dicembre 2025.'];
        }
        return $this->request(RequestURLs::AGENDA, [$startDate, $endDate]);
    }

    public function getBacheca() {
        return $this->request(RequestURLs::BACHECA);
    }

    public function getDidattica() {
        return $this->request(RequestURLs::DIDATTICA);
    }

    public function getLibri() {
        return $this->request(RequestURLs::LIBRI);
    }

    public function getCalendario() {
        return $this->request(RequestURLs::CALENDARIO);
    }

    public function getCard() {
        return $this->request(RequestURLs::CARD);
    }

    public function getVoti() {
        return $this->request(RequestURLs::VOTI);
    }

    public function getLezioniOggi() {
        return $this->request(RequestURLs::LEZIONI_OGGI);
    }

    public function getLezioniGiorno($date) {
        return $this->request(RequestURLs::LEZIONI_GIORNO, [$date]);
    }

    public function getNote() {
        return $this->request(RequestURLs::NOTE);
    }

    public function getPeriods() {
        return $this->request(RequestURLs::PERIODS);
    }

    public function getMaterie() {
        return $this->request(RequestURLs::MATERIE);
    }

    public function getDocumenti() {
        return $this->request(RequestURLs::DOCUMENTI);
    }
}

class RequestURLs {
    const BASE_URL = 'https://web.spaggiari.eu/rest/v1';
    const STUDENTS_URL = self::BASE_URL . '/students/%s';

    const ASSENZE = [self::STUDENTS_URL . '/absences/details', 'GET'];
    const AGENDA = [self::STUDENTS_URL . '/agenda/all/%s/%s', 'GET']; // startDate, endDate
    const DIDATTICA = [self::STUDENTS_URL . '/didactics', 'GET'];
    const LIBRI = [self::STUDENTS_URL . '/schoolbooks', 'GET'];
    const CALENDARIO = [self::STUDENTS_URL . '/calendar/all', 'GET'];
    const CARD = [self::STUDENTS_URL . '/card', 'GET'];
    const VOTI = [self::STUDENTS_URL . '/grades', 'GET'];
    const LEZIONI_OGGI = [self::STUDENTS_URL . '/lessons/today', 'GET'];
    const LEZIONI_GIORNO = [self::STUDENTS_URL . '/lessons/%s', 'GET']; // date
    const NOTE = [self::STUDENTS_URL . '/notes/all', 'GET'];
    const PERIODS = [self::STUDENTS_URL . '/periods', 'GET'];
    const MATERIE = [self::STUDENTS_URL . '/subjects', 'GET'];
    const LOGIN = [self::BASE_URL . '/auth/login', 'POST'];
    const BACHECA = [self::STUDENTS_URL . '/noticeboard', 'GET'];
    const DOCUMENTI = [self::STUDENTS_URL . '/documents', 'POST'];
}
