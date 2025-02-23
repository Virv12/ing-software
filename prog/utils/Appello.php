<?php
require_once('utils/CarrieraLaureando.php');
require_once('utils/ProspettoLaureando.php');
require_once('utils/ProspettoCommissione.php');
require_once('lib/PHPMailer/src/Exception.php');
require_once('lib/PHPMailer/src/PHPMailer.php');
require_once('lib/PHPMailer/src/SMTP.php');
use PHPMailer\PHPMailer\PHPMailer;

class Appello
{
    private array $matricole;
    private string $dataLaurea;
    private string $cdl;

    public function __construct(array $matricole, string $dataLaurea, string $cdl)
    {
        $this->matricole = $matricole;
        $this->dataLaurea = $dataLaurea;
        $this->cdl = $cdl;
    }

    public function salva(): void
    {
        $data = [
            "matricole" => $this->matricole,
            "data_laurea" => $this->dataLaurea,
            "cdl" => $this->cdl
        ];
        $str = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents("run/appello.json", $str);
    }

    public static function carica(): Appello
    {
        $str = file_get_contents('run/appello.json');
        $data = json_decode($str, true);

        return new Appello($data["matricole"], $data["data_laurea"], $data["cdl"]);
    }

    public function generaProspetti(): bool
    {
        $prospettiLaureandi = array();
        foreach ($this->matricole as $matricola) {
            $laureando = CarrieraLaureando::forseInformatico($matricola, $this->cdl, $this->dataLaurea);
            $prospettoLaureando = new ProspettoLaureando($laureando, $this->cdl, $this->dataLaurea);
            $prospettoLaureando->generaFile();
            array_push($prospettiLaureandi, $prospettoLaureando);
        }
        $prospettoCommissione = new ProspettoCommissione($prospettiLaureandi,  $this->cdl);
        $prospettoCommissione->generaFile();
        return true;
    }

    public function inviaProspetti(): bool
    {
        for ($i = 0; $i < count($this->matricole); $i++) {
            if ($i > 0) sleep(5);
            $matricola = $this->matricole[$i];
            $laureando = CarrieraLaureando::forseInformatico($matricola, $this->cdl, $this->dataLaurea);
            $res = $this->inviaProspetto($laureando);
            if (!$res) return false;
        }
        return true;
    }

    private function inviaProspetto(CarrieraLaureando $laureando): bool
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

        $messaggio->addAddress($laureando->email);
        $messaggio->Subject = 'Prospetti per appello di laurea';

        $cfg = Configurazione::load();
        $messaggio->Body = stripslashes($cfg->messaggio);

        $messaggio->addAttachment("run/{$laureando->matricola}-prospetto.pdf");

        $res = $messaggio->send();
        $messaggio->smtpClose();
        return $res;
    }
}
