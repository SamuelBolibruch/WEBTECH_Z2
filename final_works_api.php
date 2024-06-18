<?php

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $workplaceId = isset ($_GET['id']) ? $_GET['id'] : null;
        $studyType = isset ($_GET['study_type']) ? $_GET['study_type'] : null;

        if ($workplaceId && $studyType) {
            $response = getFreeThemes($workplaceId, $studyType);
            echo $response;
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Chýbajúce ID alebo typ štúdia."));
        }
        break;

    default:
        // Ak bola použitá nepodporovaná metóda, vrátiť chybu
        http_response_code(405); // Metóda nie je povolená
        echo json_encode(array("message" => "Metóda nie je povolená"));
        break;
}

function getFreeThemes($workplaceId, $studyType)
{
    // Příklad použití workplaceId
    // Můžete zde provést další zpracování stránky nebo získat potřebné informace podle potřeby
    // Například, můžete upravit URL tak, aby obsahovala workplaceId
    $url = 'https://is.stuba.sk/pracoviste/prehled_temat.pl?lang=sk;pracoviste=' . $workplaceId;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);

    if ($output === false) {
        // Chyba při provádění cURL požadavku
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Chyba při získávání dat z externího zdroje."));
        return;
    }

    curl_close($ch);

    $dom = new DOMDocument();
    $success = @$dom->loadHTML($output); // Zde potlačujeme chyby, ale měli byste lépe zpracovat chyby

    if (!$success) {
        // Chyba při zpracování HTML
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Chyba při zpracování HTML."));
        return;
    }

    // Získání čtvrté tabulky ze stránky
    $tables = $dom->getElementsByTagName('table');
    $fourthTable = null;
    if ($tables->length >= 4) { // Zkontrolovat, jestli existuje alespoň čtvrtá tabulka
        $fourthTable = $tables->item(3); // Čtvrtá tabulka (indexováno od 0)
    }

    if (!$fourthTable) {
        // Čtvrtá tabulka nenalezena
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "Čtvrtá tabulka nenalezena."));
        return;
    }

    // Provést zpracování čtvrté tabulky a získat požadované informace
    $themes = array();
    $tbody = $fourthTable->getElementsByTagName('tbody')->item(0); // Předpokládáme, že existuje pouze jedno <tbody>
    $rows = $tbody->getElementsByTagName('tr'); // Získání řádků pouze z <tbody>
    foreach ($rows as $row) {
        $name = '';
        $teacher = '';
        $workspace = '';
        $program = '';
        $focus = '';
        $abstract = '';

        $cols = $row->getElementsByTagName('td');
        $numberOfColumns = $cols->length;

        if ($numberOfColumns == 11) {
            if ($cols->item(1)->nodeValue == $studyType && checkaAailability($cols->item(9)->nodeValue)) {
                $name = $cols->item(2)->nodeValue;
                $teacher = $cols->item(3)->nodeValue;
                $workspace = $cols->item(4)->nodeValue;
                $program = $cols->item(5)->nodeValue;
                $focus = $cols->item(6)->nodeValue;

                $anchor = $cols->item(8)->getElementsByTagName('a')->item(0);
                $detailURL = 'https://is.stuba.sk' . $anchor->getAttribute('href');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $detailURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $detailPageContent = curl_exec($ch);
                curl_close($ch);

                $detailDOM = new DOMDocument();
                @$detailDOM->loadHTML($detailPageContent);

                $firstTableRows = $detailDOM->getElementsByTagName('table')->item(0)->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
                $targetRow = $firstTableRows->item(10); // Řádek 11 (indexováno od 0)
                $targetColContent = $targetRow->getElementsByTagName('td')->item(1)->nodeValue; // Obsah druhého sloupce v 11. řádku
                $targetColContent = str_replace(array("\r", "\n"), '', $targetColContent);
                $abstract = $targetColContent;

                $theme = array(
                    "name" => $name,
                    "teacher" => $teacher,
                    "workspace" => $workspace,
                    "program" => $program,
                    "focus" => $focus,
                    "abstract" => $abstract
                );

                $themes[] = $theme;
            }
        } else if ($numberOfColumns == 10) {
            if ($cols->item(1)->nodeValue == $studyType && checkaAailability($cols->item(8)->nodeValue)) {
                $name = $cols->item(2)->nodeValue;
                $teacher = $cols->item(3)->nodeValue;
                $workspace = $cols->item(4)->nodeValue;
                $program = $cols->item(5)->nodeValue;
                // $focus = $cols->item(6)->nodeValue;

                $anchor = $cols->item(7)->getElementsByTagName('a')->item(0);
                $detailURL = 'https://is.stuba.sk' . $anchor->getAttribute('href');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $detailURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $detailPageContent = curl_exec($ch);
                curl_close($ch);

                $detailDOM = new DOMDocument();
                @$detailDOM->loadHTML($detailPageContent);

                $firstTableRows = $detailDOM->getElementsByTagName('table')->item(0)->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
                $targetRow = $firstTableRows->item(10); // Řádek 11 (indexováno od 0)
                $targetColContent = $targetRow->getElementsByTagName('td')->item(1)->nodeValue; // Obsah druhého sloupce v 11. řádku
                $targetColContent = str_replace(array("\r", "\n"), '', $targetColContent);
                $abstract = $targetColContent;

                $theme = array(
                    "name" => $name,
                    "teacher" => $teacher,
                    "workspace" => $workspace,
                    "program" => $program,
                    "focus" => $focus,
                    "abstract" => $abstract
                );

                $themes[] = $theme;
            }
        }
    }

    return json_encode($themes);
}

function checkaAailability($input)
{
    // Rozdělení vstupu na číslo1 a číslo2
    $numbers = explode(' / ', $input);

    // Pokud druhé číslo je '--' nebo rozdíl druhého a prvního čísla je kladný, vrátit true, jinak false
    if ($numbers[1] == '--' || ($numbers[1] - $numbers[0] > 0)) {
        return true;
    } else {
        return false;
    }
}
