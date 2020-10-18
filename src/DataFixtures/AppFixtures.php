<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Attribute;
use App\Entity\BlockReason;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Region;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $objectManager;
    public const LONDON_LATITUDE = 51.50853;
    public const LONDON_LONGITUDE = -0.12574;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->objectManager = $manager;

        $this->createTestUser();

        $this->createLocations();

        $this->createBlockReason('block.no_reason');
        $this->createBlockReason('block.spam');

        $colors = $this->createCategory("Color");
        $this->createAttribute($colors, "Blue");
        $this->createAttribute($colors, "Green");
        $this->createAttribute($colors, "Yellow");

        $shape = $this->createCategory("Shape");
        $this->createAttribute($shape, "Square");
        $this->createAttribute($shape, "Circle");
        $this->createAttribute($shape, "Triangle");
    }

    /**
     * Solely for testing the UI, will be removed by behat tests
     */
    private function createTestUser()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setEnabled(true);
        $user->setPassword($this->encoder->encodePassword($user, 'password'));
        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    private function createLocations(): void
    {
        $unitedKingdom = $this->createCountry('United Kingdom');
        $unitedStates = $this->createCountry('United States');

        $newYorkState = $this->createRegion($unitedStates, 'New York State');

        $this->createCity(
            $unitedStates,
            $newYorkState,
            'New York',
            -73.9352,
            40.7128
        );

        $england = $this->createRegion($unitedKingdom, 'England');
        $scotland = $this->createRegion($unitedKingdom, 'Scotland');

        $this->createCity(
            $unitedKingdom,
            $scotland,
            'Edinburgh',
            3.1883,
            55.9533
        );

        $this->createCity(
            $unitedKingdom,
            $england,
            'London',
            -0.125739,
            51.508530
        );

        $this->createCity(
            $unitedKingdom,
            $england,
            'Bristol',
            -2.5966,
            51.4552
        );

        $this->createCity(
            $unitedKingdom,
            $england,
            'Bath',
            -2.3617,
            51.3751
        );

        $this->createCity(
            $unitedKingdom,
            $england,
            'Oxford',
            1.2577,
            51.7520
        );
    }

    private function createAttribute(Category $category, string $name): Attribute
    {
        $attribute = new Attribute();
        $attribute->setName($name);
        $attribute->setCategory($category);
        $this->objectManager->persist($attribute);
        $this->objectManager->flush();

        return $attribute;
    }

    private function createCategory(string $name): Category
    {
        $category = new Category();
        $category->setName($name);
        $this->objectManager->persist($category);
        $this->objectManager->flush();

        return $category;
    }

    private function createCountry(string $name): Country
    {
        $country = new Country();
        $country->setName($name);
        $this->objectManager->persist($country);
        $this->objectManager->flush();

        return $country;
    }

    private function createRegion(Country $country, string $name): Region
    {
        $region = new Region();
        $region->setName($name);
        $region->setCountry($country);

        $this->objectManager->persist($region);
        $this->objectManager->flush();
        return $region;
    }

    private function createCity(
        Country $country,
        Region $region,
        string $name,
        float $longitude,
        float $latitude
    ): City {
        $city = new City();
        $city->setName($name);
        $city->setCountry($country);
        $city->setRegion($region);
        $city->setLatitude($latitude);
        $city->setLongitude($longitude);

        $this->objectManager->persist($city);
        $this->objectManager->flush();

        return $city;
    }

    private function createBlockReason(string $reason)
    {
        $this->objectManager->persist((new BlockReason())->setName($reason));
    }
}
