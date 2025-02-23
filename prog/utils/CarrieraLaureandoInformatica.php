<?php
require_once('utils/CarrieraLaureando.php');
require_once('utils/GestioneCarrieraStudente.php');

class CarrieraLaureandoInformatica extends CarrieraLaureando
{
    private int $annoImmatricolazione;
    private string $dataLaurea;
    private bool $bonus;

    public function __construct(string $matricola, string $cdl, string $dataLaurea){
        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);

        $this->contructFromGCS($anagrafica, $carriera);
        $this->dataLaurea = $dataLaurea;
        $this->annoImmatricolazione = $carriera["Esami"]["Esame"][0]["ANNO_IMM"];

        $this->bonus = false;
        $fineBonus = ($this->annoImmatricolazione + 4) . ("-05-01");
        if ($dataLaurea < $fineBonus) {
            $this->bonus = true;
            $this->applicaBonus();
        }

        $config = Configurazione::load();
        foreach ($this->esami as $esame) {
            if (in_array($esame->nomeEsame, $config->esamiInformatici)) {
                $esame->informatico = true;
            }
        }
    }

    public function getMediaEsamiInformatici(): float
    {
        $num = 0;
        $den = 0;
        foreach ($this->esami as $esame) {
            if ($esame->faMedia && $esame->informatico) {
                $num += (int)$esame->votoEsame * (int)$esame->cfu;
                $den += (int)$esame->cfu;
            }
        }
        return $num / $den;
    }

    public function getBonus(): bool
    {
        return $this->bonus;
    }

    private function applicaBonus(): void
    {
        $esameMin = null;
        foreach ($this->esami as $esame) {
            if (!$esame->faMedia) continue;
            if (!$esameMin || $esame->votoEsame < $esameMin->votoEsame) {
                $esameMin = $esame;
            }
        }
        $esameMin->faMedia = 0;
    }
}
