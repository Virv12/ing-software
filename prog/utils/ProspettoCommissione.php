<?php
require_once('lib/fpdf184/fpdf.php');

class ProspettoCommissione
{
    public array $prospetti_laureandi;
    public string $cdl;

    public function __construct(array $prospetti_laureandi, string $cdl)
    {
        $this->prospetti_laureandi = $prospetti_laureandi;
        $this->cdl = $cdl;
    }

    public function generaFile(): void
    {
        $pdf = new FPDF();
        $this->generaContenuto($pdf);
        foreach ($this->prospetti_laureandi as $prospetto_laureando) {
            $prospetto_laureando->generaContenuto($pdf, true);
        }
        $pdf->Output('F', "run/prospettoCommissione.pdf");
    }

    public function generaContenuto(FPDF $pdf): void
    {
        $font_family = "Arial";
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
        foreach ($this->prospetti_laureandi as $prospetto_laureando) {
            $prospetto_laureando->generaRiga($pdf);
        }
    }
}
