<?php
require_once('utils/Appello.php');

$appello = Appello::carica();
$res = $appello->inviaProspetti();
if ($res) {
    $msg = "i prospetti sono stati inviati";
} else {
    $msg = "errore nell'invio dei prospetti";
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
