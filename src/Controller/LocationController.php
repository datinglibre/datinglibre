<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LocationController extends AbstractController
{
    private CountryRepository $countryRepository;
    private CategoryRepository $categoryRepository;
    private RegionRepository $regionRepository;
    private CityRepository $citiesRepository;

    public function __construct(
        CountryRepository $countryRepository,
        CategoryRepository $categoryRepository,
        RegionRepository $regionRepository,
        CityRepository $citiesRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->regionRepository = $regionRepository;
        $this->citiesRepository = $citiesRepository;
    }

    /**
     * @Route("/location/country/{countryId}/regions", name="country_regions")
     */
    public function displayRegions(Uuid $countryId)
    {
        if ($countryId == null) {
            throw $this->createNotFoundException('Country does not exist');
        }

        $serializer = new Serializer([new ObjectNormalizer()], [ new JsonEncoder()]);
        return new Response($serializer->serialize($this->regionRepository->findByCountry($countryId), 'json'));
    }

    /**
     * @Route("/location/region/{regionId}/cities", name="region_cities")
     */
    public function displayCities(Uuid $regionId)
    {
        if ($regionId == null) {
            throw $this->createNotFoundException('Region does not exist');
        }

        $serializer = new Serializer([new ObjectNormalizer()], [ new JsonEncoder()]);
        return new Response($serializer->serialize($this->citiesRepository->findByRegion($regionId), 'json'));
    }
}
