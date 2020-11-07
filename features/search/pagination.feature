Feature:
    As a user
    I want to be be able to navigate through matching profiles

    @search
    Scenario: I can view paginated profiles
        Given the following profiles exist:
            | email                 | characteristics | requirements   | city    | age |
            | bristol_1@example.com | Square, Blue    | Yellow, Circle | Bristol | 30  |
            | bath_1@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_2@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_3@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_4@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_5@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
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
    Scenario: I can select the next and previous page
        Given the following profiles exist:
            | email                 | characteristics | requirements   | city    | age |
            | bristol_1@example.com | Square, Blue    | Yellow, Circle | Bristol | 30  |
            | bath_1@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_2@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_3@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_4@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_5@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_6@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_7@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_8@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_9@example.com    | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_10@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_11@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_12@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_13@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_14@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_15@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_16@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_17@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_18@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_19@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_20@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
            | bath_21@example.com   | Yellow, Circle  | Blue, Square   | Bath    | 30  |
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