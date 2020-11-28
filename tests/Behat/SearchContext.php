<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Filter;
use App\Entity\ProfileProjection;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\ProfileRepository;
use App\Repository\FilterRepository;
use App\Repository\RegionRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Tests\Behat\Page\SearchPage;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;

class SearchContext implements Context
{
    private UserService $userService;
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;
    private CityRepository $cityRepository;
    private RegionRepository $regionRepository;
    private SearchPage $searchPage;
    private FilterRepository $filterRepository;
    private array $profiles;

    public function __construct(
        UserService $userService,
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        CityRepository $cityRepository,
        RegionRepository $regionRepository,
        SearchPage $searchPage,
        FilterRepository $filterRepository
    ) {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->cityRepository = $cityRepository;
        $this->searchPage = $searchPage;
        $this->filterRepository = $filterRepository;
        $this->regionRepository = $regionRepository;
    }

    /**
     * @When the user :email searches for matches
     */
    public function theUserSearchesForMatches(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);

        $profile = $this->profileRepository->find($user->getId());
        Assert::notNull($profile);

        $city = $profile->getCity();

        $filter = $this->filterRepository->findOneBy(['user' => $user->getId()]);
        Assert::notNull($filter);

        $this->profiles = $this->profileRepository->findByLocation(
            $user->getId(),
            $city->getLatitude(),
            $city->getLongitude(),
            $filter->getDistance(),
            empty($filter->getRegion()) ? null : $filter->getRegion()->getId(),
            $filter->getMinAge(),
            $filter->getMaxAge(),
            false,
            0,
            10
        );
    }

    /**
     * @Then the user :email matches
     */
    public function theUserMatches(string $email)
    {
        Assert::true($this->containsProfile($this->userService->findByEmail($email)));
    }

    /**
     * @Then the user :email does not match
     */
    public function theUserDoesNotMatch(string $email)
    {
        Assert::false($this->containsProfile($this->userService->findByEmail($email)));
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
    public function iShouldSeeTheUser(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);
        $this->searchPage->assertContains($user->getId()->toRfc4122());
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
     * @Given the following filters exist:
     */
    public function theFollowingFiltersExist(TableNode $table)
    {
        foreach ($table as $row) {
            $user = $this->userService->findByEmail($row['email']);
            Assert::notNull($user);

            $filter = new Filter();
            $filter->setUser($user);

            if (array_key_exists('region', $row)) {
                $region = $this->regionRepository->findOneBy(['name' => $row['region']]);
                Assert::notNull($region);
                $filter->setRegion($region);
            } else {
                $filter->setRegion(null);
            }

            if (array_key_exists('distance', $row)) {
                $filter->setDistance((int) $row['distance']);
            } else {
                $filter->setDistance(null);
            }

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
        Assert::eq(count($this->profiles), count($users), sprintf(
            'Match count different, got %d',
            count($this->profiles)
        ));

        foreach ($users as $user) {
            Assert::true($this->containsProfile($user), $user->getId());
        }
    }

    private function getProfile(User $user): ?ProfileProjection
    {
        foreach ($this->profiles as $profile) {
            if ($user->getId()->toRfc4122() === $profile->getId()) {
                return $profile;
            }
        }

        return null;
    }

    private function containsProfile(User $user): bool
    {
        $found = false;

        foreach ($this->profiles as $profile) {
            if ($user->getId()->toRfc4122() === $profile->getId()) {
                $found = true;
            }
        }

        return $found;
    }
}
