<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
public function getFunctions()
{
    return [
        new TwigFunction('date_diff', [$this, 'dateDiff']),
    ];
}

public function dateDiff($dateStart, $dateEnd)
{
    $dateTimeStart = new \DateTime($dateStart);
    $dateTimeEnd = new \DateTime($dateEnd);

    // Calculer la différence entre les deux dates
    $interval = $dateTimeStart->diff($dateTimeEnd);

    // Retourner la différence sous forme de chaîne formatée
    return $interval->format('%d jours, %h heures, %i minutes');
}
}