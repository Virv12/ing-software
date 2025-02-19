<?php
require_once('utils/InvioPDFLaureando.php');

$invio = new InvioPDFLaureando();
$invio->invioProspetti();
$msg = "i prospetti sono stati inviati";
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
