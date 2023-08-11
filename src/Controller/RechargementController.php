<?php

namespace App\Controller;

use App\Entity\Caisse;
use App\Entity\Commission;
use App\Entity\Rechargement;
use App\Form\RechargementType;
use App\Entity\HistoriqueCommission;
use App\Repository\CaisseRepository;
use App\Repository\CommissionRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\RechargementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HistoriqueCommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rechargement')]
class RechargementController extends AbstractController
{
    #[Route('/', name: 'app_rechargement_index', methods: ['GET'])]
    public function index(RechargementRepository $rechargementRepository): Response
    {   
        $soldeCommission = 0 ;
        $montantTotal = 0 ;        
        $brut = 0 ;

        $rechargements = $rechargementRepository->findAll();
        
        foreach ($rechargements as $rechargement ) {
            $soldeCommission = $soldeCommission + $rechargement->getCommission() ;
            $montantTotal = $montantTotal + $rechargement->getMontant() ;
            $brut = $montantTotal - $soldeCommission;
        } 

        return $this->render('rechargement/index.html.twig', [
            'rechargements' => $rechargementRepository->findAll(),
            'soldeCommission' => $soldeCommission,
            'montantTotal' => $montantTotal,
            'brut' => $brut
        ]);
    }
    
    #[Route('/excel', name: 'app_excel_rechargement_index', methods: ['GET'])]
    public function excel (RechargementRepository $rechargementRepository): Response
    {   
        $rechargements = $rechargementRepository->findAll();
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplir les en-têtes
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Numero Carte');
        $sheet->setCellValue('C1', 'Type carte');
        $sheet->setCellValue('D1', 'Montant');
        $sheet->setCellValue('E1', 'Commission');
        $sheet->setCellValue('F1', 'Date');
        // ...


        $row = 2; // Commence à la deuxième ligne (après les en-têtes)
        foreach ($rechargements as $rechargement) {
            $sheet->setCellValue('A' . $row, $rechargement->getNom());
            $sheet->setCellValue('B' . $row, $rechargement->getNumeroCarte());
            $sheet->setCellValue('C' . $row, $rechargement->getTypeCarte());
            $sheet->setCellValue('D' . $row, $rechargement->getMontant());
            $sheet->setCellValue('E' . $row, $rechargement->getCommission());
            $sheet->setCellValue('F' . $row, $rechargement->getDateCreation());

            $row++;
        }

        // Créer un objet Writer pour générer le fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définir le chemin où enregistrer le fichier Excel
        $filePath = 'C:/Users/Ezechias/Documents/rechargement.xlsx';

        // Enregistrer le fichier Excel
        $writer->save($filePath);

        return $this->file($filePath); // Téléchargement du fichier généré
        
        return $this->render('rechargement/index.html.twig', [
            'rechargements' => $rechargementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rechargement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RechargementRepository $rechargementRepository, CommissionRepository $commissionRepository, HistoriqueCommissionRepository $historiqueCommissionRepository, CaisseRepository $caisseRepository): Response
    {
        $rechargement = new Rechargement();
        // $commission = new Commission();
        $historique = new HistoriqueCommission();
        $caisse = new Caisse();

        $form = $this->createForm(RechargementType::class, $rechargement);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $montant = $form->getData()->getMontant();
            if ( $montant < 5000) {
                $commissionCalcule = ($montant*0.10);
                // $commission->setSolde($commission->getSolde()+ $commissionCalcule);
                $historique->setGainCommission($commissionCalcule);   
                $rechargement->setCommission($commissionCalcule);

            } elseif ($form->getData()->getMontant() >= 5000){
                // $commission->setSolde($commission->getSolde()+ 500);
                $historique->setGainCommission(500);   
                $rechargement->setCommission(500);

            }

            $caisse = $caisseRepository->findOneById(1);
            $caisse->retirer($montant) ;

            $historique->setNom($form->getData()->getNom());
            $historique->setMontant($form->getData()->getMontant());
            $historique->setTypeCarte($form->getData()->getTypeCarte());
            $historique->setDatecreation($form->getData()->getDateCreation());
                               
            $historique->setOperation("Rechargement de Carte");
            
            $historiqueCommissionRepository->save($historique, true);
            $rechargement->setRelation($historique);

            $rechargementRepository->save($rechargement, true);
            // $commissionRepository->save($commission, true);
            $historiqueCommissionRepository->save($historique, true);

            return $this->redirectToRoute('app_rechargement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rechargement/new.html.twig', [
            'rechargement' => $rechargement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rechargement_show', methods: ['GET'])]
    public function show(Rechargement $rechargement): Response
    {
        return $this->render('rechargement/show.html.twig', [
            'rechargement' => $rechargement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rechargement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rechargement $rechargement, RechargementRepository $rechargementRepository, CaisseRepository $caisseRepository): Response
    {   
        $caisse = $caisseRepository->findOneById(1); 
        
        $ancienSolde = $rechargement->getMontant();

        $form = $this->createForm(RechargementType::class, $rechargement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $nouveauSolde = $form->getData()->getMontant() ; 

            if ($nouveauSolde < $ancienSolde) {
                $caisse->deposer($ancienSolde - $nouveauSolde);
            } elseif ($nouveauSolde > $ancienSolde) {
                $caisse->retirer($nouveauSolde - $ancienSolde );
            }

            $rechargementRepository->save($rechargement, true);

            return $this->redirectToRoute('app_rechargement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rechargement/edit.html.twig', [
            'rechargement' => $rechargement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rechargement_delete', methods: ['POST'])]
    public function delete(Request $request, Rechargement $rechargement, RechargementRepository $rechargementRepository,CaisseRepository $caisseRepository): Response
    {   
        $caisse = $caisseRepository->findOneById(1);
        $caisse->deposer($rechargement->getMontant());

        if ($this->isCsrfTokenValid('delete'.$rechargement->getId(), $request->request->get('_token'))) {
            $rechargementRepository->remove($rechargement, true);
        }

        return $this->redirectToRoute('app_rechargement_index', [], Response::HTTP_SEE_OTHER);
    }
}
