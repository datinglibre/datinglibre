Feature: I can register using my email address

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

    @ui
    @registration
    Scenario: My email address is kept private if I already have an account on the site
        Given the following profiles exist:
            | email               | attributes       | requirements   | city   | age |
            | newuser@example.com | woman, long_term | man, long_term | London | 30  |
        And I am on "/register"
        And I fill in "newuser@example.com" for "registration_form_email"
        And I fill in "password" for "registration_form_password"
        And I check "registration_form_agreeTerms"
        And I press "Register"
        Then I should be on "/"
        And I should see "Registration successful. Please check your email to confirm your account"
        And I should receive an already exists email to "newuser@example.com"
        And I can reset my password using the link