<?php
require_once('utils/Configurazione.php');

class TestConfigurazione extends Test
{
    public function test(): void
    {
        $cfg_old = Configurazione::load();

        try{
            $ing_inf = new CorsoDiLaurea();
            $ing_inf->nome = "T. Ing. Informatica";
            $ing_inf->formula = "T + C";
            $ing_inf->cfuRichiesti = 180;
            $ing_inf->Tmin = 18;
            $ing_inf->Tmax = 30;
            $ing_inf->Tstep = 1;
            $ing_inf->Cmin = 18;
            $ing_inf->Cmax = 30;
            $ing_inf->Cstep = 1;

            $cfg = new Configurazione();
            $cfg->esami_informatici = ["ABC", "DEF"];
            $cfg->corsi_di_laurea = [$ing_inf->nome => $ing_inf];
            $cfg->save();

            $cfg = Configurazione::load();
            if ($cfg->esami_informatici != ["ABC", "DEF"]) {
                throw new Exception("esami_informatici non salvati correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->nome != $ing_inf->nome) {
                throw new Exception("corsi_di_laurea non salvati correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->formula != $ing_inf->formula) {
                throw new Exception("formula non salvata correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->cfuRichiesti != $ing_inf->cfuRichiesti) {
                throw new Exception("cfuRichiesti non salvati correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Tmin != $ing_inf->Tmin) {
                throw new Exception("Tmin non salvato correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Tmax != $ing_inf->Tmax) {
                throw new Exception("Tmax non salvato correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Tstep != $ing_inf->Tstep) {
                throw new Exception("Tstep non salvato correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Cmin != $ing_inf->Cmin) {
                throw new Exception("Cmin non salvato correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Cmax != $ing_inf->Cmax) {
                throw new Exception("Cmax non salvato correttamente");
            }
            if ($cfg->corsi_di_laurea[$ing_inf->nome]->Cstep != $ing_inf->Cstep) {
                throw new Exception("Cstep non salvato correttamente");
            }

        } finally {
            $cfg_old->save();
        }
    }
}
