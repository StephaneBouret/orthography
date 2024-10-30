<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendMailService
{
    protected $mailer;
    protected $security;
    protected $defaultFrom;

    public function __construct(MailerInterface $mailer, string $defaultFrom)
    {
        $this->mailer = $mailer;
        $this->defaultFrom = $defaultFrom;
    }

    public function sendEmail(
        string $from = null,
        string $name,
        string $to,
        string $subject,
        string $template,
        array $context
    ) {
        $email = new TemplatedEmail();
        $email->from(new Address($from ?? $this->defaultFrom, $name))
            ->to($to)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context)
            ->subject($subject);

        $this->mailer->send($email);
    }
}
