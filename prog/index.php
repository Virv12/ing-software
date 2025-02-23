<?php
require_once('utils/Configurazione.php');
$config = Configurazione::load();
?>
<!DOCTYPE html>
<html lang="it-it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Laureandosi 2.1 - Gestione Prospetti Laurea </title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <div class="container">
        <h2> Gestione Prospetti Laurea </h2>
        <form action="generaProspetti.php" method="get" id="main-form">
            <label for="cdl">
                Cdl:
            </label>
            <select name="cdl" id="cdl" required tabindex="0">
                <option hidden value="">Seleziona un Cdl</option>
                <optgroup label="Corsi di Laurea disponibili">
                    <?php
                    foreach ($config->corsiDiLaurea as $cdl => $value) {
                        $name = htmlspecialchars($cdl);
                        echo "<option>{$name}</option>";
                    }
                    ?>
                </optgroup>
            </select>

            <label for="matricole">
                Matricole:
            </label>
            <textarea
                name="matricole" id="matricole"
                rows="7" cols="15"
                placeholder="Incolla le matricole qui"
                required tabindex="1"></textarea>

            <label for="data_laurea">
                Data Laurea:
            </label>
            <input type="date" name="data_laurea" id="data_laurea" min="<?= date("Y-m-d") ?>" required tabindex="2">

            <button type="submit" tabindex="3">
                Genera Prospetti
            </button>
        </form>

        <form action="inviaProspetti.php" method="get" id="second-form">
            <div class="download-link">
                <a
                    href="run/prospettoCommissione.pdf"
                    target="_blank"
                    tabindex="4">Apri Prospetti</a>
            </div>

            <button type="submit" title="Invia i prospetti appena creati" tabindex="5">
                Invia Prospetti
            </button>
        </form>
    </div>
</body>
</html>
