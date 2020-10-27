Feature: Normalise email addresses

    @ui
    @registration
    Scenario: My mix of upper and lowercase email with whitespace is normalised to lowercase without whitespace
        Given I am on "/register"
        And I fill in "   uSeR@example.com   " for "registration_form_email"
        And I fill in "password" for "registration_form_password"
        And I check "registration_form_agreeTerms"
        And I press "Register"
        And I should receive a confirmation email to "user@example.com"
        And I click the confirmation link and see "Your account is now enabled. You can now login"
        And I fill in "UsEr@example.com" for "email"
        And I fill in "password" for "password"
        And I press "Log in"
        Then I should see "Search"