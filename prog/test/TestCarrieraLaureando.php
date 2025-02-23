<?php
require_once('utils/CarrieraLaureando.php');

class TestCarrieraLaureando extends Test
{
    public function costruttore(): void
    {
          $val = new CarrieraLaureando(123456);
          $primoEsame = "ELETTROTECNICA";
          if ($val->esami[0]->nomeEsame != $primoEsame) {
              throw new Exception("esami non inseriti correttamente");
          }
    }

    public function media(): void
    {
        $val = new CarrieraLaureando(123456);
        if ($val->getMedia() < 23 || $val->getMedia() > 24) {
            throw new Exception("media non calcolata correttamente");
        }
    }
}
