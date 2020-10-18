<?php

declare(strict_types=1);

namespace App\Form;

class PasswordUpdateForm
{
    private $secret;
    private $userId;
    private $password;
    private $confirmedPassword;

    public function getSecret()
    {
        return $this->secret;
    }

    public function setSecret($secret): void
    {
        $this->secret = $secret;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getConfirmedPassword()
    {
        return $this->confirmedPassword;
    }

    public function setConfirmedPassword($confirmedPassword): void
    {
        $this->confirmedPassword = $confirmedPassword;
    }
}
