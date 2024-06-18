<?php

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Rozhodnúť na základe metódy
switch ($method) {
    case 'GET':
        echo getAllRecords($pdo);
        break;
    case 'POST':
        $postData = json_decode(file_get_contents('php://input'), true); // Získať údaje z tela požiadavky vo formáte JSON
        $response = addRecord($pdo, $postData);
        echo $response;
        break;
    case 'DELETE':
        $recordId = isset ($_GET['id']) ? $_GET['id'] : null;
        if ($recordId) {
            $response = deleteRecord($pdo, $recordId);
            echo $response;
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Chýbajúce ID pre odstránenie záznamu."));
        }
        break;
    case 'PUT':
        $putData = json_decode(file_get_contents('php://input'), true); // Získanie údajov z tela požiadavky vo formáte JSON
        $recordId = isset ($_GET['id']) ? $_GET['id'] : null; // Získanie ID záznamu z URL
        if ($recordId) {
            $response = updateRecord($pdo, $recordId, $putData);
            echo $response;
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Chýbajúce ID pre úpravu záznamu."));
        }
        break;
    default:
        // Ak bola použitá nepodporovaná metóda, vrátiť chybu
        http_response_code(405); // Metóda nie je povolená
        echo json_encode(array("message" => "Metóda nie je povolená"));
        break;
}

function getAllRecords($db)
{
    try {
        $sql = "SELECT * FROM schedule";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch všetky riadky ako asociatívne pole

        if (empty ($results)) {
            http_response_code(404); // Not Found
            return json_encode(array("message" => "Žiadne záznamy neboli nájdené."));
        } else {
            http_response_code(200); // Not Found
            return json_encode($results); // Skonvertovať výsledky do formátu JSON
        }
    } catch (PDOException $e) {
        http_response_code(500); // Interná chyba servera
        return json_encode(array("message" => "Chyba: " . $e->getMessage()));
    }
}

function addRecord($db, $data)
{
    // Skontrolujte, či boli dáta úspešne dekódované zo vstupného tela požiadavky
    if ($data === null) {
        http_response_code(400); // Bad Request
        return json_encode(array("message" => "Chýbajú dáta vo formáte JSON."));
    }

    // Skontrolujte, či sú k dispozícii potrebné údaje pre pridanie záznamu
    if (!isset ($data['subject']) || !isset ($data['room']) || !isset ($data['day']) || !isset ($data['teacher']) || !isset ($data['time_from']) || !isset ($data['time_to']) || !isset ($data['type_of_subject'])) {
        http_response_code(400); // Bad Request
        return json_encode(array("message" => "Chýbajúce údaje pre pridanie záznamu."));
    }

    // Spracujte údaje z POST požiadavky
    $subject = $data['subject'];
    $room = $data['room'];
    $day = $data['day'];
    $teacher = $data['teacher'];
    $time_from = $data['time_from'];
    $time_to = $data['time_to'];
    $type_of_subject = $data['type_of_subject'];

    try {
        // Pripravte SQL dotaz pre vloženie nového záznamu
        $sql = "INSERT INTO schedule (subject, room, day, teacher, time_from, time_to, type_of_subject) VALUES (:subject, :room, :day, :teacher, :time_from, :time_to, :type_of_subject)";
        $stmt = $db->prepare($sql);

        // Vložte hodnoty do dotazu a vykonajte ho
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':room', $room);
        $stmt->bindParam(':day', $day);
        $stmt->bindParam(':teacher', $teacher);
        $stmt->bindParam(':time_from', $time_from);
        $stmt->bindParam(':time_to', $time_to);
        $stmt->bindParam(':type_of_subject', $type_of_subject);

        $stmt->execute();

        // Vráťte úspešnú správu
        http_response_code(201); // Created
        return json_encode(array("message" => "Záznam bol úspešne pridaný."));
    } catch (PDOException $e) {
        // Ak nastane chyba pri vykonávaní dotazu, vráťte chybovú správu
        http_response_code(500); // Interná chyba servera
        return json_encode(array("message" => "Chyba pri pridávaní záznamu: " . $e->getMessage()));
    }
}

function deleteRecord($db, $id)
{
    try {
        // Pripraviť SQL dotaz pre odstránenie záznamu s daným ID
        $sql = "DELETE FROM schedule WHERE id = :id";
        $stmt = $db->prepare($sql);

        // Vložiť hodnotu ID do dotazu a vykonať ho
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Skontrolovať, či bol záznam vymazaný
        if ($stmt->rowCount() > 0) {
            http_response_code(200); // OK
            return json_encode(array("message" => "Záznam bol úspešne odstránený."));
        } else {
            http_response_code(404); // Not Found
            return json_encode(array("message" => "Záznam s daným ID nebol nájdený."));
        }
    } catch (PDOException $e) {
        http_response_code(500); // Interná chyba servera
        return json_encode(array("message" => "Chyba pri odstraňovaní záznamu: " . $e->getMessage()));
    }
}

function updateRecord($db, $id, $data)
{
    try {
        // Pripraviť SQL dotaz pre aktualizáciu záznamu s daným ID
        $sql = "UPDATE schedule SET subject = :subject, room = :room, day = :day, teacher = :teacher, time_from = :time_from, time_to = :time_to, type_of_subject = :type_of_subject WHERE id = :id";
        $stmt = $db->prepare($sql);

        // Vložiť hodnoty do dotazu a vykonať ho
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':room', $data['room']);
        $stmt->bindParam(':day', $data['day']);
        $stmt->bindParam(':teacher', $data['teacher']);
        $stmt->bindParam(':time_from', $data['time_from']);
        $stmt->bindParam(':time_to', $data['time_to']);
        $stmt->bindParam(':type_of_subject', $data['type_of_subject']);
        $stmt->execute();

        // Skontrolovať, či bola aktualizácia úspešná
        if ($stmt->rowCount() > 0) {
            http_response_code(200); // OK
            return json_encode(array("message" => "Záznam bol úspešne aktualizovaný."));
        } else {
            http_response_code(404); // Not Found
            return json_encode(array("message" => "Záznam s daným ID nebol nájdený."));
        }
    } catch (PDOException $e) {
        http_response_code(500); // Interná chyba servera
        return json_encode(array("message" => "Chyba pri aktualizácii záznamu: " . $e->getMessage()));
    }
}

