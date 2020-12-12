Feature:
    As a user I want to be able to find another user based on at least one match in
    several distinct categories

    @search
    Scenario: I can find another user when our first and second categories match
        Given the following profiles exist:
            | email                          | attributes       | requirements     | city   | age |
            | chelsea_blue@example.com       | woman, long_term | man, long_term   | London | 30  |
            | westminster_yellow@example.com | man, long_term   | woman, long_term | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "westminster_yellow@example.com" matches

    @search
    Scenario: I do not match another user which does not match both my categories
        Given the following profiles exist:
            | email                     | attributes       | requirements      | city   | age |
            | chelsea_blue@example.com  | woman, long_term | man, long_term    | London | 30  |
            | clapham_green@example.com | man, short_term  | woman, short_term | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "clapham_green@example.com" does not match

    @search
    Scenario: I do not match another user where they match both my categories, but
    I don't match both of theirs
        Given the following profiles exist:
            | email                      | attributes       | requirements   | city   | age |
            | chelsea_blue@example.com   | woman, long_term | man, long_term | London | 30  |
            | hackney_yellow@example.com | man, long_term   | man, long_term | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "hackney_yellow@example.com" does not match