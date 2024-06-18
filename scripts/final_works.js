var table = document.getElementById('myTable');
var firstSelect = document.querySelector('select[name="department"]');
var secondSelect = document.querySelector('select[name="type_of_work"]');
var outputDiv = document.getElementById('output');

var tableData;

document.addEventListener('DOMContentLoaded', function () {
    // Funkce pro nastavení stavu druhého selectu
    function setSecondSelectState() {
        // Pokud je vybrána první možnost v prvním selectu (hodnota je prázdná), druhý select bude zakázán a nastaven na možnost "---"
        if (firstSelect.value === "") {
            secondSelect.disabled = true;
            secondSelect.value = ""; // Nastavíme druhý select na hodnotu "---"
        } else {
            secondSelect.disabled = false;
        }
    }

    // Připojení události pro sledování změn v prvním selectu
    document.querySelector('select[name="department"]').addEventListener('change', function () {
        setSecondSelectState();
        // Pokud byla provedena změna v prvním selectu, automaticky nastavíme druhý select na hodnotu "---"
        document.querySelector('select[name="type_of_work"]').value = "";
    });

    // Volání funkce pro nastavení stavu při načtení stránky
    setSecondSelectState();

    document.querySelector('select[name="type_of_work"]').addEventListener('change', function () {
        if (secondSelect.value !== '') {
            fetchFinalWorksData(firstSelect.value, secondSelect.value);
        }
    });

});

function fetchFinalWorksData(idPracoviska, typPrace) {
    // Vytvoření URL s proměnnými idPracoviska a typPrace
    const apiUrl = `final_works_api.php?id=${idPracoviska}&study_type=${typPrace}`;

    // Volání API pro získání dat
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Chyba při získávání dat.');
            }
            return response.json();
        })
        .then(data => {
            if ($.fn.DataTable.isDataTable('#myTable')) {
                // Zavolání metody destroy() na tabulce
                tableData.destroy();
            }

            let thead = table.querySelector('thead');
            if (thead) {
                thead.innerHTML = ''; // Vyčištění obsahu
            } else {
                thead = document.createElement('thead');
                table.appendChild(thead);
            }

            // Vytvoření nového řádku v hlavičce
            const row = thead.insertRow();

            const headerCells = ['Názov', 'Abstrakt', 'Meno školiteľa', 'Štúdijny program'];
            headerCells.forEach(cellText => {
                const th = document.createElement('th');
                th.textContent = cellText;
                if (cellText === 'Abstrakt') {
                    th.hidden = true; // Skrytie stĺpca pre abstrakt
                }
                row.appendChild(th);
            });

            // Získání nebo vytvoření těla tabulky
            let tbody = table.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = ''; // Vyčištění obsahu
            } else {
                tbody = document.createElement('tbody');
                table.appendChild(tbody);
            }

            // Pro každý záznam v datech vytvoříme nový řádek v tabulce
            data.forEach(record => {
                const row = tbody.insertRow();

                const nameCell = row.insertCell();
                nameCell.textContent = record['name'];
                const abstract = record['abstract'];
                nameCell.addEventListener('click', () => {
                    // const abstract = trigger.parentElement.querySelector('td:nth-child(2)').textContent; // Získať abstrakt z rovnakého riadku
                    showModal(abstract); // Zobraziť modálne okno s abstraktom
                });

                const abstractCell = row.insertCell();
                abstractCell.textContent = record['abstract'];
                abstractCell.hidden = true;

                // Vytvoření buňky pro jméno učitele a vložení hodnoty
                const teacherCell = row.insertCell();
                teacherCell.textContent = record['teacher'];

                // Vytvoření buňky pro studijní program a vložení hodnoty
                const programCell = row.insertCell();
                programCell.textContent = record['program'];
            });

            // Inicializace DataTables
            tableData = new DataTable('#myTable');

        })
        .catch(error => {
            // V prípade chyby
            console.error('Chyba pri získavaní údajov:', error);
            outputDiv.textContent = 'Nastala chyba pri získavaní údajov.';
        });
}

function showModal(abstract) {
    var abstractParagraph = document.querySelector('#myModal .modal-content p');
    abstractParagraph.textContent = abstract;
    document.getElementById('myModal').style.display = "block";
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

