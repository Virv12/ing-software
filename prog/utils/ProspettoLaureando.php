<?php
require_once('utils/CarrieraLaureando.php');
require_once('lib/fpdf184/fpdf.php');

class ProspettoLaureando
{
    public string $matricola;
    public string $nome;
    public string $cognome;
    public string $email;
    public array $esami;

    public string $cdl;
    public string $dataLaurea;

    public float $media;
    public int $creditiCheFannoMedia;
    public int $creditiCurricolariConseguiti;
    public int $votoTesi;

    public ?bool $bonus = null;
    public ?float $mediaEsamiInformatici = null;

    public function __construct(CarrieraLaureando $laureando, string $cdl, string $dataLaurea)
    {
        $this->matricola = $laureando->matricola;
        $this->nome = $laureando->nome;
        $this->cognome = $laureando->cognome;
        $this->email = $laureando->email;
        $this->esami = $laureando->esami;

        $this->cdl = $cdl;
        $this->dataLaurea = $dataLaurea;

        $this->media = $laureando->getMedia();
        $this->creditiCheFannoMedia = $laureando->getCreditiCheFannoMedia();
        $this->creditiCurricolariConseguiti = $laureando->getCreditiCurricolariConseguiti();
        $this->votoTesi = $laureando->getVotoTesi();

        if ($this->cdl == "T. Ing. Informatica") {
            $this->bonus = $laureando->getBonus();
            $this->mediaEsamiInformatici = $laureando->getMediaEsamiInformatici();
        }

        $cfg = Configurazione::load();
        assert(array_key_exists($cdl, $cfg->corsiDiLaurea));
    }

    public function generaFile(): void
    {
        $pdf = new FPDF();
        $this->generaContenuto($pdf, false);
        $pdf->Output('F', "run/{$this->matricola}-prospetto.pdf");
    }

    public function generaContenuto(FPDF $pdf, bool $simulazione): void
    {
        $config = Configurazione::load();
        $cdl = $config->corsiDiLaurea[$this->cdl];

        $fontFamily = "Arial";
        $tipoInformatico = 0;

        $pdf->AddPage();
        $pdf->SetFont($fontFamily, "", 16);

        $pdf->Cell(0, 6, $cdl->nome, 0, 1, 'C');
        $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont($fontFamily, "", 9);
        $anagraficaStringa = "Matricola:                       " . $this->matricola .
            "\nNome:                            " . $this->nome .
            "\nCognome:                      " . $this->cognome .
            "\nEmail:                             " . $this->email .
            "\nData:                              " . $this->dataLaurea;

        if ($cdl->nome == "T. Ing. Informatica") {
            $tipoInformatico = 1;
            $anagraficaStringa .= "\nBonus:                            " . ($this->bonus ? "SI" : "NO");
        }

        $pdf->MultiCell(0, 6, $anagraficaStringa, 1, 'L');
        $pdf->Ln(3);

        $larghezzaPiccola = 12;
        $altezza = 5.5;
        $larghezzaGrande = 190 - (3 * $larghezzaPiccola);
        if ($tipoInformatico != 1) {
            $pdf->Cell($larghezzaGrande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "MED", 1, 1, 'C');
        } else {
            $larghezzaPiccola -= 1;
            $larghezzaGrande = 190 - (4 * $larghezzaPiccola);
            $pdf->Cell($larghezzaGrande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "MED", 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, "INF", 1, 1, 'C');
        }

        $altezza = 4;
        $pdf->SetFont($fontFamily, "", 8);
        foreach ($this->esami as $esame) {
            $pdf->Cell($larghezzaGrande, $altezza, $esame->nomeEsame, 1, 0, 'L');
            $pdf->Cell($larghezzaPiccola, $altezza, $esame->cfu, 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, $esame->votoEsame, 1, 0, 'C');
            if ($tipoInformatico != 1) {
                $pdf->Cell($larghezzaPiccola, $altezza, $esame->faMedia ? 'X' : '', 1, 1, 'C');
            } else {
                $pdf->Cell($larghezzaPiccola, $altezza, $esame->faMedia ? 'X' : '', 1, 0, 'C');
                $pdf->Cell($larghezzaPiccola, $altezza, $esame->informatico ? 'X' : '', 1, 1, 'C');
            }
        }
        $pdf->Ln(5);

        $pdf->SetFont($fontFamily, "", 9);
        $string  = "Media Pesata (M):                                                  " . $this->media;
        $string .= "\nCrediti che fanno media (CFU):                             " . $this->creditiCheFannoMedia;
        $string .= "\nCrediti curriculari conseguiti:                                  " . $this->creditiCurricolariConseguiti . "/" . $cdl->cfuRichiesti;
        $string .= "\nVoto di Tesi (T):                                                      " . $this->votoTesi;
        $string .= "\nFormula calcolo voto di laurea:                               " . $cdl->formula;
        if ($tipoInformatico == 1) {
            $string .= "\nMedia pesata esami INF:                                        " . $this->mediaEsamiInformatici;
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");

        if ($simulazione) {
            $Tmin =  $cdl->Tmin;
            $Tmax =  $cdl->Tmax;
            $Tstep = $cdl->Tstep;
            $Cmin =  $cdl->Cmin;
            $Cmax =  $cdl->Cmax;
            $Cstep = $cdl->Cstep;
            $cfu = $this->creditiCheFannoMedia;

            $pdf->Ln(4);
            $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
            $width = 190 / 2;
            $height = 4.5;

            if ($Cmin != 0) {
                $pdf->Cell($width, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
                $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
                $M = $this->media;
                $T = 0;

                for ($C = $Cmin; $C <= $Cmax; $C += $Cstep) {
                    $voto = 0;
                    eval("\$voto = " . $cdl->formula . ";");
                    $pdf->Cell($width, $height, $C, 1, 0, 'C');
                    $pdf->Cell($width, $height, $voto, 1, 1, 'C');
                }
            }
            if ($Tmin != 0) {
                $pdf->Cell($width, $height, "VOTO TESI (T)", 1, 0, 'C');
                $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
                $M = $this->media;
                $C = 0;

                for ($T = $Tmin; $T <= $Tmax; $T += $Tstep) {
                    $voto = 0;
                    eval("\$voto = " . $cdl->formula . ";");
                    $pdf->Cell($width, $height, $T, 1, 0, 'C');
                    $pdf->Cell($width, $height, $voto, 1, 1, 'C');
                }
            }
        }
    }

    public function generaRiga(FPDF $pdf): void
    {
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, $this->cognome, 1, 0, 'L');
        $pdf->Cell($width, $height, $this->nome, 1, 0, 'L');
        $pdf->Cell($width, $height, "", 1, 0, 'C');
        $pdf->Cell($width, $height, "/110", 1, 1, 'C');
    }
}
