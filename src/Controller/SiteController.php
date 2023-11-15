<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\LieuFormType;
use App\Form\SiteFormType;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/site', name: 'site')]
#[IsGranted('ROLE_ADMIN')]
class SiteController extends AbstractController
{
    #[Route('/list/{page}', name: '_home', defaults: ['page'=> 1])]
    public function index(int $page, SiteRepository $siteRepository): Response
    {
        //récupération de la liste des sites
        $sites = $siteRepository->findSiteWithPagination($page);
        $maxPage = ceil($siteRepository->count([]) / 8);

        return $this->render('site/index.html.twig', [
            'sites'=> $sites,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }
    #[Route('/show/{id}', name: '_details', requirements: ['id'=> '\d+'])]
    public function details(int $id, SiteRepository $siteRepository):Response{

        //vérification que le site exite
        $site = $siteRepository->find($id);

        if (empty($site)){
            throw new \Exception('Le site que tu recherches n\'est pas de ce monde', 404);
        }
        return $this->render('site/details.html.twig', [
            'site' => $site
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create (Request $request, SiteRepository $siteRepository, EntityManagerInterface $entityManager){

        $site = new Site();
        $siteForm = $this->createForm(SiteFormType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            //vérification que ce nom n'existe pas
            $verifySite = $siteRepository->findByNom($site->getNom());
            if(!empty($verifySite)){
                $this->addFlash('error', 'Ce nom existe déjà');
                return $this->render('site/create.html.twig', [
                    'siteForm'=> $siteForm->createView(),
                ]);
            }
            //dd($site);
            $entityManager->persist($site);
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau sité créé avec succès');
            return $this->redirectToRoute('site_details', array('id'=> $site->getId()));
        }

        return $this->render('site/create.html.twig', [
            'siteForm'=> $siteForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit', requirements: ['id'=> '\d+'])]
    public function edit (Request $request, int $id, EntityManagerInterface $entityManager, SiteRepository $siteRepository){

        // récuperation du site
        $site = $siteRepository->find($id);

        if (empty($site)){
            throw  new \Exception('Je ne connais pas ce site ! En revanche, je connais les fraudeurs', 404);
        }

        $siteForm = $this->createForm(SiteFormType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            $verifySite = $siteRepository->findByNom($site->getNom());
            if(!empty($verifySite)){
                $this->addFlash('error', 'Ce nom existe déjà');
                return $this->render('site/edit.html.twig', [
                    'siteForm'=> $siteForm->createView(),
                ]);
            }

            $entityManager->persist($site);
            $entityManager->flush();

            $this->addFlash('success', 'Le site a été modifié avec succès');
            return $this->redirectToRoute('site_details', array('id'=> $site->getId()));
        }

        return $this->render('site/edit.html.twig', [
            'siteForm'=> $siteForm->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id'=> '\d+'])]
    public function  delete (int $id, SiteRepository $siteRepository, EntityManagerInterface $entityManager){
        //vérification que le site exite
        $site = $siteRepository->find($id);

        if (empty($site)){
            throw new \Exception('Le site que tu recherches n\'est pas de ce monde', 404);
        }

        // suppression du site
        $entityManager->remove($site);
        $entityManager->flush();

        $this->addFlash('success', 'Le site a été supprimé avec succès');
        return $this->redirectToRoute('site_home');
    }
}
