<?php

namespace App\Form\DataTransformer;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class VilleTransformer implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function transform($ville)
    {
        if (null === $ville) {
            return '';
        }

        return $ville->getCodePostal() . ' - ' . $ville->getNom();
    }

    public function reverseTransform($villeString)
    {
        if (!$villeString) {
            return null;
        }

        $parts = explode(' - ', $villeString);
        $codePostal = $parts[0];
        $nom = $parts[1];

        $ville = $this->entityManager
        ->getRepository(Ville::class)
        ->findOneBy(['codePostal' => $codePostal]);

        if (null === $ville) {
            $ville = new Ville();
            $ville->setCodePostal($codePostal);
            $ville->setNom($nom);
        }

        return $ville;
    }
}
