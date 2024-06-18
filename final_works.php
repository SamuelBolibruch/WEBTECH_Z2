<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Získanie a vypísanie údajov z API</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/final_works.css">
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
                <a class="nav-item nav-link h4" href="schedule.php">Rozvrh</a>
                <a class="nav-item nav-link active h4" href="final_works.php">Záverečné práce</a>
                <a class="nav-item nav-link h4" href="api_doc.php">API Popis</a>
            </div>
        </div>
    </nav>

    <div class="input-container">
        <select name="department">
            <option value="">---</option>
            <option value="642">Ústav automobilové mechatroniky</option>
            <option value="548">Ústav elektroenergetiky a aplikované elektrotechniky</option>
            <option value="549">Ústav elektroniky a fotoniky</option>
            <option value="550">Ústav elektrotechniky</option>
            <option value="816">Ústav informatiky a matematiky</option>
            <option value="817">Ústav jadrového a fyzikálního inženýrství</option>
            <option value="818">Ústav multimediálních informačních a komunikačních technologií</option>
            <option value="356">Ústav robotiky a kybernetiky</option>
        </select>

        <select name="type_of_work">
            <option value="">---</option>
            <option value="BP">Bakalářská práce</option>
            <option value="DP">Diplomová práce</option>
        </select>
    </div>

    <div class="table-div">
        <table id="myTable">
            <thead>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Abstrakt</h2>
            <p></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
        </script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="scripts/final_works.js"></script>
</body>

</html>