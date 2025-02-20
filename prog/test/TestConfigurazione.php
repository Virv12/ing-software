<?php
require_once('utils/Configurazione.php');

class TestConfigurazione extends Test
{
    public function load(): void
    {
        $cfg = Configurazione::load();
        if (count($cfg->esami_informatici) != 10) {
            throw new Exception("numero esami_informatici non corretto");
        }
        if ($cfg->esami_informatici[0] != "FONDAMENTI DI PROGRAMMAZIONE") {
            throw new Exception("esami_informatici non caricati correttamente");
        }

        if (count($cfg->corsi_di_laurea) != 9) {
            throw new Exception("numero corsi_di_laurea non corretto");
        }

        $ing_inf = $cfg->corsi_di_laurea["T. Ing. Informatica"];
        if ($ing_inf->nome != "T. Ing. Informatica") {
            throw new Exception("nome non caricato correttamente");
        }
        if ($ing_inf->formula != "\$M * 3 + 18 + \$T + \$C") {
            throw new Exception("formula non caricata correttamente");
        }
        if ($ing_inf->cfuRichiesti != 177) {
            throw new Exception("cfuRichiesti non caricati correttamente");
        }
        if ($ing_inf->Tmin != 0) {
            throw new Exception("Tmin non caricato correttamente");
        }
        if ($ing_inf->Tmax != 0) {
            throw new Exception("Tmax non caricato correttamente");
        }
        if ($ing_inf->Tstep != 0) {
            throw new Exception("Tstep non caricato correttamente");
        }
        if ($ing_inf->Cmin != 1) {
            throw new Exception("Cmin non caricato correttamente");
        }
        if ($ing_inf->Cmax != 7) {
            throw new Exception("Cmax non caricato correttamente");
        }
        if ($ing_inf->Cstep != 1) {
            throw new Exception("Cstep non caricato correttamente");
        }
    }
}
