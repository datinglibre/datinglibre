<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Profile;
use App\Entity\Region;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\ProfileRepository;
use App\Repository\RegionRepository;
use App\Repository\UserRepository;
use App\Service\ProfileService;
use App\Service\RequirementService;
use App\Service\UserAttributeService;
use App\Service\UserService;
use App\Tests\Behat\Page\LoginPage;
use App\Tests\Behat\Page\ProfileEditPage;
use App\Tests\Behat\Page\ProfileIndexPage;
use App\Tests\Behat\Page\SearchPage;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use DateTimeInterface;
use Webmozart\Assert\Assert;

class ProfileEditContext implements Context
{
    private UserService $userService;
    private LoginPage $loginPage;
    private ProfileIndexPage $profileViewPage;
    private ProfileEditPage $profileEditPage;
    private ProfileService $profileService;
    private SearchPage $searchPage;
    private ProfileRepository $profileRepository;
    private CityRepository $cityRepository;
    private CountryRepository $countryRepository;
    private RegionRepository $regionRepository;
    private UserRepository $userRepository;
    private UserAttributeService $userAttributeService;
    private RequirementService $requirementService;

    public function __construct(
        UserService $userService,
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        ProfileService $profileService,
        UserAttributeService $userAttributeService,
        RequirementService $requirementService,
        CityRepository $cityRepository,
        RegionRepository $regionRepository,
        CountryRepository $countryRepository,
        LoginPage $loginPage,
        ProfileIndexPage $profileIndexPage,
        ProfileEditPage $profileEditPage,
        SearchPage $searchPage
    ) {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->profileService = $profileService;
        $this->loginPage = $loginPage;
        $this->profileViewPage = $profileIndexPage;
        $this->searchPage = $searchPage;
        $this->profileEditPage = $profileEditPage;
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
        $this->regionRepository = $regionRepository;
        $this->userAttributeService = $userAttributeService;
        $this->requirementService = $requirementService;
    }

    /**
     * @Then I am redirected to the profile edit page
     */
    public function iAmRedirectedToTheProfileEditPage()
    {
        Assert::true($this->profileEditPage->isOpen());
    }

    /**
     * @Given the following profiles exist:
     */
    public function theFollowingProfilesExist(TableNode $table)
    {
        foreach ($table as $row) {
            $user = $this->createProfile(
                $row['email'],
                array_key_exists('age', $row) ? (int) $row['age'] : null,
                array_key_exists('city', $row) ? $row['city'] : null,
                array_key_exists('last_login', $row)
                    ? DateTime::createFromFormat('Y-m-d H:s', $row['last_login']) : new DateTime(),
                array_key_exists('state', $row) ? $row['state'] : null
            );

            if (array_key_exists('requirements', $row)) {
                $this->requirementService->createRequirementsByAttributeNames($user, explode(',', $row['requirements']));
            }

            if (array_key_exists('attributes', $row)) {
                $this->userAttributeService->createUserAttributesByAttributeNames($user, explode(',', $row['attributes']));
            }
        }
    }

    private function createProfile(
        string $email,
        ?int $age,
        ?string $city,
        DateTimeInterface $lastLogin,
        ?string $state
    ): User {
        $user = $this->userService->create($email, 'password', true, []);
        $user->setLastLogin($lastLogin ?? new DateTime());
        $this->userRepository->save($user);

        $profile = new Profile();
        if ($age !== null) {
            $profile->setDob((new DateTime())
                ->modify(sprintf('-%d year', $age))
                ->modify('-1 day'));
        }

        if ($city !== null) {
            $profile->setCity($this->getCity($city));
        }

        if ($state !== null) {
            $profile->setState($state);
        }

        $profile->setUsername(str_replace('@example.com', '', $email));
        $profile->setUser($user);

        $this->profileRepository->save($profile);

        return $user;
    }

    private function getCity(string $city): City
    {
        $city = $this->cityRepository->findOneBy([City::NAME => $city]);
        Assert::notNull($city, 'City was null');
        return $city;
    }

    /**
     * @Given I open the my own profile index page
     */
    public function iOpenTheMyOwnProfileViewPage()
    {
        $this->profileViewPage->open();
        Assert::true($this->profileViewPage->isOpen(), 'Profile view page not open');
    }

    /**
     * @When I am on the profile edit page
     */
    public function iAmOnTheProfileEditPage()
    {
        $this->profileEditPage->open();
        Assert::true($this->profileEditPage->isOpen());
    }

    /**
     * @Given I select :regionName as my region
     */
    public function iSelectAsMyRegion($regionName)
    {
        $region = $this->regionRepository->findOneBy([Region::NAME => $regionName]);
        Assert::notNull($region);
        $this->profileEditPage->setRegion($region->getId());
    }

    /**
     * @Given I select :countryName as my country
     */
    public function iSelectAsMyCountry($countryName)
    {
        $country = $this->countryRepository->findOneBy([Country::NAME => $countryName]);
        Assert::notNull($country);
        $this->profileEditPage->setCountry($country->getId());
    }

    /**
     * @Given I select :cityName as my city
     */
    public function iSelectAsMyCity($cityName)
    {
        $city = $this->cityRepository->findOneBy([City::NAME => $cityName]);
        Assert::notNull($city);
        $this->profileEditPage->setCity($city->getId());
    }

    /**
     * @When I select the location:
     */
    public function iSelectTheLocation(TableNode $table): void
    {
        foreach ($table as $row) {
            $this->iSelectAsMyCountry($row['country']);
            $this->iSelectAsMyRegion($row['region']);
            $this->iSelectAsMyCity($row['city']);
        }
    }

    /**
     * @When I close the toolbar
     */
    public function closeToolbar()
    {
        $this->profileEditPage->closeToolbar();
    }

    /**
     * @Then I should see the age for :year :month :day
     */
    public function iShouldSeeTheAgeFor(int $year, int $month, int $day)
    {
        // this is actually the profile view page
        $this->profileEditPage->assertContains(DateTime::createFromFormat(
            'j-n-Y',
            sprintf('%d-%d-%d', $day, $month, $year)
        )
                ->diff(new DateTime())
                ->format('%Y'));
    }
}
