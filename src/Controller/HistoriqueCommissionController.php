<?php

namespace App\Controller;

use App\Entity\HistoriqueCommission;
use App\Form\HistoriqueCommissionType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HistoriqueCommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/historique/commission')]
class HistoriqueCommissionController extends AbstractController
{
    #[Route('/', name: 'app_historique_commission_index', methods: ['GET'])]
    public function index(HistoriqueCommissionRepository $historiqueCommissionRepository): Response
    {            
        $soldeCommission = 0 ;
        $montantTotal = 0 ;        
        $brut = 0 ;

        $historiqueCommissions = $historiqueCommissionRepository->findAll();
        
        foreach ($historiqueCommissions as $historiqueCommission ) {
            $soldeCommission = $soldeCommission + $historiqueCommission->getGainCommission() ;
            $montantTotal = $montantTotal + $historiqueCommission->getMontant() ;
            $brut = $montantTotal - $soldeCommission;
        } 

        return $this->render('historique_commission/index.html.twig', [
            'historique_commissions' => $historiqueCommissionRepository->findAll(),
            'soldeCommission' => $soldeCommission,
            'montantTotal' => $montantTotal,
            'brut' => $brut
        ]);
    }
    
    #[Route('/excel', name: 'app_excel_historique_commission_index', methods: ['GET'])]
    public function excel (HistoriqueCommissionRepository $historiqueCommissionRepository): Response
    {   
        $historiqueCommissions = $historiqueCommissionRepository->findAll();
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplir les en-têtes
        $sheet->setCellValue('A1', 'Operation');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Type de Carte');
        $sheet->setCellValue('D1', 'Montant');
        $sheet->setCellValue('E1', 'Commission');
        $sheet->setCellValue('F1', 'Date');
        // ...


        $row = 2; // Commence à la deuxième ligne (après les en-têtes)
        foreach ($historiqueCommissions as $historiqueCommission) {
            $sheet->setCellValue('A' . $row, $historiqueCommission->getOperation());
            $sheet->setCellValue('B' . $row, $historiqueCommission->getNom());
            $sheet->setCellValue('C' . $row, $historiqueCommission->getTypeCarte());
            $sheet->setCellValue('D' . $row, $historiqueCommission->getMontant());
            $sheet->setCellValue('E' . $row, $historiqueCommission->getGainCommission());
            $sheet->setCellValue('F' . $row, $historiqueCommission->getDateCreation());

            $row++;
        }

        // Créer un objet Writer pour générer le fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définir le chemin où enregistrer le fichier Excel
        $filePath = 'C:/Users/Ezechias/Documents/historique_des_operations.xlsx';

        // Enregistrer le fichier Excel
        $writer->save($filePath);

        return $this->file($filePath); // Téléchargement du fichier généré
        
        return $this->render('achat_gros/index.html.twig', [
            'historique_commissions' => $historiqueCommissionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_historique_commission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HistoriqueCommissionRepository $historiqueCommissionRepository): Response
    {
        $historiqueCommission = new HistoriqueCommission();
        $form = $this->createForm(HistoriqueCommissionType::class, $historiqueCommission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $historiqueCommissionRepository->save($historiqueCommission, true);

            return $this->redirectToRoute('app_historique_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('historique_commission/new.html.twig', [
            'historique_commission' => $historiqueCommission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historique_commission_show', methods: ['GET'])]
    public function show(HistoriqueCommission $historiqueCommission): Response
    {
        return $this->render('historique_commission/show.html.twig', [
            'historique_commission' => $historiqueCommission,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_historique_commission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HistoriqueCommission $historiqueCommission, HistoriqueCommissionRepository $historiqueCommissionRepository): Response
    {
        $form = $this->createForm(HistoriqueCommissionType::class, $historiqueCommission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $historiqueCommissionRepository->save($historiqueCommission, true);

            return $this->redirectToRoute('app_historique_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('historique_commission/edit.html.twig', [
            'historique_commission' => $historiqueCommission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historique_commission_delete', methods: ['POST'])]
    public function delete(Request $request, HistoriqueCommission $historiqueCommission, HistoriqueCommissionRepository $historiqueCommissionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historiqueCommission->getId(), $request->request->get('_token'))) {
            $historiqueCommissionRepository->remove($historiqueCommission, true);
        }

        return $this->redirectToRoute('app_historique_commission_index', [], Response::HTTP_SEE_OTHER);
    }
}
