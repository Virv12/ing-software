<?php
require_once('utils/CarrieraLaureandoInformatica.php');
require_once('utils/CarrieraLaureando.php');
require_once('lib/fpdf184/fpdf.php');

class ProspettoPDFLaureando
{
	public CarrieraLaureando $carrieraLaureando;
	protected string $dataLaurea;

    public function __construct(string $matricola, string $cdl, string $dataLaurea)
    {
        if ($cdl != "INGEGNERIA INFORMATICA (IFO-L)" && $cdl != "T. Ing. Informatica") {
            $this->carrieraLaureando = new CarrieraLaureando($matricola, $cdl);
        } else {
            $this->carrieraLaureando = new CarrieraLaureandoInformatica($matricola, $cdl, $dataLaurea);
        }
        $this->dataLaurea = $dataLaurea;
	}

    public function generaProspetto(): void
    {
        $font_family = "Arial";
        // indica se il laureando è informatico, viene modificato da solo
        $tipo_informatico = 0;

        $pdf = new FPDF();
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
            $anagrafica_stringa .= "\nBonus:                            " . ($this->carrieraLaureando->getBonus() ? "SI" : "NO");
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
            "\nVoto di Tesi (T):                                                      " . $this->carrieraLaureando->getVotoDiTesi() .
            "\nFormula calcolo voto di laurea:                               " . $this->carrieraLaureando->cdl->formula;
        if ($tipo_informatico == 1) {
            $string .= "\nMedia pesata esami INF:                                        " . $this->carrieraLaureando->getMediaEsamiInformatici();
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");
        $pdf->Output('F', "run/{$this->carrieraLaureando->matricola}-prospetto.pdf");
    }

    public function getCarriera(): CarrieraLaureando
    {
        return $this->carrieraLaureando;
    }
}
