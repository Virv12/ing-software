<?php
require_once('utils/CorsoDiLaurea.php');

class Configurazione
{
    public $messaggio;
    public $esami_informatici;
    public $corsi_di_laurea;

    private static $instance = null;

    public static function load(): Configurazione
    {
        if (self::$instance == null) {
            self::$instance = new Configurazione();

            self::$instance->messaggio = file_get_contents('config/messaggio.txt');

            $esami_informatici = file_get_contents('config/esami_informatici.json');
            $esami_informatici = json_decode($esami_informatici, true);
            self::$instance->esami_informatici = $esami_informatici["nomi_esami"];

            $formule_laurea = file_get_contents('config/corsi_di_laurea.json');
            $formule_laurea = json_decode($formule_laurea, true);
            self::$instance->corsi_di_laurea = [];

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
                self::$instance->corsi_di_laurea[$nome] = $cdl;
            }
        }

        return self::$instance;
    }
}
