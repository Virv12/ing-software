<?php
require_once('utils/Appello.php');

if (isset($_GET["matricole"])) {
    $matricole = explode("\n", $_GET["matricole"]);
    $matricole = array_map("trim", $matricole);
    $matricole = array_values(array_filter($matricole));

    $appello = new Appello($matricole, $_GET["data_laurea"], $_GET["cdl"]);
    $res = $appello->generaProspetti();
    if ($res) {
        $msg = "i prospetti sono stati generati";
        $appello->salva();
    } else {
        $msg = "errore nella generazione dei prospetti";
    }
}
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
        <p><?= $msg ?></p>
        <a id="go-back" href=".">Torna alla pagina principale</a>
    </div>
</body>
</html>
