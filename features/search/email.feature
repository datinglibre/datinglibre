Feature:
    As an administrator
    I want to be be able to search by email

    @search
    Scenario: I want to be able search for profiles by email
        Given the following profiles exist:
            | email                          | attributes       | requirements | city   | age |
            | chelsea_blue@example.com       | circle, green    | square, blue | London | 30  |
        And an administrator exists with email "admin@example.com"
        And I am logged in with "admin@example.com"
        And I am on "/admin/search/email"
        And I fill in "Chelsea_Blue@example.com" for "email_search_form_email"
        And I press "email_search_form_submit"
        Then I should see "chelsea_blue"
