Feature:
    As a user
    I want to complete a profile

    @ui @profile
    Scenario: I can login to my account
        Given a user with email "newuser@example.com" and password "password" exists
        When I log in using email "newuser@example.com" and password "password"
        Then I should be on "/profile/edit"
        And I should see "Please complete your profile"

    @ui @profile
    Scenario: I can view my profile as a new user
        Given a user with email "newuser@example.com"
        When I log in using email "newuser@example.com"
        Then I should be on "/profile/edit"
        And I should see "Please complete your profile"
        And the country "United Kingdom" should be displayed

    @javascript
    Scenario: I can fill in my profile
        Given a user with email "newuser@example.com"
        And I am logged in with "newuser@example.com"
        When I am on the profile edit page
        And I fill in my profile with the following details
            | username | country        | region  | city   | about                      | day | month | year | color | shape  |
            | New user | United Kingdom | England | London | This is some text about me | 1   | 1     | 1990 | Green | Circle |
        And I save my profile
        Then I should see the following profile details
            | username | region  | city   | about                      | day | month | year | color | shape  |
            | New user | England | London | This is some text about me | 1   | 1     | 1990 | Green | Circle |
