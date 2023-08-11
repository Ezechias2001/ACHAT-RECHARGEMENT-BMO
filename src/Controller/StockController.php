<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/stock')]
class StockController extends AbstractController
{   
    private function ajouterStock( Stock $stock, int $Qte){
        $stock->setQuantite($stock->getQuantite() + $Qte);
    }  

    #[Route('/', name: 'app_stock_index', methods: ['GET'])]
    public function index(StockRepository $stockRepository): Response
    {   
        $stockLOW = $stockRepository->findOneByTypeCarte('LOW') ;
        $stockMID = $stockRepository->findOneByTypeCarte('MID') ;
        $stockHIGH = $stockRepository->findOneByTypeCarte('HIGH') ;

        return $this->render('stock/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
            'stockLOW' => $stockLOW->getQuantite(),
            'stockMID' => $stockMID->getQuantite(),
            'stockHIGH' => $stockHIGH->getQuantite(),
        ]);
    }
    
    #[Route('/excel', name: 'app_excel_stock_index', methods: ['GET'])]
    public function excel (StockRepository $stockRepository): Response
    {   
        $stocks = $stockRepository->findAll();
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplir les en-têtes
        $sheet->setCellValue('A1', 'Type de carte');
        $sheet->setCellValue('B1', 'Quantite');
        // ...


        $row = 2; // Commence à la deuxième ligne (après les en-têtes)
        foreach ($stocks as $stock) {
            $sheet->setCellValue('A' . $row, $stock->gettypeCarte());
            $sheet->setCellValue('B' . $row, $stock->getQuantite());

            $row++;
        }

        // Créer un objet Writer pour générer le fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définir le chemin où enregistrer le fichier Excel
        $filePath = 'C:/Users/Ezechias/Documents/stock.xlsx';

        // Enregistrer le fichier Excel
        $writer->save($filePath);

        return $this->file($filePath); // Téléchargement du fichier généré
        
        return $this->render('achat_gros/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StockRepository $stockRepository): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockRepository->save($stock, true);

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }
    
    #[Route('/ajout', name: 'app_stock_ajout', methods: ['GET', 'POST'])]
    public function ajout(Request $request, StockRepository $stockRepository): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if($form->getData()->getTypeCarte() == "LOW"){
                $stock = $stockRepository->findOneByTypeCarte('LOW');
                $Qte = $form->getData()->getQuantite();
                $this->ajouterStock($stock, $Qte);
            } 
            elseif($form->getData()->getTypeCarte() == "MID"){
                $stock = $stockRepository->findOneByTypeCarte('MID');
                $Qte = $form->getData()->getQuantite();
                $this->ajouterStock($stock, $Qte);
            } 
            elseif($form->getData()->getTypeCarte() == "HIGH"){
                $stock = $stockRepository->findOneByTypeCarte('HIGH');
                $Qte = $form->getData()->getQuantite();
                $this->ajouterStock($stock, $Qte);
            }

            $stockRepository->save($stock, true);

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/ajout.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, StockRepository $stockRepository): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockRepository->save($stock, true);

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, StockRepository $stockRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stock->getId(), $request->request->get('_token'))) {
            $stockRepository->remove($stock, true);
        }

        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }
}
