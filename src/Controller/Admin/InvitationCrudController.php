<?php

namespace App\Controller\Admin;

use App\Entity\Invitation;
use App\Service\SendMailService;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\FormFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Uid\Uuid;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InvitationCrudController extends AbstractCrudController
{
    public function __construct(protected SendMailService $mailer, protected InvitationRepository $invitationRepository, protected FormFactoryInterface $formFactory) {}

    public static function getEntityFqcn(): string
    {
        return Invitation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendInvitations = Action::new('sendInvitations', 'Envoyer les invitations')
            ->linkToCrudAction('sendInvitations')
            ->displayIf(fn() => $this->invitationRepository->count(['isSent' => false]) > 0)
            ->addCssClass('btn btn-info')
            ->createAsGlobalAction();

        $importCsv = Action::new('importCsv', 'Importer CSV')
            ->linkToCrudAction('importCsv')
            ->addCssClass('btn btn-danger')
            ->createAsGlobalAction();

        $actions = parent::configureActions($actions);
        $actions->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, $sendInvitations)
            ->add(Crud::PAGE_INDEX, $importCsv)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Email de l\'invité']]),
            TextField::new('uuid', 'Uuid')
                ->hideWhenCreating(),
            AssociationField::new('user', 'Utilisateur')
                ->hideWhenCreating(),
            BooleanField::new('isSent', 'Envoyé ?')
                ->renderAsSwitch(false)
                ->hideWhenCreating(),
        ];
    }

    public function sendInvitations(
        EntityManagerInterface $entityManager,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        // Sélectionner toutes les invitations non envoyées
        $invitations = $entityManager->getRepository(Invitation::class)->findBy(['isSent' => false]);

        foreach ($invitations as $invitation) {
            // Envoyer l'email
            $this->mailer->sendEmail(
                null,
                'Invitation de l\'application e-learning',
                $invitation->getEmail(),
                'Invitation pour vous enregistrer sur le site e-learning',
                'invitation',
                ['uuid' => $invitation->getUuid()]
            );

            // Mettre à jour l'invitation comme envoyée
            $invitation->setSent(true);
            $entityManager->persist($invitation);
        }

        // Sauvegarder les changements
        $entityManager->flush();

        // Rediriger vers la page d'index avec un message flash
        $this->addFlash('success', 'Les invitations ont été envoyées avec succès.');
        $url = $adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl();

        return $this->redirect($url);
    }

    public function importCsv(
        Request $request,
        EntityManagerInterface $entityManager,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        // Créer un formulaire pour le fichier CSV
        $form = $this->formFactory->createBuilder()
            ->add('csv_file', FileType::class, ['label' => 'Fichier CSV (uniquement e-mails)', 'required' => true])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form->get('csv_file')->getData();

            if ($csvFile) {
                $handle = fopen($csvFile->getPathname(), 'r');
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $email = $row[0];

                    // Vérifier si l'email est valide
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->addFlash('danger', "L'email {$email} n'est pas valide et n'a pas été importé.");
                        continue;
                    }

                    // Vérifier si l'invitation existe déjà
                    $existingInvitation = $this->invitationRepository->findOneBy(['email' => $email]);

                    if (!$existingInvitation) {
                        $invitation = new Invitation();
                        $invitation->setEmail($email);
                        $invitation->setUuid(Uuid::v7());

                        $entityManager->persist($invitation);
                    }
                }
                fclose($handle);
                $entityManager->flush();

                $this->addFlash('success', 'Les invitations ont été importées avec succès.');
                $url = $adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl();
                return $this->redirect($url);
            }
        }

        return $this->render('admin/import_csv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
