<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class LoginPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "login";
    }

    public function login(string $email, string $password): void
    {
        $this->getElement('email')->setValue($email);
        $this->getElement('password')->setValue($password);
        $this->getElement('login')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'register' => '#register',
            'email' => '#email',
            'password' => '#password',
            'login' => '#login'
        ]);
    }
}
