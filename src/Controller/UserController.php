<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher): Response
    {   
        $existantPassword = $user->getPassword();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($oldPassword !== null && $newPassword !== null && $confirmPassword !== null) {
                
                $hashedOldPassword = $passwordHasher->hashPassword(
                    $user,
                    $oldPassword
                );
        
                if ($existantPassword === $hashedOldPassword && $newPassword === $confirmPassword) {
                    $hashedNewPassword = $passwordHasher->hashPassword(
                        $user,
                        $newPassword
                    );
                    $user->setPassword($hashedNewPassword);
                } else {
                    return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
                }
            } else {
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }


            $uploadedFile = $form->get('photoProfil')->getData();
            // Générer un nom de fichier unique
            $newFilename = uniqid().'.'.$uploadedFile->guessExtension();

            // Déplacer le fichier vers le dossier 'assets/img'
            $uploadedFile->move(
                $this->getParameter('image_profil'),
                $newFilename
            );

            $user->setPhotoProfil('assets/photoProfil/'.$newFilename);
            
            // $photo = $user->setPhotoProfil('assets/imgRecuperes/'.$newFilename);

            $userRepository->save($user, true);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
