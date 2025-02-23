<?php
require_once('utils/CorsoDiLaurea.php');
require_once('utils/Configurazione.php');
require_once('utils/EsameLaureando.php');
require_once('utils/GestioneCarrieraStudente.php');
require_once('utils/CarrieraLaureandoInformatica.php');

class CarrieraLaureando
{
    public string $matricola;
    public string $nome;
    public string $cognome;
    public string $email;
    public array $esami;

    public function __construct(string $matricola)
    {
        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);
        $this->contructFromGCS($anagrafica, $carriera);
    }

    protected function contructFromGCS(array $anagrafica, array $carriera): void
    {
        $this->matricola = $carriera["Esami"]["Esame"][0]["MATRICOLA"];
        $this->nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->email = $anagrafica["Entries"]["Entry"]["email_ate"];

        $this->esami = array();
        foreach ($carriera["Esami"]["Esame"] as $esame) {
            $esame = $this->makeEsame($esame["DES"], $esame["VOTO"], $esame["PESO"]);
            if ($esame) {
                array_push($this->esami, $esame);
            }
        }
    }

    public static function forseInformatico(string $matricola, string $cdl, string $dataLaurea): CarrieraLaureando
    {
        if ($cdl == "T. Ing. Informatica") {
            return new CarrieraLaureandoInformatica($matricola, $cdl, $dataLaurea);
        } else {
            return new CarrieraLaureando($matricola);
        }
    }

    public function getMedia(): float
    {
        $num = 0;
        $den = 0;

        foreach ($this->esami as $esame) {
            if ($esame->faMedia) {
                $num += (int)$esame->votoEsame * (int)$esame->cfu;
                $den += (int)$esame->cfu;
            }
        }

        return $num / $den;
    }

    public function getCreditiCurricolariConseguiti(): int
    {
        $crediti = 0;
        foreach ($this->esami as $esame) {
            if ($esame->nomeEsame == "PROVA FINALE") continue;
            if ($esame->nomeEsame == "LIBERA SCELTA PER RICONOSCIMENTI") continue;
            $crediti += $esame->cfu;
        }
        return $crediti;
    }

    public function getCreditiCheFannoMedia(): int
    {
        $crediti = 0;
        foreach ($this->esami as $esame) {
            if (!$esame->faMedia) continue;
            $crediti += $esame->cfu;
        }
        return $crediti;
    }

    public function getVotoTesi(): int
    {
        foreach ($this->esami as $esame) {
            if ($esame->nomeEsame == "PROVA FINALE") {
                return $esame->votoEsame;
            }
        }
        return 0;
    }

    private function makeEsame($nome, $voto, $cfu): ?EsameLaureando
    {
        if (!is_string($nome)) return null;
        if ($nome == "TEST DI VALUTAZIONE DI INGEGNERIA") return null;

        $faMedia = !($nome == "PROVA FINALE" || !$voto);
        if ($voto == "30  e lode" || $voto == "30 e lode") {
            // -_- ci hanno messo 2 spazi
            $voto = 33;
        }

        $esame = new EsameLaureando();
        $esame->nomeEsame = $nome;
        $esame->votoEsame = (int)$voto;
        $esame->cfu = (int)$cfu;
        $esame->faMedia = $faMedia;
        return $esame;
    }
}
