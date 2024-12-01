<?php

namespace App\Service\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Security\EmailVerifier;

class EmailService
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    public function sendConfirmationEmail(User $user, string $verifyRoute): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@ecom.com', 'Admin'))
            ->to((string) $user->getEmail())
            ->subject('Please Confirm your Email')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $this->emailVerifier->sendEmailConfirmation($verifyRoute, $user, $email);
    }
}
