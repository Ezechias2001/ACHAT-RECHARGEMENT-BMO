<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Commission;
use App\Entity\AchatDetail;
use App\Form\AchatDetailType;
use App\Repository\StockRepository;
use App\Entity\HistoriqueCommission;
use App\Repository\CommissionRepository;
use App\Repository\AchatDetailRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HistoriqueCommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/achat/detail')]
class AchatDetailController extends AbstractController
{
    #[Route('/', name: 'app_achat_detail_index', methods: ['GET'])]
    public function index(AchatDetailRepository $achatDetailRepository): Response
    {    
        $soldeCommission = 0 ;
        $montantTotal = 0 ;        
        $brut = 0 ;

        $achatDetails = $achatDetailRepository->findAll();
        
        foreach ($achatDetails as $achatDetail ) {
            $soldeCommission = $soldeCommission + $achatDetail->getCommission() ;
            $montantTotal = $montantTotal + $achatDetail->getMontant() ;
            $brut = $montantTotal - $soldeCommission;
        } 

        return $this->render('achat_detail/index.html.twig', [
            'achat_details' => $achatDetailRepository->findAll(),
            'soldeCommission' => $soldeCommission,
            'montantTotal' => $montantTotal,
            'brut' => $brut
        ]);
    }
    #[Route('/excel', name: 'app_excel_achat_detail_index', methods: ['GET'])]
    public function excel (AchatDetailRepository $achatDetailRepository): Response
    {   
        $achatDetails = $achatDetailRepository->findAll();
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplir les en-têtes
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Date de Naissance');
        $sheet->setCellValue('C1', 'Sexe');
        $sheet->setCellValue('D1', 'Pièce');
        $sheet->setCellValue('E1', 'Numero de Pièce');
        $sheet->setCellValue('F1', 'Type de carte');
        $sheet->setCellValue('G1', 'Montant');
        $sheet->setCellValue('H1', 'Commission');
        $sheet->setCellValue('I1', 'Date de création');
        // ...


        $row = 2; // Commence à la deuxième ligne (après les en-têtes)
        foreach ($achatDetails as $achatDetail) {
            $sheet->setCellValue('A' . $row, $achatDetail->getNom());
            $sheet->setCellValue('B' . $row, $achatDetail->getDateNaissance());
            $sheet->setCellValue('C' . $row, $achatDetail->getSexe());
            $sheet->setCellValue('D' . $row, $achatDetail->getPiece());
            $sheet->setCellValue('E' . $row, $achatDetail->getNumeroPiece());
            $sheet->setCellValue('F' . $row, $achatDetail->getTypeCarte());
            $sheet->setCellValue('G' . $row, $achatDetail->getMontant());
            $sheet->setCellValue('H' . $row, $achatDetail->getCommission());
            $sheet->setCellValue('I' . $row, $achatDetail->getDateCreation());

            $row++;
        }

        // Créer un objet Writer pour générer le fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définir le chemin où enregistrer le fichier Excel
        $filePath = 'C:/Users/Ezechias/Documents/achat_detail.xlsx';

        // Enregistrer le fichier Excel
        $writer->save($filePath);

        return $this->file($filePath); // Téléchargement du fichier généré
        
        return $this->render('achat_detail/index.html.twig', [
            'achat_details' => $achatDetailRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_achat_detail_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AchatDetailRepository $achatDetailRepository, CommissionRepository $commissionRepository, HistoriqueCommissionRepository $historiqueCommissionRepository, StockRepository $stockRepository ): Response
    {
        $achatDetail = new AchatDetail();
        // $commission = new Commission();
        $historique = new HistoriqueCommission();        
        $stock = new Stock();

        $form = $this->createForm(AchatDetailType::class, $achatDetail);
        $form->handleRequest($request);
        
        $commission = $commissionRepository->find(1);

        if ($form->isSubmitted() && $form->isValid()) {
            //Insertion automatisée du montant
            if ( $form->getData()->getTypeCarte() === "LOW" ) {

                $achatDetail->setMontant(1500);
                // $commission->setSolde($commission->getSolde()+ 400);
                $historique->setGainCommission(400);   
                $achatDetail->setCommission(400);
                
                //Gestion de stock
                $stock = $stockRepository->findOneByTypeCarte('LOW');
                $stock->setQuantite($stock->getQuantite() - 1);

            } elseif ($form->getData()->getTypeCarte() === "MID") {
                $achatDetail->setMontant(3500);
                // $commission->setSolde($commission->getSolde()+1300);
                $historique->setGainCommission(1300);
                $achatDetail->setCommission(1300);

                //Gestion de stock
                $stock = $stockRepository->findOneByTypeCarte('MID');                
                $stock->setQuantite($stock->getQuantite() - 1);

            } elseif ($form->getData()->getTypeCarte() === "HIGH") {
                $achatDetail->setMontant(11000);
                // $commission->setSolde($commission->getSolde()+5000);
                $historique->setGainCommission(5000);
                $achatDetail->setCommission(5000);

                //Gestion de stock
                $stock = $stockRepository->findOneByTypeCarte('HIGH');                
                $stock->setQuantite($stock->getQuantite() - 1);
            }

            $uploadedFile = $form->get('imagePiece')->getData();
            // Générer un nom de fichier unique
            $newFilename = uniqid().'.'.$uploadedFile->guessExtension();

            // Déplacer le fichier vers le dossier 'assets/img'
            $uploadedFile->move(
                $this->getParameter('image_directory'),
                $newFilename
            );

            $achatDetail->setImagePiece('assets/Pieces/'.$newFilename);
            
            $historique->setNom($form->getData()->getNom());
            $historique->setMontant($form->getData()->getMontant());
            $historique->setTypeCarte($form->getData()->getTypeCarte());
            $historique->setDatecreation($form->getData()->getDateCreation());
                               
            $historique->setOperation("Achat de Carte");
            $historiqueCommissionRepository->save($historique, true);
            $achatDetail->setRelation($historique);

            $achatDetailRepository->save($achatDetail, true);
            // $commissionRepository->save($commission, true);
            $stockRepository->save($stock, true);

            return $this->redirectToRoute('app_achat_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat_detail/new.html.twig', [
            'achat_detail' => $achatDetail,
            'form' => $form,
            'commission' => $commission->getSolde()
        ]);
    }

    // #[Route('/{id}', name: 'app_achat_detail_show', methods: ['GET'])]
    // public function show(AchatDetail $achatDetail): Response
    // {
    //     return $this->render('achat_detail/show.html.twig', [
    //         'achat_detail' => $achatDetail,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_achat_detail_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AchatDetail $achatDetail, AchatDetailRepository $achatDetailRepository): Response
    {
        $form = $this->createForm(AchatDetailType::class, $achatDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $achatDetailRepository->save($achatDetail, true);

            return $this->redirectToRoute('app_achat_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat_detail/edit.html.twig', [
            'achat_detail' => $achatDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_achat_detail_delete', methods: ['POST'])]
    public function delete(Request $request, AchatDetail $achatDetail, AchatDetailRepository $achatDetailRepository, StockRepository $stockRepository): Response
    {   
        
        $type = $achatDetail->getTypeCarte();
        $stock = $stockRepository->findOneByTypeCarte($type);
        $stock->setQuantite($stock->getQuantite() + 1 );
        
        if ($this->isCsrfTokenValid('delete'.$achatDetail->getId(), $request->request->get('_token'))) {
            $achatDetailRepository->remove($achatDetail, true);
        }

        return $this->redirectToRoute('app_achat_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
