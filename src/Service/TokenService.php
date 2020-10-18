<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use DateTime;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class TokenService
{
    private TokenRepository $tokenRepository;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(
        TokenRepository  $tokenRepository,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function save(User $user, string $type): Token
    {
        $this->tokenRepository->deleteByUserIdAndType($user->getId(), $type);
        $token = new Token();
        $token->setType($type);
        $token->setUser($user);
        $token->setSecret($this->tokenGenerator->generateToken());
        return $this->tokenRepository->save($token);
    }

    public function verify(User $user, string $secret, string $type): bool
    {
        $token = $this->tokenRepository->findOneBy([Token::USER => $user->getId(),
            Token::SECRET => $secret, Token::TYPE => $type]);

        if ($token === null) {
            return false;
        }

        if ($this->isOlderThanADay($token)) {
            $this->tokenRepository->deleteByUserIdAndType($user->getId(), $type);
            return false;
        }

        $this->tokenRepository->deleteByUserIdAndType($user->getId(), $type);
        return true;
    }

    private function isOlderThanADay(Token $token): bool
    {
        $currentTime = new DateTime();
        $dateInterval = $currentTime->diff($token->getCreatedAt());
        return $dateInterval->days >= 1;
    }
}
