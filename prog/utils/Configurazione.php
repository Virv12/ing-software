<?php
require_once('utils/CorsoDiLaurea.php');

class Configurazione
{
    public string $messaggio;
    public array $esamiInformatici;
    public array $corsiDiLaurea;

    private static ?Configurazione $instance = null;

    public static function load(): Configurazione
    {
        if (self::$instance == null) {
            self::$instance = new Configurazione();

            self::$instance->messaggio = file_get_contents('config/messaggio.txt');

            $esamiInformatici = file_get_contents('config/esami_informatici.json');
            $esamiInformatici = json_decode($esamiInformatici, true);
            self::$instance->esamiInformatici = $esamiInformatici["nomi_esami"];

            $corsiDiLaurea = file_get_contents('config/corsi_di_laurea.json');
            $corsiDiLaurea = json_decode($corsiDiLaurea, true);
            self::$instance->corsiDiLaurea = [];

            foreach ($corsiDiLaurea as $nome => $value) {
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
                self::$instance->corsiDiLaurea[$nome] = $cdl;
            }
        }

        return self::$instance;
    }
}
