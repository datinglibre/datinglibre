Feature: Login

    @account
    Scenario: A blank username and password is denied
        Given I am on "/"
        And I press "Log in"
        Then I should see "Incorrect username or password"

    @account
    Scenario: I am notified when I enter an incorrect password
        Given the following profiles exist:
            | email               | attributes   | requirements   | city   | age |
            | newuser@example.com | blue, square | yellow, circle | London | 30  |
        And I am on "/"
        When I fill in "newuser@example.com" for "email"
        And I fill in "incorrect" for "password"
        And I press "Log in"
        Then I should see "Incorrect username or password"

    @account
    Scenario: Trying to log in to a non-existent account does not say whether the account exists or not
    Given
        And I am on "/"
        When I fill in "doesnotexist@example.com" for "email"
        And I fill in "incorrect" for "password"
        And I press "Log in"
        Then I should see "Incorrect username or password"