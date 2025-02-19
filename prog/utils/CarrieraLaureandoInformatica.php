<?php
require_once('utils/CarrieraLaureando.php');
require_once('utils/GestioneCarrieraStudente.php');

class CarrieraLaureandoInformatica extends CarrieraLaureando
{
    private string $dataImmatricolazione;
    private string $dataLaurea;
    private float $mediaEsamiInformatici;
    private bool $bonus;

    public function __construct(string $matricola, string $cdl, string $dataLaurea){
        parent::__construct($matricola, $cdl);
        $this->dataLaurea = $dataLaurea;
        $this->bonus = false;

        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);

        $this->dataImmatricolazione = $carriera["Esami"]["Esame"][0]["ANNO_IMM"];
        $fine_bonus = ($this->dataImmatricolazione + 4) . ("-05-01");
        if ($dataLaurea < $fine_bonus) {
            $this->bonus = true;
            $this->applicaBonus();
        }

        $config = Configurazione::load();
        foreach ($this->esami as $esame) {
            if (in_array($esame->nomeEsame, $config->esami_informatici)) {
                $esame->informatico = true;
            }
        }
        $this->calcolaMediaEsamiInformatici();
        $this->calcola_media();
    }

    public function getMediaEsamiInformatici(): float
    {
        return $this->mediaEsamiInformatici;
    }

    public function calcolaMediaEsamiInformatici(): void
    {
        $somma = 0;
        $numero = 0;
        foreach ($this->esami as $esame) {
            if ($esame->faMedia && $esame->informatico) {
                $somma += (int)$esame->votoEsame;
                $numero++;
            }
        }
        $this->mediaEsamiInformatici = $somma / $numero;
    }

    public function getBonus(): bool
    {
        return $this->bonus;
    }

    private function applicaBonus(): void
    {
        $esame_min = null;
        foreach ($this->esami as $esame) {
            if (!$esame->faMedia) continue;
            if (!$esame_min || $esame->votoEsame < $esame_min->votoEsame) {
                $esame_min = $esame;
            }
        }
        $esame_min->faMedia = 0;
    }
}
