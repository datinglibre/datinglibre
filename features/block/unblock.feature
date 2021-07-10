Feature:
    As a user I want to unblock another user

    @block
    Scenario: I want to block another user
        Given the following profiles exist:
            | email                          | attributes     | requirements   | city   | age |
            | chelsea_blue@example.com       | blue, square   | yellow, circle | London | 30  |
            | westminster_yellow@example.com | yellow, circle | blue, square   | London | 30  |
        And the following blocks exist
            | email                    | block                          |
            | chelsea_blue@example.com | westminster_yellow@example.com |
        When I log in using email "chelsea_blue@example.com"
        And I am on "/blocks"
        Then I should see "westminster_yellow"
        And I follow "westminster_yellow"
        And I follow "Unblock"
        And I press "Confirm"
        Then I should see "westminster_yellow"
        And I should see "Successfully unblocked user"
        And I should see "Block"
        And I am on "/search"
        Then I should see "westminster_yellow"
