<!DOCTYPE html>
<head>
    <title>Test</title>
    <style>
    .test-good {
        color: green;
    }
    .test-bad {
        color: red;
    }
    </style>
</head>
<body style = "background-color: whitesmoke" >
    <?php
    require_once('test/TestGestioneCarrieraStudente.php');
    require_once('test/TestCarrieraLaureando.php');
    require_once('test/TestCarrieraLaureandoInformatica.php');
    require_once('test/TestConfigurazione.php');

    $tests = [
        new TestGestioneCarrieraStudente(),
        new TestCarrieraLaureando(),
        new TestCarrieraLaureandoInformatica(),
        new TestConfigurazione(),
    ];

    foreach ($tests as $test) {
        $test->run_tests();
    }
    ?>
</body>
</html>
