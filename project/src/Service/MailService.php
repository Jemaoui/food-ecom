<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendVerificationEmail(string $to, string $verificationUrl): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@ecom.com')
            ->to($to)
            ->subject('Verify your email address')
            ->htmlTemplate('emails/verification_email.html.twig')
            ->context([
                'verificationUrl' => $verificationUrl,
            ]);

        $this->mailer->send($email);
    }
}
