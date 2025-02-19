<?php
require_once('utils/CarrieraLaureandoInformatica.php');

class TestCarrieraLaureandoInformatica extends Test
{
    public function costruttore(): void
    {
        $val = new CarrieraLaureandoInformatica("123456", "T. Ing. Informatica", "2024-01-05");
        $aspettato = false;
        if ($aspettato != $val->getBonus()) {
            throw new Exception("bonus non calcolato correttamente");
        }

        $val1 = new CarrieraLaureandoInformatica("123456", "T. Ing. Informatica", "2018-01-05");
        $aspettato1 = "SI";
        if ($aspettato1 != $val1->getBonus()) {
            throw new Exception("bonus non calcolato correttamente");
        }
    }

    public function media(): void
    {
        $val = new CarrieraLaureandoInformatica("123456", "T. Ing. Informatica", "2024-01-05");
        $val1 = new CarrieraLaureandoInformatica("123456", "T. Ing. Informatica", "2018-01-05");

        if ($val->getMedia() == $val1->getMedia()) {
            throw new Exception("il bonus non è stato applicato correttamente");
        }
    }
}
