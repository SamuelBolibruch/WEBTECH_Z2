<?php

require_once 'config.php';

$ch = curl_init();
function login($ch, $username, $password): bool
{

    $postValues = array(
        'destination' => '/auth',
        'credential_0' => $username,
        'credential_1' => $password,
        'login' => 'Prihlásiť sa',
        'credential_2' => '86400',
        'credential_cookie' => '1',
        'lang' => 'sk',
    );

    $loginUrl = 'https://is.stuba.sk/auth/';

    // Nastavenie URL a ostatných možností pre cURL
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postValues));
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/cookies.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Vykonanie cURL požiadavky
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

    if ($response && $httpCode == 302) {
        return true;
    } else {
        return false;
    }
}

function getScheduleFromPage($page, $db)
{
    $day = 0;
    $days_of_week = array("Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok");

    $dom = new DOMDocument();
    $dom->loadHTML($page);

    $tables = $dom->getElementsByTagName('table'); // Získať všetky tabuľky zo stránky
    $schedule_table = $tables->item(0); // Získať prvú tabuľku

    if ($schedule_table !== null) {
        $tbody = $schedule_table->getElementsByTagName('tbody')->item(0); // Získať prvý <tbody> element

        if ($tbody !== null) {
            $rows = $tbody->getElementsByTagName('tr'); // Získať všetky riadky z <tbody>
            foreach ($rows as $row) {
                if (trim($row->getAttribute('class')) === 'rozvrh-sep') {
                    $day++;
                    continue; // preskočiť tento riadok a pokračovať s ďalším
                }
                // echo $days_of_week[$day];
                $cells = $row->getElementsByTagName('td'); // Získať všetky bunky v aktuálnom riadku
                $minsFrom8 = 0;

                foreach ($cells as $index => $cell) {

                    // $day_string = '';
                    // $start_time_string = '';
                    // $end_time_string = '';

                    // Tu môžete pracovať s každou bunkou tabuľky
                    if ($cell->getAttribute('colspan') == 22) {
                        $day_string = $days_of_week[$day];

                        $startTime = 8 * 60 + $minsFrom8;
                        $hours = floor($startTime / 60);
                        $minutes = $startTime % 60;
                        $start_time_string = sprintf('%02d:%02d', $hours, $minutes);

                        $minsFrom8 += 22 * 5;
                        $hours = floor(($startTime + 22 * 5) / 60);
                        $minutes = ($startTime + 22 * 5) % 60;
                        $end_time_string = sprintf('%02d:%02d', $hours, $minutes);

                        $subject_name = '';
                        $room_name = '';
                        $teacher = '';
                        $subject_type = '';

                        if ($cell->getAttribute('class') == 'rozvrh-cvic') {
                            $subject_type = "Cvičenie";
                        } else {
                            $subject_type = "Prednáška";
                        }

                        $a_elements = $cell->getElementsByTagName('a');
                        $room_name = $a_elements->item(0)->nodeValue;
                        $subject_name = $a_elements->item(1)->nodeValue;
                        $teacher = $a_elements->item(2)->nodeValue;

                        try {
                            $sql = "INSERT INTO schedule (subject, room, day, teacher, time_from, time_to, type_of_subject) VALUES (:subject, :room, :day, :teacher, :time_from, :time_to, :type_of_subject)";
                            $stmt = $db->prepare($sql);

                            $stmt->bindParam(":subject", $subject_name, PDO::PARAM_STR);
                            $stmt->bindParam(":room", $room_name, PDO::PARAM_STR);
                            $stmt->bindParam(":day", $day_string, PDO::PARAM_STR);
                            $stmt->bindParam(":teacher", $teacher, PDO::PARAM_STR);
                            $stmt->bindParam(":time_from", $start_time_string, PDO::PARAM_STR);
                            $stmt->bindParam(":time_to", $end_time_string, PDO::PARAM_STR);
                            $stmt->bindParam(":type_of_subject", $subject_type, PDO::PARAM_STR);

                            $stmt->execute();
                            unset($stmt);
                            // Váš kód pre vloženie do databázy
                        } catch (PDOException $e) {
                            echo "Chyba: " . $e->getMessage();
                        }
                    }

                    if ($index !== 0 && !$cell->hasAttribute('colspan')) {
                        $minsFrom8 += 5;
                    }
                } // Koniec riadku
            }
        } else {
            echo "Žiadny <tbody> element v tabuľke.";
        }
    } else {
        echo "Tabuľka nebola nájdená.";
    }
}


function getPageContent($ch, $db)
{

    $isSuccessful = login($ch, "******", "******");

    if ($isSuccessful) {
        // Úspešné prihlásenie
        $urlSchedule = "https://is.stuba.sk/auth/katalog/rozvrhy_view.pl?rozvrh_student_obec=1?zobraz=1;format=html;rozvrh_student=115298;zpet=../student/moje_studium.pl?_m=3110,lang=sk,studium=167938,obdobi=630;lang=sk";

        // Údaje formulára na odoslanie
        $formData = array(
            'zobraz' => 'Zobraziť',
            'osobni' => '1',
            'lang' => 'sk',
        );

        // Nastavenie URL a ostatných možností pre cURL
        curl_setopt($ch, CURLOPT_URL, $urlSchedule); // redirectUrl je adresa stránky, kam ste presmerovaní
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData)); // Odoslanie údajov formulára
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/cookies.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Vykonanie cURL požiadavky
        $redirectedResponse = curl_exec($ch);

        $sql = "INSERT INTO sites (name, html) VALUES (:name, :html)";
        $stmt = $db->prepare($sql);

        $name = "rozvrh";
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":html", $redirectedResponse, PDO::PARAM_STR);

        $stmt->execute();

        getScheduleFromPage($redirectedResponse, $db);

        unset($stmt);
        // Zatvorenie cURL spojenia
    } else {
        // Chyba pri prihlásení alebo neúspešný HTTP kód odpovede
        echo "Chyba pri prihlásení alebo neúspešný HTTP kód odpovede.";
    }
}

$sql = "DELETE FROM schedule";
$stmt = $pdo->prepare($sql);
$stmt->execute();

getPageContent($ch, $pdo);
curl_close($ch);
header("Location: index.php");


