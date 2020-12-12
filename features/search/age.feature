Feature:
    As a user
    I want to be able to filter my search matches by age

    @search
    Scenario: I can find another user in my age range
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 100     |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "bath_yellow@example.com" matches

    @search
    Scenario: I don't find users outside of my age range
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
            | bath_green@example.com   | man, long_term   | woman, long_term | Bath    | 28  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 28      |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "bath_yellow@example.com" does not match
        And the user "bath_green@example.com" matches

    @search
    Scenario: I can't find another user where they match my age range, but I don't match their range
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 32      |
            | bath_yellow@example.com  | 100000   | 18      | 28      |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "bath_yellow@example.com" does not match

    @search
    Scenario: I can't find another user where they don't match my age range, but I match their range
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 28      |
            | bath_yellow@example.com  | 100000   | 18      | 32      |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "bath_yellow@example.com" does not match

    @search
    Scenario: I can find a user for one exact year
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 30      | 30      |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "bath_yellow@example.com" matches
