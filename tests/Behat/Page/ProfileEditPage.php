<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

class ProfileEditPage extends SymfonyPage
{
    const COUNTRY_SELECT = '#profile_form_country';
    const REGION_SELECT = '#profile_form_region';
    const CITY_SELECT = '#profile_form_city';
    const SELECT_PICKER_VALUE = "$('%s').selectpicker('val', '%s');";
    const SELECT_PICKER_TRIGGER = "$('%s').trigger('hidden.bs.select');";
    const IS_OPTION_PRESENT = "$(\"%s option[value='%s']\").length > 0;";

    public function getRouteName(): string
    {
        return "profile_edit";
    }

    public function assertContains($message): void
    {
        Assert::contains($this->getDriver()->getContent(), $message);
    }

    public function assertCountryIsDisplayed($country): void
    {
        Assert::contains($this->getDriver()->getContent(), $country);
    }

    public function setCountry(Uuid $countryId): void
    {
        $this->getSession()->wait(5000, sprintf(
            self::IS_OPTION_PRESENT,
            self::COUNTRY_SELECT,
            $countryId
        ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_VALUE,
                self::COUNTRY_SELECT,
                $countryId
            ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_TRIGGER,
                self::COUNTRY_SELECT
            ));
    }

    public function setRegion(Uuid $regionId): void
    {
        $this->getSession()->wait(5000, sprintf(
            self::IS_OPTION_PRESENT,
            self::REGION_SELECT,
            $regionId
        ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_VALUE,
                self::REGION_SELECT,
                $regionId
            ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_TRIGGER,
                self::REGION_SELECT
            ));
    }

    public function setCity(Uuid $cityId): void
    {
        $this->getSession()->wait(5000, sprintf(
            self::IS_OPTION_PRESENT,
            self::CITY_SELECT,
            $cityId
        ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_VALUE,
                self::CITY_SELECT,
                $cityId
            ));

        $this->getSession()
            ->executeScript(sprintf(
                self::SELECT_PICKER_TRIGGER,
                self::CITY_SELECT
            ));
    }

    public function save(): void
    {
        // we want to keep the toolbar for convenience,
        // but it covers the "save" button here, so remove it.
        $this->getSession()->executeScript('$(".sf-toolbar").hide();');
        $this->getElement('save')->click();
    }

    public function setAbout($about): void
    {
        $this->getElement('about')->setValue($about);
    }

    public function fillInDob($day, $month, $year)
    {
        $this->getElement('year')->selectOption($year);
        $this->getElement('month')->selectOption($month);
        $this->getElement('day')->selectOption($day);
    }
}
