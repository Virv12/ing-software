<?php
require_once('utils/ProspettoPDFLaureando.php');

class ProspettoConSimulazione extends ProspettoPDFLaureando {
    public function __construct($matricola, $cdl_in, $data_laurea)
    {
        parent::__construct($matricola, $cdl_in, $data_laurea);
    }

    public function generaProspettoConSimulazione(): FPDF
    {
        $pdf = new FPDF();
        $this->generaContenuto($pdf);
        return $pdf;
    }

    public function generaContenuto(FPDF $pdf): void
    {
        $font_family = "Arial";
        $tipo_informatico = 0;
        // indica se il laureando è informatico, viene modificato da solo

        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 16);
        // --------------------- INTESTAZIONE : cdl e scritta prospetto --------------------------

        $pdf->Cell(0, 6, $this->carrieraLaureando->cdl->nome, 0, 1, 'C');
        // dimensioni, testo, bordo, a capo, align
        $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $pdf->Ln(2);
        // ------------------------------ INFORMAZIONI ANAGRAFICHE DELLO STUDENTE ------------------------------

        $pdf->SetFont($font_family, "", 9);
        $anagrafica_stringa = "Matricola:                       " . $this->carrieraLaureando->matricola . //attenzione: quelli che sembrano spazi in realtà sono &Nbsp perché fpdf non stampa spazi
            "\nNome:                            " . $this->carrieraLaureando->nome .
            "\nCognome:                      " . $this->carrieraLaureando->cognome .
            "\nEmail:                             " . $this->carrieraLaureando->email .
            "\nData:                              " . $this->dataLaurea;
        //aggiungere bonus if inf

        if ($this->carrieraLaureando->cdl->nome == "T. Ing. Informatica") {
            $tipo_informatico = 1;
            $anagrafica_stringa .= "\nBonus:                            " . $this->carrieraLaureando->getBonus();
        }

        $pdf->MultiCell(0, 6, $anagrafica_stringa, 1, 'L');
        //$pdf->Cell(0, 100 ,$anagrafica_stringa, 1 ,1, '');
        $pdf->Ln(3);
        // spazio bianco

        // ------------------------------- INFORMAZIONI SUGLI ESAMI ----------------------------------------
        // 1 pag = 190 = 21cm con bordi di 1cm
        $larghezza_piccola = 12;
        $altezza = 5.5;
        $larghezza_grande = 190 - (3 * $larghezza_piccola);
        if ($tipo_informatico != 1) {
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 1, 'C');
            // newline
        } else {
            $larghezza_piccola -= 1;
            $larghezza_grande = 190 - (4 * $larghezza_piccola);
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "INF", 1, 1, 'C');
            // newline
        }

        $altezza = 4;
        $pdf->SetFont($font_family, "", 8);
        for ($i = 0; $i < sizeof($this->carrieraLaureando->esami); $i++) {
            $esame = $this->carrieraLaureando->esami[$i];
            $pdf->Cell($larghezza_grande, $altezza, $esame->nomeEsame, 1, 0, 'L');
            $pdf->Cell($larghezza_piccola, $altezza, $esame->cfu, 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, $esame->votoEsame, 1, 0, 'C');
            if ($tipo_informatico != 1) {
                $pdf->Cell($larghezza_piccola, $altezza, ($esame->faMedia == 1) ? 'X' : '', 1, 1, 'C');
                // newline
            } else {
                $pdf->Cell($larghezza_piccola, $altezza, ($esame->faMedia == 1) ? 'X' : '', 1, 0, 'C');
                $pdf->Cell($larghezza_piccola, $altezza, ($esame->informatico == 1) ? 'X' : '', 1, 1, 'C');
            }
        }
        $pdf->Ln(5);
        // ------------------------------- PARTE RIASUNTIVA  ----------------------------------------
        $pdf->SetFont($font_family, "", 9);
        $string = "Media Pesata (M):                                                  " . $this->carrieraLaureando->getMedia() .
            "\nCrediti che fanno media (CFU):                             " . $this->carrieraLaureando->creditiCheFannoMedia() .
            "\nCrediti curriculari conseguiti:                                  " . $this->carrieraLaureando->creditiCurricolariConseguiti() .
            "\nFormula calcolo voto di laurea:                               " . $this->carrieraLaureando->cdl->formula;
        if ($tipo_informatico == 1) {
            $string .= "\nMedia pesata esami INF:                                        " . $this->carrieraLaureando->getMediaEsamiInformatici();
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");


        // ------------------------- PARTE DELLA SIMULAZIONE ------------------------------------

        //prendere l'intervallo dei parametri t e c
        $t_min =  $this->carrieraLaureando->cdl->Tmin;
        $t_max =  $this->carrieraLaureando->cdl->Tmax;
        $t_step = $this->carrieraLaureando->cdl->Tstep;
        $c_min =  $this->carrieraLaureando->cdl->Cmin;
        $c_max =  $this->carrieraLaureando->cdl->Cmax;
        $c_step = $this->carrieraLaureando->cdl->Cstep;
        $CFU = $this->carrieraLaureando->creditiCheFannoMedia();

        // aggiungere al pdf le parti necessarie
        $pdf->Ln(4);
        $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
        $width = 190 / 2;
        $height = 4.5;

        if ($c_min != 0) {
            $pdf->Cell($width, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $M = $this->carrieraLaureando->getMedia();
            $T = 0;

            for ($C = $c_min; $C <= $c_max; $C += $c_step) {
                $voto = 0;
                eval("\$voto = " . $this->carrieraLaureando->cdl->formula . ";");
                //$voto = intval($voto);
                $pdf->Cell($width, $height, $C, 1, 0, 'C');
                $pdf->Cell($width, $height, $voto, 1, 1, 'C');
            }
        }
        if ($t_min != 0) {
            $pdf->Cell($width, $height, "VOTO TESI (T)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $M = $this->carrieraLaureando->getMedia();
            $C = 0;

            for ($T = $t_min; $T <= $t_max; $T += $t_step) {
                $voto = 0;
                eval("\$voto = " . $this->carrieraLaureando->cdl->formula . ";");
                //$voto = intval($voto);
                $pdf->Cell($width, $height, $T, 1, 0, 'C');
                $pdf->Cell($width, $height, $voto, 1, 1, 'C');
            }
        }
    }

    public function generaRiga(FPDF $pdf): void
    {
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, $this->carrieraLaureando->cognome, 1, 0, 'L');
        $pdf->Cell($width, $height, $this->carrieraLaureando->nome, 1, 0, 'L');
        $pdf->Cell($width, $height, "", 1, 0, 'C');
        // è vuoto apposta, il cdl è scritto sopra. nell'esempio era così
        $pdf->Cell($width, $height, "/110", 1, 1, 'C');
    }
}
