<?php
require_once('utils/Configurazione.php');

class TestConfigurazione extends Test
{
    public function load(): void
    {
        $cfg = Configurazione::load();
        if (count($cfg->esamiInformatici) != 10) {
            throw new Exception("numero esami_informatici non corretto");
        }
        if ($cfg->esamiInformatici[0] != "FONDAMENTI DI PROGRAMMAZIONE") {
            throw new Exception("esami_informatici non caricati correttamente");
        }

        if (count($cfg->corsiDiLaurea) != 9) {
            throw new Exception("numero corsi_di_laurea non corretto");
        }

        $ingInf = $cfg->corsiDiLaurea["T. Ing. Informatica"];
        if ($ingInf->nome != "T. Ing. Informatica") {
            throw new Exception("nome non caricato correttamente");
        }
        if ($ingInf->formula != "\$M * 3 + 18 + \$T + \$C") {
            throw new Exception("formula non caricata correttamente");
        }
        if ($ingInf->cfuRichiesti != 177) {
            throw new Exception("cfuRichiesti non caricati correttamente");
        }
        if ($ingInf->Tmin != 0) {
            throw new Exception("Tmin non caricato correttamente");
        }
        if ($ingInf->Tmax != 0) {
            throw new Exception("Tmax non caricato correttamente");
        }
        if ($ingInf->Tstep != 0) {
            throw new Exception("Tstep non caricato correttamente");
        }
        if ($ingInf->Cmin != 1) {
            throw new Exception("Cmin non caricato correttamente");
        }
        if ($ingInf->Cmax != 7) {
            throw new Exception("Cmax non caricato correttamente");
        }
        if ($ingInf->Cstep != 1) {
            throw new Exception("Cstep non caricato correttamente");
        }
    }
}
