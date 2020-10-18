<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Ramsey\Uuid\UuidInterface;
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

    public function setUsername($username)
    {
        $this->getElement('username')->setValue($username);
    }

    public function setCountry(UuidInterface $countryId): void
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

    public function setRegion(UuidInterface $regionId): void
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

    public function setCity(UuidInterface $cityId): void
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

    public function fillInShape(string $shape): void
    {
        $this->getElement('shape')->selectOption($shape);
    }

    public function fillInColor(string $color): void
    {
        $this->getElement('color')->selectOption($color);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'year' => '#profile_form_dob_year',
            'month' => '#profile_form_dob_month',
            'day' => '#profile_form_dob_day',
            'username' => '#profile_form_username',
            'save' => '#profile_form_save',
            'about' => '#profile_form_about',
            'color' => '#profile_form_color',
            'shape' => '#profile_form_shape'
        ]);
    }
}
