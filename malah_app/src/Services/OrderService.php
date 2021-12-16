<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $date = (new \DateTime);
    }

    public function createOrder($cart)
    {
    }

    public function saveCart($data, $user)
    {
    }

    public function generateUuid()
    {
        // initialise le générateur de nombres aléatoires Mersenne Twister
        mt_srand((float)microtime() * 100000);

        // strtoupper : Renvoie une chaine en maj
        // uniqid : Génère un identifiant unique
        $charid = strtoupper(md5(uniqid(rand(), true)));

        // Génère une chaine d'un octet à partir d'un nombre
        $hyphen = chr(45);

        // substr : Retourne un segment de chaine
        $uuid = ""
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);

        return $uuid;
    }
}
