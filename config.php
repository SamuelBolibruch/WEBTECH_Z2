<?php
$host = 'localhost'; // Adresa servera
$dbname = 'zadanie2'; // Názov databázy
$username = 'xbolibruch'; // Užívateľské meno
$password = 'heslo123456789'; // Heslo

try {
    // Vytvorenie spojenia s databázou pomocou PDO a priradenie pripojenia do premennej s názvom "pdo"
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Nastavenie chybového výpisu
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // V prípade chyby vypíšeme chybu
    echo "Chyba pri pripájaní k databáze: " . $e->getMessage();
}
