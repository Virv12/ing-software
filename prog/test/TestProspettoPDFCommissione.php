<?php
require_once('utils/ProspettoPDFCommissione.php');

class TestProspettoPDFCommissione extends Test
{
    public function test(): void
    {
        $valore = array(123456);
        $vecchio = file_get_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf');
        $val = new ProspettoPDFCommissione2($valore,"2024_01_05", "T. Ing. Informatica");
        $val->generaProspettiCommissione();
        $aux = file_get_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf');
        file_put_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf',$vecchio);

        $vecchio1 = file_get_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf');
        $var = new ProspettoPDFCommissione2($valore,"2018_01_05", "T. Ing. Informatica");
        $var->generaProspettiCommissione();
        $aux1 = file_get_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf');
        file_put_contents('C:\Users\franc\Local Sites\genera-prospetti-laurea\app\public\data\pdf_generati\prospettoCommissione.pdf',$vecchio1);

        if($aux == $aux1)
            echo "prospetti non generati correttamente";
        else
            echo "ProspettoPDFCommissione2 : TEST SUPERATI";
    }
}
