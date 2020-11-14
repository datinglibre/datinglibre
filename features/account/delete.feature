Feature:
    I can close my account

    @account
    Scenario: I successfully delete my account
        Given the following profiles exist:
            | email               | attributes   | requirements   | city   | age |
            | newuser@example.com | square, blue | yellow, circle | London | 30  |
        And the user "newuser@example.com" has uploaded a profile image
        And I am logged in with "newuser@example.com"
        When I go to "/user/delete"
        Then I should see "Confirm your password below to permanently delete your account"
        And I fill in "delete_account_form_password_first" with "password"
        And I fill in "delete_account_form_password_second" with "password"
        And I press "Permanently delete my account"
        Then I should be on "/"