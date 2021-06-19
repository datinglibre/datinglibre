Feature:
    As a user
    I want to be be able to navigate through matching profiles

    @search
    Scenario: I can view paginated profiles
        Given the following profiles exist:
            | email                 | attributes     | requirements   | city    | age |
            | bristol_1@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_1@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_2@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_3@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_4@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_5@example.com    | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                 | distance | min_age | max_age |
            | bristol_1@example.com | 100000   | 25      | 35      |
        And I am logged in with "bristol_1@example.com"
        Then I should see "bath_1"
        And I should see "bath_2"
        And I should see "bath_3"
        And I should see "bath_4"
        And I should see "bath_5"

    @search
    Scenario: I should not be able to view an unmoderated profile image in profiles
        Given the following profiles exist:
            | email                 | attributes     | requirements   | city    | age |
            | bristol_1@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_1@example.com    | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                 | distance | min_age | max_age |
            | bristol_1@example.com | 100000   | 25      | 35      |
        And the user "bath_1@example.com" has uploaded a profile image
        And I am logged in with "bristol_1@example.com"
        Then the response should contain "profile.jpg"
        And the response should not contain "Signature"

    @search
    Scenario: I should be able to view a successfully moderated profile image in profiles
        Given the following profiles exist:
            | email                 | attributes     | requirements   | city    | age |
            | bristol_1@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_1@example.com    | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                 | distance | min_age | max_age |
            | bristol_1@example.com | 100000   | 25      | 35      |
        And the user "bath_1@example.com" has uploaded a profile image
        And the profile image for "bath_1@example.com" has passed moderation
        And I am logged in with "bristol_1@example.com"
        Then the response should contain "Signature"

    @search
    Scenario: I should not be able to view a failed moderated profile image in profiles
        Given the following profiles exist:
            | email                 | attributes     | requirements   | city    | age |
            | bristol_1@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_1@example.com    | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                 | distance | min_age | max_age |
            | bristol_1@example.com | 100000   | 25      | 35      |
        And the user "bath_1@example.com" has uploaded a profile image
        And the profile image for "bath_1@example.com" has failed moderation
        And I am logged in with "bristol_1@example.com"
        Then the response should not contain "Signature"
        And the response should contain "profile.jpg"

    @search
    Scenario: I can select the next and previous page
        Given the following profiles exist:
            | email                 | attributes     | requirements   | city    | age |
            | bristol_1@example.com | blue, square   | yellow, circle | Bristol | 30  |
            | bath_1@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_2@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_3@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_4@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_5@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_6@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_7@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_8@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_9@example.com    | yellow, circle | blue, square   | Bath    | 30  |
            | bath_10@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_11@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_12@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_13@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_14@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_15@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_16@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_17@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_18@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_19@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_20@example.com   | yellow, circle | blue, square   | Bath    | 30  |
            | bath_21@example.com   | yellow, circle | blue, square   | Bath    | 30  |
        And the following filters exist:
            | email                 | distance | min_age | max_age |
            | bristol_1@example.com | 100000   | 25      | 35      |
        And I am logged in with "bristol_1@example.com"
        And I follow "Next"
        Then I should see "bath_11"
        And I should not see "bath_10"
        And I follow "Next"
        Then I should see "bath_21"
        And I should not see "bath_11"
        And I follow "Previous"
        Then I should see "bath_11"
        And I should not see "bath_21"
        And I follow "Previous"
        Then I should see "bath_1"