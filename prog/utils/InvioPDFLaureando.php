<?php
require_once('utils/ProspettoPDFLaureando.php');
require_once('lib/PHPMailer/src/Exception.php');
require_once('lib/PHPMailer/src/PHPMailer.php');
require_once('lib/PHPMailer/src/SMTP.php');
use PHPMailer\PHPMailer\PHPMailer;

class InvioPDFLaureando {
    private array $matricole;
    private string $cdl;
    private string $dataLaurea;

    public function __construct()
    {
        $str = file_get_contents('run/appello.json');
        $data = json_decode($str, true);

        $this->matricole = $data["matricole"];
        $this->cdl = $data["cdl"];
        $this->dataLaurea = $data["data"];
    }

    public function invioProspetti(): void
    {
        foreach ($this->matricole as $matricola) {
            $prospetto = new ProspettoPDFLaureando($matricola, $this->cdl, $this->dataLaurea);
            $this->inviaProspetto($prospetto->carrieraLaureando);
        }
    }

    public function inviaProspetto(CarrieraLaureando $carriera_laureando): void
    {
        $messaggio = new PHPMailer();
        $messaggio->Host = "mixer.unipi.it";
        $messaggio->Port = 25;

        //$messaggio->isSMTP();
        //$messaggio->SMTPSecure = "tls";
        //$messaggio->SMTPAuth = false;

        $messaggio->From = 'noreply-laureandosi@ing.unipi.it';
        $messaggio->FromName = 'Laureandosi 2.1';

        $messaggio->CharSet = 'UTF-8';
        $messaggio->isHTML();

        $messaggio->addAddress($carriera_laureando->email);
        $messaggio->Subject = 'Prospetti per appello di laurea';

        $msg = file_get_contents('config/messaggio.txt');
        $messaggio->Body = stripslashes($msg);

        $messaggio->addAttachment("run/{$carriera_laureando->matricola}-prospetto.pdf");

        $res = $messaggio->send();
        $messaggio->smtpClose();

        if (!$res) {
            echo $messaggio->ErrorInfo . "<br>";
            echo "Errore nell invio<br>";
        } else {
            echo "Email inviata correttamente!<br>";
        }

    }
}
?>
