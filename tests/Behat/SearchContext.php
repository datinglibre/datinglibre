<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Filter;
use App\Entity\ProfileProjection;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\ProfileRepository;
use App\Repository\FilterRepository;
use App\Repository\UserRepository;
use App\Service\MatchingService;
use App\Service\UserService;
use App\Tests\Behat\Page\SearchPage;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;

class SearchContext implements Context
{
    const TEST_RADIUS = 80_000;
    private MatchingService $matchingService;
    private UserService $userService;
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;
    private CityRepository $cityRepository;
    private SearchPage $searchPage;
    private FilterRepository $filterRepository;
    private array $profiles;

    public function __construct(
        UserService $userService,
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        MatchingService $matchingService,
        CityRepository $cityRepository,
        SearchPage $searchPage,
        FilterRepository $filterRepository
    ) {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->matchingService = $matchingService;
        $this->profileRepository = $profileRepository;
        $this->cityRepository = $cityRepository;
        $this->searchPage = $searchPage;
        $this->filterRepository = $filterRepository;
    }

    /**
     * @When the user :email searches for matches
     */
    public function theUserSearchesForMatches(string $email)
    {
        $user = $this->userRepository->findOneBy([User::EMAIL => $email]);
        Assert::notNull($user, "User not found");

        $profile = $this->profileRepository->find($user->getId());
        Assert::notNull($profile);

        $city = $profile->getCity();

        $this->profiles = $this->profileRepository->findProfilesByDistance(
            $user->getId(),
            $city->getLatitude(),
            $city->getLongitude(),
            self::TEST_RADIUS,
            false,
            0,
            10
        );
    }

    /**
     * @Then the user :email matches
     */
    public function theUserMatches($email)
    {
        Assert::true($this->containsProfile($this->userRepository->findOneBy([User::EMAIL => $email])));
    }

    /**
     * @Then the user :email does not match
     */
    public function theUserDoesNotMatch($email)
    {
        Assert::false($this->containsProfile($this->userRepository->findOneBy([User::EMAIL => $email])));
    }

    /**
     * @Then the following users match:
     */
    public function theFollowingUsersMatch(TableNode $table)
    {
        $users = [];
        foreach ($table as $row) {
            $user = $this->userService->findByEmail(trim($row['email']));
            Assert::notNull($user);
            $users[] = $user;
        }

        $this->containsMatches($users);
    }

    /**
     * @Then I should see the user :email
     */
    public function iShouldSeeTheUser($email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);
        $this->searchPage->assertContains($user->getId()->toString());
    }

    /**
     * @Given I navigate to the search page
     */
    public function iNavigateToTheSearchPage()
    {
        $this->searchPage->open();
        Assert::true($this->searchPage->isOpen());
    }

    /**
     * @Then I should see that no profiles match
     */
    public function iShouldSeeThatNoProfilesMatch()
    {
        $this->searchPage->assertContains('No results, please try changing your search criteria');
    }

    /**
     * @Given the following filters exist
     */
    public function theFollowingFiltersExist(TableNode $table)
    {
        foreach ($table as $row) {
            $user = $this->userService->findByEmail($row['email']);
            Assert::notNull($user);

            $filter = new Filter();
            $filter->setUser($user);
            $filter->setDistance((int) $row['distance']);
            $filter->setMinAge((int) $row['min_age']);
            $filter->setMaxAge((int) $row['max_age']);

            $this->filterRepository->save($filter);
        }
    }


    /**
     * @Given I select the profile :username
     */
    public function iSelectTheProfile(string $username)
    {
        $this->searchPage->selectUsername($username);
    }

    /**
     * @Then the image of :email should not appear
     */
    public function theImageOfShouldNotAppear(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);

        $profile = $this->getProfile($user);
        Assert::notNull($profile);

        Assert::null($profile->getImageUrl());
        Assert::null($profile->getImageState());
        Assert::false($profile->isImagePresent());
    }

    /**
     * @Then the image of :email should appear
     */
    public function theImageOfShouldAppear(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);

        $profile = $this->getProfile($user);
        Assert::notNull($profile);

        Assert::notNull($profile->getImageUrl());
        Assert::notNull($profile->getImageState());
        Assert::true($profile->isImagePresent());
    }

    private function containsMatches(array $users): void
    {
        Assert::eq(count($this->profiles), count($users), 'Match count different');

        foreach ($users as $user) {
            Assert::true($this->containsProfile($user), $user->getId());
        }
    }

    private function getProfile(User $user): ?ProfileProjection
    {
        foreach ($this->profiles as $profile) {
            if ($user->getId()->toString() === $profile->getId()) {
                return $profile;
            }
        }

        return null;
    }

    private function containsProfile(User $user): bool
    {
        $found = false;

        foreach ($this->profiles as $profile) {
            if ($user->getId()->toString() === $profile->getId()) {
                $found = true;
            }
        }

        return $found;
    }
}
