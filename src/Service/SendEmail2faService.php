<?php

namespace App\Service;

use App\Entity\User;
use App\Service\SendMailService;
use Symfony\Bundle\SecurityBundle\Security;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class SendEmail2faService implements AuthCodeMailerInterface
{
    protected $email;
    protected $security;

    public function __construct(SendMailService $email, Security $security)
    {
        $this->email = $email;
        $this->security = $security;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();

        // Send email
        /** @var User */
        $user = $this->security->getUser();
        $this->email->sendEmail(
            null,
            'Code de vérification : application elearning',
            $user->getEmail(),
            'Code de vérification',
            'authentication',
            [
                'user' => $user,
                'authCode' => $authCode
            ]
        );
    }
}