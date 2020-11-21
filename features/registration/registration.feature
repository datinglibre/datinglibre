Feature:
    As a user
    I want to be able to register an account

    @ui
    @registration
    Scenario: I can register for an account
        When I am on the homepage
        And I follow "Register"
        And I fill in my registration details correctly with email "user@example.com"
        Then I should be on "/"
        And I should see "Registration successful. Please check your email to confirm your account"
        And I should receive a confirmation email to "user@example.com"
        And I click the confirmation link and see "Your account is now enabled. You can now login"

    @ui
    @registration
    Scenario: I am notified when my account confirmation is unsuccessful
        When I am on the homepage
        And I follow "Register"
        And I fill in my registration details correctly with email "user@example.com"
        Then I should be on "/"
        And I should see "Registration successful. Please check your email to confirm your account"
        And I should receive a confirmation email to "user@example.com"
        And I click the confirmation link with the incorrect secret and see "Could not enable your account"

    @ui
    @registration
    Scenario: I can view terms and conditions
        When I am on "/register"
        And I follow "Read terms and conditions"
        Then I should see "demonstration"

    @ui
    @registration
    Scenario: I cannot register with an invalid email address
        When I am on "/register"
        And I check "registration_form_agreeTerms"
        And I fill in "registration_form_email" with "userexample.com"
        And I fill in "registration_form_password" with "password"
        And I press "register"
        Then I should see "Please enter a valid email address"

    @ui
    @registration
    Scenario: I cannot register with a blank email address
        When I am on "/register"
        And I check "registration_form_agreeTerms"
        And I fill in "registration_form_email" with ""
        And I fill in "registration_form_password" with "password"
        And I press "register"
        Then I should see "Please enter an email address"

    @ui
    @registration
    Scenario: I cannot register with a blank password
        When I am on "/register"
        And I check "registration_form_agreeTerms"
        And I fill in "registration_form_email" with "user@example.com"
        And I fill in "registration_form_password" with ""
        And I press "register"
        Then I should see "Please enter a password"

    @ui
    @registration
    Scenario: Password should be at least 8 characters
        When I am on "/register"
        And I check "registration_form_agreeTerms"
        And I fill in "registration_form_email" with "user@example.com"
        And I fill in "registration_form_password" with "123456"
        And I press "register"
        Then I should see "Your password should be at least 8 characters"