<?php
require_once('utils/ProspettoLaureando.php');
require_once('test/Test.php');

class TestProspettoLaureando extends Test
{
    private function _run_test(string $matricola, string $cdl, string $dataLaurea, array $result): void
    {
        $laureando = CarrieraLaureando::forseInformatico($matricola, $cdl, $dataLaurea);
        $prospetto = new ProspettoLaureando($laureando, $cdl, $dataLaurea);

        if ($prospetto->matricola !== $result["matricola"]) {
            throw new Exception("matricola non corretta");
        }
        if ($prospetto->nome !== $result["nome"]) {
            throw new Exception("nome non corretto");
        }
        if ($prospetto->cognome !== $result["cognome"]) {
            throw new Exception("cognome non corretto");
        }
        if ($prospetto->email !== $result["email"]) {
            throw new Exception("email non corretta");
        }

        if ($prospetto->cdl !== $result["cdl"]) {
            throw new Exception("cdl non corretto");
        }
        if ($prospetto->dataLaurea !== $result["dataLaurea"]) {
            throw new Exception("dataLaurea non corretta");
        }

        if (abs($prospetto->media - $result["media"]) > 0.01) {
            throw new Exception("media non corretta");
        }
        if ($prospetto->creditiCheFannoMedia !== $result["creditiCheFannoMedia"]) {
            throw new Exception("creditiCheFannoMedia non corretti");
        }
        if ($prospetto->creditiCurricolariConseguiti !== $result["creditiCurricolariConseguiti"]) {
            throw new Exception("creditiCurricolariConseguiti non corretti");
        }
        if ($prospetto->votoTesi !== $result["votoTesi"]) {
            throw new Exception("votoTesi non corretto");
        }

        if ($prospetto->bonus !== ($result["bonus"] ?? null)) {
            throw new Exception("bonus non corretto");
        }
        if (abs(($prospetto->mediaEsamiInformatici ?? 0) - ($result["mediaEsamiInformatici"] ?? 0)) > 0.01) {
            throw new Exception("mediaEsamiInformatici non corretta");
        }
    }

    public function mat_123456(): void
    {
        $this->_run_test("123456", "T. Ing. Informatica", "2023-01-04", [
            "matricola" => "123456",
            "nome" => "GIUSEPPE",
            "cognome" => "ZEDDE",
            "email" => "g.zedde@studenti.unipi.it",

            "cdl" => "T. Ing. Informatica",
            "dataLaurea" => "2023-01-04",

            "media" => 23.655,
            "creditiCheFannoMedia" => 174,
            "creditiCurricolariConseguiti" => 177,
            "votoTesi" => 28,

            "bonus" => false,
            "mediaEsamiInformatici" => 23.667,
        ]);
    }

    public function mat_234567(): void
    {
        $this->_run_test("234567", "M. Ing. Elettronica", "2023-01-04", [
            "matricola" => "234567",
            "nome" => "GIOVANNI",
            "cognome" => "ATZENI",
            "email" => "g.atzeni@studenti.unipi.it",

            "cdl" => "M. Ing. Elettronica",
            "dataLaurea" => "2023-01-04",

            "media" => 24.559,
            "creditiCheFannoMedia" => 102,
            "creditiCurricolariConseguiti" => 102,
            "votoTesi" => 30,
        ]);
    }

    public function mat_345678(): void
    {
        $this->_run_test("345678", "T. Ing. Informatica", "2023-01-04", [
            "matricola" => "345678",
            "nome" => "LUIGI",
            "cognome" => "BRUSCHELLI",
            "email" => "l.bruschelli@studenti.unipi.it",

            "cdl" => "T. Ing. Informatica",
            "dataLaurea" => "2023-01-04",

            "media" => 25.564,
            "creditiCheFannoMedia" => 165,
            "creditiCurricolariConseguiti" => 177,
            "votoTesi" => 29,

            "bonus" => true,
            "mediaEsamiInformatici" => 25.75,
        ]);
    }

    public function mat_456789(): void
    {
        $this->_run_test("456789", "M. Ing. delle Telecomunicazioni", "2023-01-04", [
            "matricola" => "456789",
            "nome" => "JONATAN",
            "cognome" => "BARTOLETTI",
            "email" => "j.bartoletti@studenti.unipi.it",

            "cdl" => "M. Ing. delle Telecomunicazioni",
            "dataLaurea" => "2023-01-04",

            "media" => 32.625,
            "creditiCheFannoMedia" => 96,
            "creditiCurricolariConseguiti" => 96,
            "votoTesi" => 0,
        ]);
    }

    public function mat_567890(): void
    {
        $this->_run_test("567890", "M. Cybersecurity", "2023-01-04", [
            "matricola" => "567890",
            "nome" => "FRANCESCO",
            "cognome" => "ACERBI",
            "email" => "nome.cognome@studenti.unipi.it",

            "cdl" => "M. Cybersecurity",
            "dataLaurea" => "2023-01-04",

            "media" => 24.882,
            "creditiCheFannoMedia" => 102,
            "creditiCurricolariConseguiti" => 120,
            "votoTesi" => 0,
        ]);
    }
}
