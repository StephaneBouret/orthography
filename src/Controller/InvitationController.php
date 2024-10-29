<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Google\GoogleService;
use App\Repository\InvitationRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InvitationController extends AbstractController
{
    #[Route('/invitation/{uuid}', name: 'app_invitation', requirements: ['uuid' => '[\w-]+'])]
    public function index($uuid, InvitationRepository $invitationRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, AvatarService $avatarService, GoogleService $googleService): Response
    {
        $invitation = $invitationRepository->findOneBy([
            'uuid' => $uuid
        ]);
        if ($invitation->getUser() !== null) {
            throw new \Exception('Cette invitation est déjà utilisée !');
        }

        $user = new User();
        $user->setEmail($invitation->getEmail());

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setFirstname(ucfirst($form->get('firstname')->getData()))
                ->setLastname(mb_strtoupper($form->get('lastname')->getData()))
                ->setCity(ucfirst($form->get('city')->getData()))
                ->setAvatar($avatarService->createAndAssignAvatar($user));
            $invitation->setUser($user);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $this->addFlash('warning', 'Merci de consulter vos emails');
            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'invitation' => $invitation,
            'google_api_key' => $googleService->getGoogleKey(),
        ]);
    }
}
