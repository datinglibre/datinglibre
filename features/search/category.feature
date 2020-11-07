Feature:
    As a user I want to be able to find another user based on at least one match in
    several distinct categories

    @search
    Scenario: I can find another user when our first and second categories match
        Given the following profiles exist:
            | email                          | characteristics | requirements   | city   | age |
            | chelsea_blue@example.com       | Square, Blue    | Yellow, Circle | London | 30  |
            | westminster_yellow@example.com | Yellow, Circle  | Blue, Square   | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "westminster_yellow@example.com" matches

    @search
    Scenario: I do not match another user which does not match both my categories
        Given the following profiles exist:
            | email                     | characteristics | requirements   | city   | age |
            | chelsea_blue@example.com  | Blue, Square    | Yellow, Circle | London | 30  |
            | clapham_green@example.com | Green, Circle   | Blue, Square   | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "clapham_green@example.com" does not match

    @search
    Scenario: I do not match another user where they match both my categories, but
    I don't match both of theirs
        Given the following profiles exist:
            | email                      | characteristics | requirements   | city   | age |
            | chelsea_blue@example.com   | Blue, Square    | Yellow, Circle | London | 30  |
            | hackney_yellow@example.com | Yellow, Circle  | Blue, Circle   | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "hackney_yellow@example.com" does not match

    @search
    Scenario: I match another user when they share two characteristics in separate categories
    in three of my requirements
        Given the following profiles exist:
            | email                      | characteristics          | requirements             | city   | age |
            | chelsea_blue@example.com   | Blue, Square             | Yellow, Circle, Triangle | London | 30  |
            | hackney_yellow@example.com | Yellow, Circle, Triangle | Blue, Circle, Square     | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "hackney_yellow@example.com" matches

    @search
    Scenario: I do not match another user when they share two characteristics, but only in
    one category, and none in the other
        Given the following profiles exist:
            | email                      | characteristics          | requirements            | city   | age |
            | chelsea_blue@example.com   | Blue, Square             | Green, Circle, Triangle | London | 30  |
            | hackney_yellow@example.com | Yellow, Circle, Triangle | Blue, Square            | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the user "hackney_yellow@example.com" does not match

    @search
    Scenario: I match two users when our requirements and characteristics intersect
        Given the following profiles exist:
            | email                      | characteristics          | requirements          | city   | age |
            | chelsea_blue@example.com   | Blue, Square             | Yellow, Circle, Green | London | 30  |
            | hackney_yellow@example.com | Yellow, Circle, Triangle | Blue, Circle, Square  | London | 30  |
            | clapham_green@example.com  | Green, Circle            | Blue, Square          | London | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | chelsea_blue@example.com | 100000   | 18      | 100     |
        When the user "chelsea_blue@example.com" searches for matches
        Then the following users match:
            | email                      |
            | hackney_yellow@example.com |
            | clapham_green@example.com  |