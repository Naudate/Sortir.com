<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    #[Route('/villes/get', name: 'autocomplete_ville')]
    public function autocompleteVille(Request $request)
    {
        // Effectuez une requête à votre API pour récupérer les villes de France
        $apiData = $this->getCityDataFromApi($request->query->get('q'));

        // Retournez les données sous forme de JSON
        return new JsonResponse($apiData);
    }

    public function getCityDataFromApi($query)
    {
        // Créez un client HTTP pour effectuer une requête à l'API
        $httpClient = HttpClient::create();

        // Définissez les paramètres de la requête
        $url = 'https://geo.api.gouv.fr/communes?';
        $parameters = [
            'nom' => $query,
            'format' => 'json',
            'boost' => 'population',
            'limit' => 5
        ];

        dump($url);
        dump($parameters);

        // Effectuez la requête GET à l'API
        $response = $httpClient->request('GET', $url, [
            'query' => $parameters,
        ]);
        dump($response);
        // Vérifiez si la requête a réussi
        if ($response->getStatusCode() === 200) {
            // Convertissez la réponse JSON en tableau associatif
            $data = $response->toArray();
            // Renvoyez les données des villes
            dump($data);
            return $data;
        } else {
            // Gérez les erreurs en conséquence (par exemple, journalisez l'erreur)
            return [];
        }
    }
}
