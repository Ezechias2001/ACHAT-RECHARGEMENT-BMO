<?php

namespace App\Controller;

use App\Entity\Commission;
use App\Repository\CommissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommissionController extends AbstractController
{
    #[Route('/commission', name: 'app_commission')]
    public function index(CommissionRepository $commissionRepository): Response
    {   
        $commission = new Commission();
        $commission = $commissionRepository->find(1);

        return $this->render('commission/index.html.twig', [
            'solde' => $commission->getSolde()
        ]);
    }
}
