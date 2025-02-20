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
        assert(array_key_exists($cdl, $cfg->corsi_di_laurea));
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
        $cdl = $config->corsi_di_laurea[$this->cdl];

        $font_family = "Arial";
        $tipo_informatico = 0;

        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 16);

        $pdf->Cell(0, 6, $cdl->nome, 0, 1, 'C');
        $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont($font_family, "", 9);
        $anagrafica_stringa = "Matricola:                       " . $this->matricola .
            "\nNome:                            " . $this->nome .
            "\nCognome:                      " . $this->cognome .
            "\nEmail:                             " . $this->email .
            "\nData:                              " . $this->dataLaurea;

        if ($cdl->nome == "T. Ing. Informatica") {
            $tipo_informatico = 1;
            $anagrafica_stringa .= "\nBonus:                            " . ($this->bonus ? "SI" : "NO");
        }

        $pdf->MultiCell(0, 6, $anagrafica_stringa, 1, 'L');
        $pdf->Ln(3);

        $larghezza_piccola = 12;
        $altezza = 5.5;
        $larghezza_grande = 190 - (3 * $larghezza_piccola);
        if ($tipo_informatico != 1) {
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 1, 'C');
        } else {
            $larghezza_piccola -= 1;
            $larghezza_grande = 190 - (4 * $larghezza_piccola);
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "INF", 1, 1, 'C');
        }

        $altezza = 4;
        $pdf->SetFont($font_family, "", 8);
        foreach ($this->esami as $esame) {
            $pdf->Cell($larghezza_grande, $altezza, $esame->nomeEsame, 1, 0, 'L');
            $pdf->Cell($larghezza_piccola, $altezza, $esame->cfu, 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, $esame->votoEsame, 1, 0, 'C');
            if ($tipo_informatico != 1) {
                $pdf->Cell($larghezza_piccola, $altezza, $esame->faMedia ? 'X' : '', 1, 1, 'C');
            } else {
                $pdf->Cell($larghezza_piccola, $altezza, $esame->faMedia ? 'X' : '', 1, 0, 'C');
                $pdf->Cell($larghezza_piccola, $altezza, $esame->informatico ? 'X' : '', 1, 1, 'C');
            }
        }
        $pdf->Ln(5);

        $pdf->SetFont($font_family, "", 9);
        $string  = "Media Pesata (M):                                                  " . $this->media;
        $string .= "\nCrediti che fanno media (CFU):                             " . $this->creditiCheFannoMedia;
        $string .= "\nCrediti curriculari conseguiti:                                  " . $this->creditiCurricolariConseguiti . "/" . $cdl->cfuRichiesti;
        $string .= "\nVoto di Tesi (T):                                                      " . $this->votoTesi;
        $string .= "\nFormula calcolo voto di laurea:                               " . $cdl->formula;
        if ($tipo_informatico == 1) {
            $string .= "\nMedia pesata esami INF:                                        " . $this->mediaEsamiInformatici;
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");

        if ($simulazione) {
            $t_min =  $cdl->Tmin;
            $t_max =  $cdl->Tmax;
            $t_step = $cdl->Tstep;
            $c_min =  $cdl->Cmin;
            $c_max =  $cdl->Cmax;
            $c_step = $cdl->Cstep;
            $CFU = $this->creditiCheFannoMedia;

            $pdf->Ln(4);
            $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
            $width = 190 / 2;
            $height = 4.5;

            if ($c_min != 0) {
                $pdf->Cell($width, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
                $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
                $M = $this->media;
                $T = 0;

                for ($C = $c_min; $C <= $c_max; $C += $c_step) {
                    $voto = 0;
                    eval("\$voto = " . $cdl->formula . ";");
                    $pdf->Cell($width, $height, $C, 1, 0, 'C');
                    $pdf->Cell($width, $height, $voto, 1, 1, 'C');
                }
            }
            if ($t_min != 0) {
                $pdf->Cell($width, $height, "VOTO TESI (T)", 1, 0, 'C');
                $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
                $M = $this->media;
                $C = 0;

                for ($T = $t_min; $T <= $t_max; $T += $t_step) {
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
