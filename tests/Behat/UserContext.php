<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Tests\Behat\Page\LoginPage;
use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class UserContext extends RawMinkContext
{
    public const DEFAULT_PASSWORD = 'password';
    private UserRepository $userRepository;
    private UserService $userService;
    private LoginPage $loginPage;
    private HttpClientInterface $httpClient;
    private string $passwordResetEmail;

    public function __construct(
        UserRepository $userRepository,
        UserService $userService,
        LoginPage $loginPage
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->loginPage = $loginPage;
        $this->httpClient = HttpClient::create();
    }

    /**
     * @BeforeScenario
     */
    public function setup()
    {
        $this->passwordResetEmail = '';
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $this->userService->delete(null, $user->getId());
        }
    }

    /**
     * @When I log in using email :email and password :password
     */
    public function iLogInUsingEmailAndPassword($email, $password)
    {
        $this->loginPage->open();
        $this->loginPage->login($email, $password);
    }

    /**
     * @When I log in using email :email
     */
    public function iLogInUsingEmail($email)
    {
        $this->iLogInUsingEmailAndPassword($email, UserContext::DEFAULT_PASSWORD);
    }

    /**
     * @Given a user with email :email and password :password exists
     */
    public function aUserWithEmailAndPasswordExists(string $email, string $password)
    {
        $this->userService->create($email, $password, true, []);
    }

    /**
     * @Given a user with email :email
     */
    public function aUserWithEmail($email)
    {
        $this->aUserWithEmailAndPasswordExists($email, self::DEFAULT_PASSWORD);
    }

    /**
     * @Given I am logged in with :email
     */
    public function amLoggedInWithEmail(string $email)
    {
        $this->iLogInUsingEmail($email);
    }

    /**
     * @Given a moderator exists with email :email
     */
    public function aModeratorExistsWithEmail(string $email)
    {
        $this->userService->create(
            $email,
            self::DEFAULT_PASSWORD,
            true,
            [User::MODERATOR]
        );
    }

    /**
     * @Then I should receive a password reset email to :email
     */
    public function iShouldReceiveAPasswordResetEmailTo(string $email)
    {
        $emailResponse = $this->httpClient->request('GET', sprintf(MailHogConstants::EMAIL_REST_URL, 'localhost', $email));
        Assert::eq($emailResponse->getStatusCode(), 200);
        $emails = json_decode($emailResponse->getContent(), true);
        Assert::eq($emails['items'][0]['Content']['Headers']['Subject'][0], 'Reset password');
        $this->passwordResetEmail = quoted_printable_decode($emails['items'][0]['Content']['Body']);
        Assert::contains($this->passwordResetEmail, 'Click the link below to reset your password. Please ignore this email if you did not request to reset your password');
    }

    /**
     * @Then I click the password reset link I should see :message
     */
    public function iClickThePasswordResetLinkIShouldSee(string $message)
    {
        $passwordResetLink = $this->getPasswordResetLink();

        $this->getSession()->visit($passwordResetLink);
        Assert::contains($this->getSession()->getPage()->getContent(), $message);
    }

    /**
     * @Given I click the password reset link with the incorrect secret I should see :message
     */
    public function iClickThePasswordResetLinkWithTheIncorrectSecretIShouldSee(string $message)
    {
        $passwordResetLink = str_replace('&secret=', '&secret=123', $this->getPasswordResetLink());

        $this->getSession()->visit($passwordResetLink);
        Assert::contains($this->getSession()->getPage()->getContent(), $message);
    }

    public function getPasswordResetLink(): string
    {
        $crawler = new Crawler($this->passwordResetEmail);
        return $crawler->filterXPath('//a/@href')->getNode(0)->nodeValue;
    }
}
