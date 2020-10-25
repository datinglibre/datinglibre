Feature:
    As a user
    I want to be able to register an account

    @ui
    Scenario: I can register for an account
        When I am on the homepage
        And I follow "Register"
        And I fill in my registration details correctly with email "user@example.com"
        Then I should be on "/"
        And I should see "Registration successful. Please check your email to confirm your account"
        And I should receive a confirmation email to "user@example.com"
        And I click the confirmation link and see "Your account is now enabled. You can now login"

    @ui
    Scenario: I am notified when my account confirmation is unsuccessful
        When I am on the homepage
        And I follow "Register"
        And I fill in my registration details correctly with email "user@example.com"
        Then I should be on "/"
        And I should see "Registration successful. Please check your email to confirm your account"
        And I should receive a confirmation email to "user@example.com"
        And I click the confirmation link with the incorrect secret and see "Could not enable your account"

    @ui
    Scenario: I can view terms and conditions
        When I am on "/register"
        And I follow "Read terms and conditions"
        Then I should see "demonstration"