<?php
namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailVerificationTest extends WebTestCase
{
    public function testVerifyUserEmail(): void
    {
        $client = static::createClient();

        // Mock a user that needs email verification
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('testuser@example.com');

        $this->assertFalse($user->isVerified());

        // Simulate email verification link
        $client->loginUser($user);
        $client->request('GET', '/verify/email', ['token' => $user->getVerificationToken()]);

        $this->assertResponseRedirects('/login');

        // Check that the user is now verified
        $user = $userRepository->findOneByEmail('testuser@example.com');
        $this->assertTrue($user->isVerified());
    }
}
