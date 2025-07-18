<?php
class GestioneCarrieraStudente
{
    public static function restituisciCarrieraStudente(string $matricola): mixed
    {
        $str = file_get_contents("data/{$matricola}_esami.json");
        return json_decode($str, true);
    }

    public static function restituisciAnagraficaStudente(string $matricola): mixed
    {
        $str = file_get_contents("data/{$matricola}_anagrafica.json");
        return json_decode($str, true);
    }
}
