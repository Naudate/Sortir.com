<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private VilleRepository $villeRepository)
    {
    }

    #[Route('/villes', name: 'gerer_villes')]
    public function gererVilles(Request $request)
    {
        $ville = new Ville();
        $villes = $this->villeRepository->findBy(array(), array('codePostal' => 'ASC'));
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->entityManager->persist($ville);
                $this->entityManager->flush();

                $this->addFlash("success", "Ville ajoutée");

                return $this->redirectToRoute('gerer_villes');
            } catch (UniqueConstraintViolationException $e) {
                // Gérer l'erreur d'unicité du code postal
                $this->addFlash("error", "Le code postal existe déjà.");
            }
        }

        return $this->render('ville/gererVille.html.twig', [
            'form'=> $form->createView(),
            'villes'=> $villes,
        ]);
    }

    #[Route('/villes/getApi/nom', name: 'autocomplete_ville')]
    public function autocompleteVilleNom(Request $request)
    {
        // Effectuez une requête à votre API pour récupérer les villes de France
        $apiData = $this->getCityDataFromApi('nom', $request->query->get('q'));

        // Retournez les données sous forme de JSON
        return new JsonResponse($apiData);
    }

    #[Route('/villes/getApi/codePostal', name: 'autocomplete_codePostal')]
    public function autocompleteVilleCodePostal(Request $request)
    {
        // Effectuez une requête à votre API pour récupérer les villes de France
        $apiData = $this->getCityDataFromApi('codePostal', $request->query->get('q'));

        // Retournez les données sous forme de JSON
        return new JsonResponse($apiData);
    }

    public function getCityDataFromApi($field, $query)
    {
        // Créez un client HTTP pour effectuer une requête à l'API
        $httpClient = HttpClient::create();

        // Définissez les paramètres de la requête
        $url = 'https://geo.api.gouv.fr/communes?';
        $parameters = [
            $field => $query,
            'format' => 'json',
            'boost' => 'population',
            'limit' => 5
        ];

        // Effectuez la requête GET à l'API
        $response = $httpClient->request('GET', $url, [
            'query' => $parameters,
        ]);

        // Vérifiez si la requête a réussi
        if ($response->getStatusCode() === 200) {
            // Convertissez la réponse JSON en tableau associatif
            $data = $response->toArray();
            // Renvoyez les données des villes
            return $data;
        } else {
            // Gérez les erreurs en conséquence (par exemple, journalisez l'erreur)
            return [];
        }
    }
}