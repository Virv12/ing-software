<?php
require_once('utils/CorsoDiLaurea.php');
require_once('utils/Configurazione.php');
require_once('utils/EsameLaureando.php');
require_once('utils/GestioneCarrieraStudente.php');

class CarrieraLaureando
{
    public string $matricola;
    public string $nome;
    public string $cognome;
    public CorsoDiLaurea $cdl;
    public string $email;
    public array $esami;

    private float $media;

    public function __construct(string $matricola, string $cdl)
    {
        $this->matricola = $matricola;

        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);

        $config = Configurazione::load();

        $this->nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->email = $anagrafica["Entries"]["Entry"]["email_ate"];
        $this->cdl = $config->corsi_di_laurea[$cdl];
        $this->esami = array();

        foreach ($carriera["Esami"]["Esame"] as $esame) {
            if (!is_string($esame["DES"])) continue;
            $esame = $this->make_esame($esame["DES"], $esame["VOTO"], $esame["PESO"], 1);
            if ($esame) {
                array_push($this->esami, $esame);
            }
        }

        $this->calcola_media();
    }

    public function calcola_media(): void
    {
        $somma_voto_cfu = 0;
        $somma_cfu_tot = 0;

        foreach ($this->esami as $esame) {
            if ($esame->faMedia) {
                $somma_voto_cfu += (int)$esame->votoEsame * (int)$esame->cfu;
                $somma_cfu_tot += (int)$esame->cfu;
            }
        }

        $this->media = $somma_voto_cfu / $somma_cfu_tot;
    }

    public function getMedia(): float
    {
        return $this->media;
    }

    public function creditiCurricolariConseguiti(): int
    {
        $crediti = 0;
        foreach ($this->esami as $esame) {
            if ($esame->nomeEsame == "PROVA FINALE") continue;
            if ($esame->nomeEsame == "LIBERA SCELTA PER RICONOSCIMENTI") continue;
            if (!$esame->curricolare) continue;
            $crediti += $esame->cfu;
        }
        return $crediti;
    }

    public function creditiCheFannoMedia(): int
    {
        $crediti = 0;
        foreach ($this->esami as $esame) {
            if (!$esame->faMedia) continue;
            $crediti += $esame->cfu;
        }
        return $crediti;
    }

    private function make_esame($nome, $voto, $cfu, $curricolare): ?EsameLaureando
    {
        if (!is_string($nome)) return null;
        if ($nome == "TEST DI VALUTAZIONE DI INGEGNERIA") return null;

        $faMedia = !($nome == "PROVA FINALE" || !$voto);
        if ($voto == "30  e lode") {
            // -_- ci hanno messo 2 spazi
            $voto = 33;
        }

        $esame = new EsameLaureando();
        $esame->nomeEsame = $nome;
        $esame->votoEsame = (int)$voto;
        $esame->cfu = (int)$cfu;
        $esame->faMedia = $faMedia;
        $esame->curricolare = $curricolare;
        return $esame;
    }

    public function getVotoDiTesi(): int
    {
        foreach ($this->esami as $esame) {
            if ($esame->nomeEsame == "PROVA FINALE") {
                return $esame->votoEsame;
            }
        }
        return 0;
    }
}
