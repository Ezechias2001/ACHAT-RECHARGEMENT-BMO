<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\AchatGros;
use App\Entity\Commission;
use App\Form\AchatGrosType;
use App\Repository\StockRepository;
use App\Entity\HistoriqueCommission;
use App\Repository\AchatGrosRepository;
use App\Repository\CommissionRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HistoriqueCommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/achat/gros')]
class AchatGrosController extends AbstractController
{
    #[Route('/', name: 'app_achat_gros_index', methods: ['GET'])]
    public function index(AchatGrosRepository $achatGrosRepository): Response
    {   
        $soldeCommission = 0 ;
        $montantTotal = 0 ;        
        $brut = 0 ;

        $achatGros = $achatGrosRepository->findAll();
        
        foreach ($achatGros as $achatGro ) {
            $soldeCommission = $soldeCommission + $achatGro->getCommission() ;
            $montantTotal = $montantTotal + $achatGro->getMontant() ;
            $brut = $montantTotal - $soldeCommission;
        } 
        return $this->render('achat_gros/index.html.twig', [
            'achat_gros' => $achatGrosRepository->findAll(),
            'soldeCommission' => $soldeCommission,
            'montantTotal' => $montantTotal,
            'brut' => $brut
        ]);
    }
    
    #[Route('/excel', name: 'app_excel_achat_gros_index', methods: ['GET'])]
    public function excel (AchatGrosRepository $achatGrosRepository): Response
    {   
        $achatGros = $achatGrosRepository->findAll();
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplir les en-têtes
        $sheet->setCellValue('A1', 'Nom Service');
        $sheet->setCellValue('B1', 'Type de carte');
        $sheet->setCellValue('C1', 'Nombre');
        $sheet->setCellValue('D1', 'Montant');
        $sheet->setCellValue('E1', 'Commission');
        $sheet->setCellValue('F1', 'Date');
        // ...


        $row = 2; // Commence à la deuxième ligne (après les en-têtes)
        foreach ($achatGros as $achatGro) {
            $sheet->setCellValue('A' . $row, $achatGro->getNomService());
            $sheet->setCellValue('B' . $row, $achatGro->getTypeCarte());
            $sheet->setCellValue('C' . $row, $achatGro->getNombre());
            $sheet->setCellValue('D' . $row, $achatGro->getMontant());
            $sheet->setCellValue('E' . $row, $achatGro->getCommission());
            $sheet->setCellValue('F' . $row, $achatGro->getDateCreation());

            $row++;
        }

        // Créer un objet Writer pour générer le fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définir le chemin où enregistrer le fichier Excel
        $filePath = 'C:/Users/Ezechias/Documents/achat_gros.xlsx';

        // Enregistrer le fichier Excel
        $writer->save($filePath);

        return $this->file($filePath); // Téléchargement du fichier généré
        
        return $this->render('achat_gros/index.html.twig', [
            'achat_gros' => $achatGrosRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_achat_gros_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AchatGrosRepository $achatGrosRepository, CommissionRepository $commissionRepository, HistoriqueCommissionRepository $historiqueCommissionRepository, StockRepository $stockRepository ): Response
    {
        $achatGro = new AchatGros();
        // $commission = new Commission();
        $historique = new HistoriqueCommission();     
        $stock = new Stock();
        
        $form = $this->createForm(AchatGrosType::class, $achatGro);
        $form->handleRequest($request);

        
        // $commission = $commissionRepository->find(1);


        if ($form->isSubmitted() && $form->isValid()) {
            $nbre = $form->getData()->getNombre();
            //Insertion automatisée du montant
            if ( $form->getData()->getTypeCarte() === "LOW" ) {
                $stock = $stockRepository->findOneByTypeCarte('LOW');
                $QteStock  = $stock->getQuantite()  ;
                if ($nbre > $QteStock) {
                    $this->addFlash('error','message');
                    return $this->redirectToRoute('app_achat_gros_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    $achatGro->setMontant((1200*$nbre));
                    // $commission->setSolde($commission->getSolde()+ (100*$nbre));
                    $historique->setGainCommission((100*$nbre)); 
                    $achatGro->setCommission((100*$nbre));  
                    //Gestion de stock
                    $stock->setQuantite($stock->getQuantite() - $nbre);
                    
                    $historique->setNom($form->getData()->getNomService());
                    $historique->setMontant(($form->getData()->getMontant())*$nbre);
                    $historique->setTypeCarte($form->getData()->getTypeCarte());
                    $historique->setDatecreation($form->getData()->getDateCreation());
                                    
                    $historique->setOperation("Achat en Gros de Carte");
                    
                    $historiqueCommissionRepository->save($historique, true);
                    $achatGro->setRelation($historique);

                    $achatGrosRepository->save($achatGro, true);
                    // $commissionRepository->save($commission, true);
                    $historiqueCommissionRepository->save($historique, true);
                    $stockRepository->save($stock, true);
                }                

            } elseif ($form->getData()->getTypeCarte() === "MID") {
                $stock = $stockRepository->findOneByTypeCarte('MID');
                $QteStock  = $stock->getQuantite()  ;
                if ($nbre > $QteStock) {
                    $this->addFlash('error','message');
                } else {
                    $achatGro->setMontant((2500*$nbre));
                    // $commission->setSolde($commission->getSolde()+(300*$nbre));
                    $historique->setGainCommission((300*$nbre));
                    $achatGro->setCommission((300*$nbre)); 
                    //Gestion de stock               
                    $stock->setQuantite($stock->getQuantite() - $nbre);
                    
                    $historique->setNom($form->getData()->getNomService());
                    $historique->setMontant(($form->getData()->getMontant())*$nbre);
                    $historique->setTypeCarte($form->getData()->getTypeCarte());
                    $historique->setDatecreation($form->getData()->getDateCreation());
                                    
                    $historique->setOperation("Achat en Gros de Carte");
                    
                    $historiqueCommissionRepository->save($historique, true);
                    $achatGro->setRelation($historique);

                    $achatGrosRepository->save($achatGro, true);
                    // $commissionRepository->save($commission, true);
                    $historiqueCommissionRepository->save($historique, true);
                    $stockRepository->save($stock, true);
                }
                

            } elseif ($form->getData()->getTypeCarte() === "HIGH") {
                $stock = $stockRepository->findOneByTypeCarte('HIGH');
                $QteStock  = $stock->getQuantite()  ;
                if ($nbre > $QteStock) {
                    dd($nbre, $stock);
                    $this->addFlash('error','message');
                } else {
                    $achatGro->setMontant((7000*$nbre));
                    // $commission->setSolde($commission->getSolde()+(1000*$nbre));
                    $historique->setGainCommission((1000*$nbre));
                    $achatGro->setCommission((1000*$nbre)); 
                    //Gestion de stock             
                    $stock->setQuantite($stock->getQuantite() - $nbre);
                    
                    $historique->setNom($form->getData()->getNomService());
                    $historique->setMontant($form->getData()->getMontant());
                    $historique->setTypeCarte($form->getData()->getTypeCarte());
                    $historique->setDatecreation($form->getData()->getDateCreation());              
                    $historique->setOperation("Achat en Gros de Carte");
                    
                    $historiqueCommissionRepository->save($historique, true);
                    $achatGro->setRelation($historique);
                    

                    $achatGrosRepository->save($achatGro, true);
                    // $commissionRepository->save($commission, true);
                    $historiqueCommissionRepository->save($historique, true);
                    $stockRepository->save($stock, true);
                }
            }
            return $this->redirectToRoute('app_achat_gros_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat_gros/new.html.twig', [
            'achat_gro' => $achatGro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_achat_gros_show', methods: ['GET'])]
    public function show(AchatGros $achatGro): Response
    {
        return $this->render('achat_gros/show.html.twig', [
            'achat_gro' => $achatGro,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_achat_gros_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AchatGros $achatGro, AchatGrosRepository $achatGrosRepository): Response
    {
        $form = $this->createForm(AchatGrosType::class, $achatGro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $achatGrosRepository->save($achatGro, true);

            return $this->redirectToRoute('app_achat_gros_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat_gros/edit.html.twig', [
            'achat_gro' => $achatGro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_achat_gros_delete', methods: ['POST'])]
    public function delete(Request $request, AchatGros $achatGro, AchatGrosRepository $achatGrosRepository, StockRepository $stockRepository): Response
    {   
        
        $type = $achatGro->getTypeCarte();
        $stock = $stockRepository->findOneByTypeCarte($type);
        $stock->setQuantite($stock->getQuantite() + $achatGro->getNombre());

        if ($this->isCsrfTokenValid('delete'.$achatGro->getId(), $request->request->get('_token'))) {
            $achatGrosRepository->remove($achatGro, true);
        }

        return $this->redirectToRoute('app_achat_gros_index', [], Response::HTTP_SEE_OTHER);
    }
}
