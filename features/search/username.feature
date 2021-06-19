Feature:
    As an administrator or moderator
    I want to be be able to search by username

    @search
    Scenario: As an administrator I want to be able search for profiles by username
        Given the following profiles exist:
            | email                          | attributes       | requirements | city   | age |
            | chelsea_blue@example.com       | circle, green    | square, blue | London | 30  |
        And an administrator exists with email "admin@example.com"
        And I am logged in with "admin@example.com"
        And I am on "/moderator/search/username"
        And I fill in "Chelsea_blue" for "username_search_form_username"
        And I press "username_search_form_submit"
        Then I should see "chelsea_blue"

    @search
    Scenario: As a moderator I want to be able search for profiles by usernmae
        Given the following profiles exist:
            | email                          | attributes       | requirements | city   | age |
            | chelsea_blue@example.com       | circle, green    | square, blue | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And I am logged in with "moderator@example.com"
        And I am on "/moderator/search/username"
        And I fill in "chelsea_Blue" for "username_search_form_username"
        And I press "username_search_form_submit"
        Then I should see "chelsea_blue"
