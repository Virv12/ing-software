<?php
require_once('utils/CarrieraLaureando.php');
require_once('utils/GestioneCarrieraStudente.php');

class CarrieraLaureandoInformatica extends CarrieraLaureando
{
    public string $annoImmatricolazione;
    public string $dataLaurea;
    public bool $bonus;

    public function __construct(string $matricola, string $cdl, string $dataLaurea){
        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);

        $this->contructFromGCS($anagrafica, $carriera);
        $this->dataLaurea = $dataLaurea;
        $this->annoImmatricolazione = $carriera["Esami"]["Esame"][0]["ANNO_IMM"];

        $this->bonus = false;
        $fine_bonus = ($this->annoImmatricolazione + 4) . ("-05-01");
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
    }

    public function getMediaEsamiInformatici(): float
    {
        $somma = 0;
        $numero = 0;
        foreach ($this->esami as $esame) {
            if ($esame->faMedia && $esame->informatico) {
                $somma += (int)$esame->votoEsame * (int)$esame->cfu;
                $numero += (int)$esame->cfu;
            }
        }
        return $somma / $numero;
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
