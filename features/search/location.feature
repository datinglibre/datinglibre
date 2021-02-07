Feature:
    As a user
    I want to be able to find other users near me

    @search @ui
    Scenario:
        Given the following profiles exist:
            | email                    | attributes       | requirements   | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term | Bristol | 30  |
        When I log in using email "bristol_blue@example.com"
        And I navigate to the search page
        Then I should see that no profiles match

    @search
    Scenario: I can find another user when they are near me
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
    Scenario: I can't see another user's un-moderated profile image in a location search
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                   | distance | min_age | max_age |
            | bath_yellow@example.com | 100000   | 18      | 100     |
        When the user "bristol_blue@example.com" has uploaded a profile image
        And the user "bath_yellow@example.com" searches for matches
        Then the image of "bristol_blue@example.com" should not appear


    @search
    Scenario: I can't see another user's rejected profile image in a location search
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                   | distance | min_age | max_age |
            | bath_yellow@example.com | 100000   | 18      | 100     |
        When the user "bristol_blue@example.com" has uploaded a profile image
        And the profile image for "bristol_blue@example.com" has failed moderation
        And the user "bath_yellow@example.com" searches for matches
        Then the image of "bristol_blue@example.com" should not appear

    @search
    Scenario: I can see a accepted profile images in a location search
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                   | distance | min_age | max_age |
            | bath_yellow@example.com | 100000   | 18      | 100     |
        When the user "bristol_blue@example.com" has uploaded a profile image
        And the profile image for "bristol_blue@example.com" has passed moderation
        And the user "bath_yellow@example.com" searches for matches
        Then the image of "bristol_blue@example.com" should appear

    @search
    Scenario: I should not find another user when they are not near me
        Given the following profiles exist:
            | email                     | attributes       | requirements     | city    | age |
            | bristol_blue@example.com  | woman, long_term | man, long_term   | Bristol | 30  |
            | oxford_yellow@example.com | man, long_term   | woman, long_term | Oxford  | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 18      | 100     |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "oxford_yellow@example.com" does not match

    @search
    Scenario: I can find users in a region when they are not near me
        Given the following profiles exist:
            | email                     | attributes       | requirements     | city    | age |
            | bristol_blue@example.com  | woman, long_term | man, long_term   | Bristol | 30  |
            | london_yellow@example.com | man, long_term   | woman, long_term | London  | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age | region  |
            | bristol_blue@example.com | 25000    | 18      | 100     | England |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "london_yellow@example.com" matches

    @search
    Scenario: I can find users in a region only
        Given the following profiles exist:
            | email                     | attributes       | requirements     | city    | age |
            | bristol_blue@example.com  | woman, long_term | man, long_term   | Bristol | 30  |
            | london_yellow@example.com | man, long_term   | woman, long_term | London  | 30  |
        And the following filters exist:
            | email                    | min_age | max_age | region  |
            | bristol_blue@example.com | 18      | 100     | England |
        When the user "bristol_blue@example.com" searches for matches
        Then the user "london_yellow@example.com" matches

    @search @ui
    Scenario: I can find users near me on the search page
        Given the following profiles exist:
            | email                    | attributes       | requirements     | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term   | Bristol | 30  |
            | bath_yellow@example.com  | man, long_term   | woman, long_term | Bath    | 30  |
        And the following filters exist:
            | email                    | distance | min_age | max_age |
            | bristol_blue@example.com | 100000   | 25      | 35      |
        When I log in using email "bristol_blue@example.com"
        And I navigate to the search page
        Then I should see the user "bath_yellow@example.com"


