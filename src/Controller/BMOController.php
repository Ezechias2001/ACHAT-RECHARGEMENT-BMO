<?php

namespace App\Controller;

use App\Entity\BMO;
use App\Form\BMOType;
use App\Repository\BMORepository;
use App\Repository\CaisseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bmo')]
class BMOController extends AbstractController
{
    #[Route('/', name: 'app_b_m_o_index', methods: ['GET'])]
    public function index(Request $request, BMORepository $bMORepository, CaisseRepository $caisseRepository): Response
    {   
        $caisse = $caisseRepository->find(1);

        return $this->render('bmo/index.html.twig', [
            'b_m_os' => $bMORepository->findAll(),
            'depotSuccess' => !empty($depotSuccess),
            'depotError' => !empty($depotError),
            'retraitSuccess' => !empty($retraitSuccess),
            'solde' => $solde = $caisse->getSolde(),
            'depot' => $solde = $caisse->getDepot(),
            'retrait' => $solde = $caisse->getRetrait()
        ]);
    }

    #[Route('/new', name: 'app_b_m_o_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BMORepository $bMORepository, CaisseRepository $caisseRepository): Response
    {
        $bMO = new BMO();
        $form = $this->createForm(BMOType::class, $bMO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération de l'instance de la caisse
            $caisse = $caisseRepository->find(1);
            // Récupération automatique du montant en fonction du type d'opération
            $montant = $form->getData()->getMontant(); 
            // Remplacez "getData()" par la méthode appropriée pour récupérer le montant
    
            if ($form->getData()->getType() === 'Dépôt') {
                $bMO->setMontant($montant);
                                
                if (!$caisse->retirer($montant)) {
                    // Ajoute un attribut flash pour indiquer l'erreur du depot
                     $this->addFlash('depot_error', true);
                    return $this->redirectToRoute('app_b_m_o_index');
                }
                $caisse->setDepot($caisse->getDepot() + $montant);

                // Ajoute un attribut flash pour indiquer le succès du depot
                $this->addFlash('depot_success', true);
            } 
            elseif ($form->getData()->getType() === 'Retrait') {
                $bMO->setMontant($montant);  
                $caisse->deposer($montant);   
                $caisse->setRetrait($caisse->getRetrait() + $montant);

                // Ajoute un attribut flash pour indiquer le succès du retrait
                 $this->addFlash('retrait_success', true);
            }
    
            $bMORepository->save($bMO, true);
            return $this->redirectToRoute('app_b_m_o_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bmo/new.html.twig', [
            'b_m_o' => $bMO,
            'form' => $form,
            'solde_insuffisant' => $request->query->getBoolean('solde_insuffisant', false),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_b_m_o_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BMO $bMO, BMORepository $bMORepository): Response
    {
        $form = $this->createForm(BMOType::class, $bMO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bMORepository->save($bMO, true);

            return $this->redirectToRoute('app_b_m_o_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bmo/edit.html.twig', [
            'b_m_o' => $bMO,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_b_m_o_delete', methods: ['POST'])]
    public function delete(Request $request, BMO $bMO, BMORepository $bMORepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bMO->getId(), $request->request->get('_token'))) {
            $bMORepository->remove($bMO, true);
        }

        return $this->redirectToRoute('app_b_m_o_index', [], Response::HTTP_SEE_OTHER);
    }
}
