<?php
require_once('utils/ProspettoConSimulazione.php');
require_once('lib/fpdf184/fpdf.php');

class ProspettoPDFCommissione
{
    private array $matricole = array();
    private string $dataLaurea;
    private string $cdl;

    public function __construct(array $matricole, string $dataLaurea, string $cdl)
    {
        $this->matricole = $matricole;
        $this->dataLaurea = $dataLaurea;
        $this->cdl = $cdl;
    }

    public function generaProspettiCommissione(): void
    {
        $pdf = new FPDF();
        $font_family = "Arial";

        // --------  PRIMA PAGINA CON LA LISTA ---------------
        $pdf->AddPage();

        $pdf->SetFont($font_family, "", 14);
        $pdf->Cell(0, 6, $this->cdl, 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont($font_family, "", 14);
        $pdf->Cell(0, 6, 'LAUREANDOSI 2', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont($font_family, "", 16);
        $pdf->Cell(0, 6, 'LISTA LAUREANDI', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont($font_family, "", 14);
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, "COGNOME", 1, 0, 'C');
        $pdf->Cell($width, $height, "NOME", 1, 0, 'C');
        $pdf->Cell($width, $height, "CDL", 1, 0, 'C');
        $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');

        $pdf->SetFont($font_family, "", 12);
        foreach ($this->matricole as $matricola) {
            $pag_con_simulazione = new ProspettoConSimulazione($matricola, $this->cdl, $this->dataLaurea);
            $pag_con_simulazione->generaRiga($pdf);
        }

        // --------  PAGINE CON LA CARRIERA ----------------
        foreach ($this->matricole as $matricola) {
            $pag_con_simulazione = new ProspettoConSimulazione($matricola, $this->cdl, $this->dataLaurea);
            $pag_con_simulazione->generaContenuto($pdf);
        }

        $pdf->Output('F', "run/prospettoCommissione.pdf");
    }

    public function generaProspettiLaureandi(): void
    {
        foreach ($this->matricole as $matricola) {
            $prospetto = new ProspettoPDFLaureando($matricola, $this->cdl, $this->dataLaurea);
            $prospetto->generaProspetto();
        }
    }

    public function generaAusiliario(): void
    {
        $data = [
            "matricole" => $this->matricole,
            "cdl" => $this->cdl,
            "data" => $this->dataLaurea,
        ];
        $str = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('run/appello.json', $str);
    }
}
