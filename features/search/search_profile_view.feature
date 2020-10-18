Feature:
    As a user
    I want to be able to view another user's profile

    @ui @profile
    Scenario: I want to be able to view another user's profile
        Given the following profiles exist:
            | email                          | characteristics  | requirements   | city    | age |
            | chelsea_blue@example.com       | Square, Blue     | Yellow, Circle | London  | 30  |
            | westminster_yellow@example.com | Yellow, Circle   | Blue, Square   | London  | 30  |
        And the following filters exist
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 25      | 35      |
        And I am logged in with "chelsea_blue@example.com"
        When I navigate to the search page
        And I select the profile "westminster_yellow"
        Then I should see "westminster_yellow"
        And I should see "London"
        And I should see "England"
        And I should see "30"
