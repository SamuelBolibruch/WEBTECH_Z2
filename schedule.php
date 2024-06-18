<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/schedule.css">
    <title>Document</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
            <div class="navbar-nav ">
                <a class="nav-item nav-link h4" href="index.php">Načitaj rozvrh<span
                        class="sr-only">(current)</span></a>
                <a class="nav-item nav-link active h4" href="#">Rozvrh</a>
                <a class="nav-item nav-link h4" href="final_works.php">Záverečné práce</a>
                <a class="nav-item nav-link h4" href="api_doc.php">API Popis</a>
            </div>
        </div>
    </nav>

    <div class="addButtonContainer">
        <button id="addButton">
            Pridať rozvrhovú akciu
        </button>
    </div>

    <div class="page-content">
        <table id="scheduleTable" class="table">
            <thead>
                <tr>
                    <th>Predmet</th>
                    <th>Miestnosť</th>
                    <th>Deň</th>
                    <th>Učiteľ</th>
                    <th>Čas od</th>
                    <th>Čas do</th>
                    <th>Typ predmetu</th>
                    <th>Upraviť</th>
                    <th>Vymazať</th>
                </tr>
            </thead>
            <tbody id="scheduleData"></tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="myForm">
                <!-- Ostatné vstupné polia formulára -->
                <label for="subject">Predmet:</label><br>
                <input type="text" id="subject" name="subject"><br>
                <label for="room">Miestnosť:</label><br>
                <input type="text" id="room" name="room"><br>
                <label for="day">Deň:</label><br>
                <input type="text" id="day" name="day"><br>
                <label for="teacher">Učiteľ:</label><br>
                <input type="text" id="teacher" name="teacher"><br>
                <label for="time_from">Čas od:</label><br>
                <input type="text" id="time_from" name="time_from"><br>
                <label for="time_to">Čas do:</label><br>
                <input type="text" id="time_to" name="time_to"><br>
                <label for="type_of_subject">Typ predmetu:</label><br>
                <input type="text" id="type_of_subject" name="type_of_subject"><br><br>
                <!-- Skryté pole pre uchovanie ID záznamu -->
                <input type="hidden" id="scheduleId" name="scheduleId">
                <!-- Tlačidlo Submit s prázdnou hodnotou -->
                <input id="edit_submit" type="submit" value="Upraviť">
            </form>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="myAddForm">
                <!-- Ostatné vstupné polia formulára -->
                <label for="subject">Predmet:</label><br>
                <input type="text" id="subjectAdd" name="subject"><br>
                <label for="room">Miestnosť:</label><br>
                <input type="text" id="roomAdd" name="room"><br>
                <label for="day">Deň:</label><br>
                <input type="text" id="dayAdd" name="day"><br>
                <label for="teacher">Učiteľ:</label><br>
                <input type="text" id="teacherAdd" name="teacher"><br>
                <label for="time_from">Čas od:</label><br>
                <input type="text" id="time_fromAdd" name="time_from"><br>
                <label for="time_to">Čas do:</label><br>
                <input type="text" id="time_toAdd" name="time_to"><br>
                <label for="type_of_subject">Typ predmetu:</label><br>
                <input type="text" id="type_of_subjectAdd" name="type_of_subject"><br><br>
                <!-- Skryté pole pre uchovanie ID záznamu -->
                <input type="hidden" id="scheduleIdAdd" name="scheduleId">
                <!-- Tlačidlo Submit s prázdnou hodnotou -->
                <input id="add_submit" type="submit" value="Upraviť">
            </form>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="scripts/schedule.js"></script>
</body>

</html>