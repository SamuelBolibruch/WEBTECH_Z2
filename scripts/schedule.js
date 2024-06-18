var modal = document.getElementById("editModal");
var modalAdd = document.getElementById("addModal");
var scheduleData;
var addButton = document.getElementById("addButton");

document.addEventListener('DOMContentLoaded', function () {
    var closeBtn = document.getElementById("editModal").getElementsByClassName("close")[0];
    var closeAddBtn = document.getElementById("addModal").getElementsByClassName("close")[0];

    addButton.onclick = function () {
        document.getElementById("add_submit").value = "Pridať";
        modalAdd.style.display = 'block';
    }

    // Pridanie udalosti click na tlačidlo "close"
    closeBtn.onclick = function () {
        // Vymažte text z každého poľa formulára
        var inputs = document.getElementById("myForm").getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = "";
        }

        // Zneviditeľniť modálne okno
        modal.style.display = "none";
    };

    closeAddBtn.onclick = function () {
        // Vymažte text z každého poľa formulára
        var inputs = document.getElementById("myAddForm").getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = "";
        }

        // Zneviditeľniť modálne okno
        modalAdd.style.display = "none";
    };

    document.getElementById("myForm").addEventListener("submit", function (event) {
        event.preventDefault();

        if (checkEditFormData()) {
            modal.style.display = 'none';
        } else {
            console.error("Chyba: Neplatná data.");
        }
    });

    document.getElementById("myAddForm").addEventListener("submit", function (event) {
        event.preventDefault();

        if (checkAddFormData()) {
            modalAdd.style.display = 'none';
        } else {
            console.error("Chyba: Neplatné data.");
        }
    });

    loadSchedule();
});

function checkAddFormData() {
    var subject = document.getElementById("subjectAdd").value;
    var room = document.getElementById("roomAdd").value;
    var day = document.getElementById("dayAdd").value;
    var teacher = document.getElementById("teacherAdd").value;
    var time_from = document.getElementById("time_fromAdd").value;
    var time_to = document.getElementById("time_toAdd").value;
    var type_of_subject = document.getElementById("type_of_subjectAdd").value;
    var id = document.getElementById("scheduleIdAdd").value;

    var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

    // Vytvoření objektu s údaji formuláře
    var formData = {
        "subject": subject,
        "room": room,
        "day": day,
        "teacher": teacher,
        "time_from": time_from,
        "time_to": time_to,
        "type_of_subject": type_of_subject
    };

    console.log(formData);

    // Kontrola, zda jsou všechny pole vyplněna
    if (subject.trim() === '' || room.trim() === '' || day.trim() === '' || teacher.trim() === '' ||
        time_from.trim() === '' || time_to.trim() === '' || type_of_subject.trim() === '') {
        console.error("Chyba: Všetky polia musia byť vyplnenaé.");
        return false;
    }


    // Kontrola, zda hodnoty time_from a time_to mají formát hh:mm
    if (!timeRegex.test(time_from) || !timeRegex.test(time_to)) {
        console.error("Chyba: Formát času musí být hh:mm.");
        return false;
    }

    fetch('https://node20.webte.fei.stuba.sk/Zadanie2SB/schedule_api.php', {
        method: 'POST', // Použití metody POST pro vytvoření nového záznamu
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData), // Převedení dat na formát JSON
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Chyba pri odosielaní dát na server.');
            }
            loadSchedule();
            console.log('Dáta boli uspešne pridané.');
            // Zde můžete přidat další kód pro zpracování odpovědi serveru, pokud je to potřeba
        })
        .catch(error => {
            console.error('Chyba:', error);
        });


    return true;
}


function checkEditFormData() {
    var subject = document.getElementById("subject").value;
    var room = document.getElementById("room").value;
    var day = document.getElementById("day").value;
    var teacher = document.getElementById("teacher").value;
    var time_from = document.getElementById("time_from").value;
    var time_to = document.getElementById("time_to").value;
    var type_of_subject = document.getElementById("type_of_subject").value;
    var id = document.getElementById("scheduleId").value;

    // Regulární výrazy pro kontrolu formátu času hh:mm
    var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

    // Kontrola, zda jsou všechny hodnoty neprázdné řetězce
    if (subject.trim() === '' || room.trim() === '' || day.trim() === '' || teacher.trim() === '' ||
        time_from.trim() === '' || time_to.trim() === '' || type_of_subject.trim() === '' || id.trim() === '') {
        console.error("Chyba: Všechna pole musí být vyplněna.");
        return false;
    }

    // Kontrola, zda hodnoty time_from a time_to mají formát hh:mm
    if (!timeRegex.test(time_from) || !timeRegex.test(time_to)) {
        console.error("Chyba: Formát času musí být hh:mm.");
        return false;
    }

    // Vytvoření objektu s údaji formuláře
    var formData = {
        "subject": subject,
        "room": room,
        "day": day,
        "teacher": teacher,
        "time_from": time_from,
        "time_to": time_to,
        "type_of_subject": type_of_subject
    };

    // Odeslání dat na server v JSON formátu pomocí PUT metody
    fetch('https://node20.webte.fei.stuba.sk/Zadanie2SB/schedule_api.php?id=' + id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Chyba pri odosielaní dát na server.');
            }
            loadSchedule();
            console.log('Dáta boli uspešne zmenené.');
            // Zde můžete přidat další kód pro zpracování odpovědi serveru, pokud je to potřeba
        })
        .catch(error => {
            console.error('Chyba:', error);
        });

    // Pokud všechny kontroly projdou, vrátíme true
    return true;
}


function loadSchedule() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            scheduleData = JSON.parse(this.responseText);
            var tableBody = document.getElementById("scheduleData");
            tableBody.innerHTML = ""; // Vyčistiť obsah tabuľky pred pridaním nových dát
            scheduleData.forEach(function (schedule) {
                var row = tableBody.insertRow();
                row.setAttribute("data-id", schedule.id); // Pridať atribút data-id s identifikátorom záznamu
                row.insertCell().innerText = schedule.subject;
                row.insertCell().innerText = schedule.room;
                row.insertCell().innerText = schedule.day;
                row.insertCell().innerText = schedule.teacher;
                row.insertCell().innerText = schedule.time_from;
                row.insertCell().innerText = schedule.time_to;
                row.insertCell().innerText = schedule.type_of_subject;

                // Pridanie tlačidla "Upraviť" s príslušnou funkcionalitou
                var editCell = row.insertCell();
                var editButton = document.createElement("button");
                editButton.classList.add("edit-button");
                editButton.innerText = "Upraviť";

                editButton.onclick = function () {
                    var rowId = this.parentNode.parentNode.getAttribute("data-id");
                    var selectedSchedule = scheduleData.find(schedule => schedule.id == rowId);
                    if (selectedSchedule) {
                        document.getElementById("subject").value = selectedSchedule.subject;
                        document.getElementById("room").value = selectedSchedule.room;
                        document.getElementById("day").value = selectedSchedule.day;
                        document.getElementById("teacher").value = selectedSchedule.teacher;
                        document.getElementById("time_from").value = selectedSchedule.time_from;
                        document.getElementById("time_to").value = selectedSchedule.time_to;
                        document.getElementById("type_of_subject").value = selectedSchedule.type_of_subject;
                        document.getElementById("scheduleId").value = selectedSchedule.id;

                        document.getElementById("edit_submit").value = "Upraviť";

                        modal.style.display = 'block';
                    } else {
                        console.error("Záznam s ID " + rowId + " nebol nájdený.");
                    }
                };


                editCell.appendChild(editButton);

                // Pridanie tlačidla "Vymazať" s príslušnou funkcionalitou
                var deleteCell = row.insertCell();
                var deleteButton = document.createElement("button");
                deleteButton.classList.add("delete-button");
                deleteButton.innerText = "Vymazať";

                deleteButton.onclick = function () {
                    var confirmation = confirm("Naozaj chcete vymazať tento záznam?");
                    if (confirmation) {
                        // Vykonáme DELETE požiadavku na základe ID
                        var scheduleId = schedule.id;
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                // Aktualizujeme tabuľku po vymazaní záznamu
                                loadSchedule();
                            }
                        };
                        xhttp.open("DELETE", "schedule_api.php?id=" + scheduleId, true);
                        xhttp.send();
                    }
                };

                deleteCell.appendChild(deleteButton);
            });
        }
    };
    xhttp.open("GET", "schedule_api.php", true);
    xhttp.send();
}
