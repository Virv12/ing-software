<?php
require_once('utils/CarrieraLaureando.php');

class TestCarrieraLaureando extends Test
{
    public function costruttore(): void
    {
          $val = new CarrieraLaureando(123456, "T. Ing. Informatica");
          $primo_esame = "ELETTROTECNICA";
          if ($val->esami[0]->nomeEsame != $primo_esame) {
              throw new Exception("esami non inseriti correttamente");
          }
    }

    public function media(): void
    {
        $val = new CarrieraLaureando(123456, "T. Ing. Informatica");
        if ($val->getMedia() < 23 || $val->getMedia() > 24) {
            throw new Exception("media non calcolata correttamente");
        }
    }
}
