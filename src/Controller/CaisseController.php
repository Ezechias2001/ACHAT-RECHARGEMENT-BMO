<?php

namespace App\Controller;

use App\Form\CaisseType;
use App\Entity\Historique;
use App\Repository\CaisseRepository;
use App\Repository\HistoriqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CaisseController extends AbstractController
{   
    #[Route('/caisse', name: 'app_caisse_index')]
    public function index(Request $request, CaisseRepository $caisseRepository, HistoriqueRepository $historiqueRepository): Response
    {           
        $caisse = $caisseRepository->find(1);
        $historiques = $historiqueRepository->findByEnregistrement($caisse->getId());


        return $this->render('caisse/index.html.twig', [
            'solde' => $solde = $caisse->getSolde(),
            'historiques' => $historiques
        ]);
    }

    #[Route('/caisse/deposer', name: 'app_caisse_depot')]
    public function depot (Request $request,CaisseRepository $caisseRepository, EntityManagerInterface $entityManager): Response
    {   
        $historique = new Historique();
        $caisse = $caisseRepository->find(1);
        $form = $this->createForm(CaisseType::class, $caisse);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $montant = $form->get('montant')->getData();
            $caisse->deposer($montant);
            
            $historique->setType('Dépôt');
            $historique->setMontant($montant);
            $historique->setDate('22 Octobre 2022');
            
            $entityManager->persist($historique);
            $entityManager->flush();
            
            $caisse->addHistorique($historique);
            $caisseRepository->save($caisse, true);

            // Ajoute un attribut flash pour indiquer le succès du dépôt
            $this->addFlash('depot_success', true);

            return $this->redirectToRoute('app_caisse_index');
        }

        return $this->renderForm('caisse/deposer.html.twig', [
            'form' => $form,
            'controller_name' => 'CaisseController',
        ]);
    }
    #[Route('/caisse/retrait', name: 'app_caisse_retrait')]
    public function retrait (Request $request,CaisseRepository $caisseRepository, EntityManagerInterface $entityManager): Response
    {   
        $historique = new Historique();
        $caisse = $caisseRepository->find(1);

        $form = $this->createForm(CaisseType::class, $caisse);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $montant = $form->get('montant')->getData();
            $solde = $caisse->getSolde();
            if (  $solde < $montant ) {
                // Ajoute un attribut flash pour indiquer le succès du dépôt
                $this->addFlash('retrait_error', true);
                return $this->redirectToRoute('app_caisse_index');
            } else {
                $caisse->retirer($montant);
                $this->addFlash('retrait_success', true);
            }
            
            $historique->setType('Retrait');
            $historique->setMontant($montant);
            $historique->setDate('22 Octobre 2022');
            
            $entityManager->persist($historique);
            $entityManager->flush();
            
            $caisse->addHistorique($historique);
            $caisseRepository->save($caisse, true);

            return $this->redirectToRoute('app_caisse_index');
        }

        return $this->renderForm('caisse/retirer.html.twig', [
            'form' => $form,
        ]);
    }
}
