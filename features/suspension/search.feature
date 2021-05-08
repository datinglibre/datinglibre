Feature:
    A user who is suspended does not appear in search

    @suspension
    Scenario: A user does not see a suspended profile in search
        Given the following profiles exist:
            | email                    | attributes     | requirements   | city    | age |
            | bristol_blue@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_yellow@example.com  | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 100     |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "bath_yellow@example.com" for "spam" for "72" hours
        And I am logged in with "bristol_blue@example.com"
        And I follow "Search"
        Then I should not see "bath_yellow"

    @suspension
    Scenario: A user sees a profile after their suspension has been closed
        Given the following profiles exist:
            | email                    | attributes     | requirements   | city    | age |
            | bristol_blue@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_yellow@example.com  | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 100     |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "bath_yellow@example.com" for "spam" for "72" hours
        And the moderator "moderator@example.com" has closed the suspension for "bath_yellow@example.com"
        And I am logged in with "bristol_blue@example.com"
        And I follow "Search"
        Then I should see "bath_yellow"