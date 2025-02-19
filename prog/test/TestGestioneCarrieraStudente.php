<?php
require_once('test/Test.php');
require_once('utils/GestioneCarrieraStudente.php');

class TestGestioneCarrieraStudente extends Test
{
    public function restituisciAnagraficaStudente(): void
    {
        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente("123456");
        $nome = $anagrafica["Entries"]["Entry"]["nome"];
        if ($nome != "GIUSEPPE") {
            throw new Exception("Nome non corretto");
        }
    }

    public function restituisciCarrieraStudente(): void
    {
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente("123456");
        $esame = $carriera["Esami"]["Esame"][0]["DES"];
        if ($esame != "ELETTROTECNICA") {
            throw new Exception("Esame non corretto");
        }
    }
}
