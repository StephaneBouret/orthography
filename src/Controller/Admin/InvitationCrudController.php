<?php

namespace App\Controller\Admin;

use App\Entity\Invitation;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InvitationCrudController extends AbstractCrudController
{
    public function __construct(protected SendMailService $mailer)
    {}

    public static function getEntityFqcn(): string
    {
        return Invitation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->disable(Action::EDIT)
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
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    // Before persist entity
    parent::persistEntity($entityManager, $entityInstance);
    $uuid = $entityInstance->getUuid();
    $this->mailer->sendEmail(
        'no-reply@monsite.net',
        'Invitation de l\'application e-learning',
        $entityInstance->getEmail(),
        'Invitation pour vous enregistrer sur le site e-learning',
        'invitation',
        ['uuid' => $uuid]
    );
    // After persist entity
}
}
