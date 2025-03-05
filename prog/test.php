<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
    <style>
    body {
        background-color: whitesmoke;
    }
    .test-good {
        color: green;
    }
    .test-bad {
        color: red;
    }
    </style>
</head>
<body>
    <?php
    require_once('test/TestGestioneCarrieraStudente.php');
    require_once('test/TestCarrieraLaureando.php');
    require_once('test/TestCarrieraLaureandoInformatica.php');
    require_once('test/TestConfigurazione.php');
    require_once('test/TestProspettoLaureando.php');

    $tests = [
        new TestGestioneCarrieraStudente(),
        new TestCarrieraLaureando(),
        new TestCarrieraLaureandoInformatica(),
        new TestConfigurazione(),
        new TestProspettoLaureando(),
    ];

    foreach ($tests as $test) {
        $test->run_tests();
    }
    ?>
</body>
</html>
