<?php
require_once('utils/CorsoDiLaurea.php');

class Configurazione
{
    public $esami_informatici;
    public $corsi_di_laurea;

    public static function load(): Configurazione
    {
        $cfg = new Configurazione();

        $esami_informatici = file_get_contents('config/esami_informatici.json');
        $esami_informatici = json_decode($esami_informatici, true);
        $cfg->esami_informatici = $esami_informatici["nomi_esami"];

        $formule_laurea = file_get_contents('config/formule_laurea.json');
        $formule_laurea = json_decode($formule_laurea, true);
        $cfg->corsi_di_laurea = [];

        foreach ($formule_laurea as $nome => $value) {
            $cdl = new CorsoDiLaurea();
            $cdl->nome = $nome;
            $cdl->formula = $value["formula"];
            $cdl->cfuRichiesti = $value["cfu_richiesti"];
            $cdl->Tmin = $value["Tmin"];
            $cdl->Tmax = $value["Tmax"];
            $cdl->Tstep = $value["Tstep"];
            $cdl->Cmin = $value["Cmin"];
            $cdl->Cmax = $value["Cmax"];
            $cdl->Cstep = $value["Cstep"];
            $cfg->corsi_di_laurea[$nome] = $cdl;
        }

        return $cfg;
    }

    public function save(): void
    {
        $esami_informatici = ["nomi_esami" => $this->esami_informatici];
        $esami_informatici = json_encode($esami_informatici, JSON_PRETTY_PRINT);
        file_put_contents('config/esami_informatici.json', $esami_informatici);

        $formule_laurea = [];
        foreach ($this->corsi_di_laurea as $key => $cdl) {
            $obj = [];
            $obj["formula"] = $cdl->formula;
            $obj["cfu_richiesti"] = $cdl->cfuRichiesti;
            $obj["Tmin"] = $cdl->Tmin;
            $obj["Tmax"] = $cdl->Tmax;
            $obj["Tstep"] = $cdl->Tstep;
            $obj["Cmin"] = $cdl->Cmin;
            $obj["Cmax"] = $cdl->Cmax;
            $obj["Cstep"] = $cdl->Cstep;
            $formule_laurea[$key] = $obj;
        }
        $formule_laurea = json_encode($formule_laurea, JSON_PRETTY_PRINT);
        file_put_contents('config/formule_laurea.json', $formule_laurea);
    }
}
