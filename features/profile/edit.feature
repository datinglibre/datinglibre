Feature:
    As a user
    I want to complete a profile

    @profile
    Scenario: I can login to my account
        Given a user with email "user@example.com" and password "password" exists
        When I log in using email "user@example.com" and password "password"
        Then I should be on "/profile/edit"
        And I should see "Please complete your profile"

    @profile
    Scenario: I can view my profile as a new user
        Given a user with email "user@example.com"
        When I log in using email "user@example.com"
        Then I should be on "/profile/edit"
        And I should see "Please complete your profile"
        And I should see "United Kingdom"

    @profile
    Scenario: I have to enter a username
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in "" for "profile_form_username"
        And I press "Save"
        Then I should see "Please enter a username"

    @profile
    Scenario: My username has to be a valid length
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in "a" for "profile_form_username"
        And I press "Save"
        Then I should see "Your username must be between 3 and 32 characters"

    @profile
    Scenario: My username has to be a valid length
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa" for "profile_form_username"
        And I press "Save"
        Then I should see "Your username must be between 3 and 32 characters"

    @profile
    Scenario: My about section must not be too long
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in a profile about section that is too long
        And I press "Save"
        Then I should see "Your about section is too long"

    @profile
    Scenario: I cannot enter a duplicate username
        Given the following profiles exist:
            | email                    | attributes    | requirements | city   | age |
            | chelsea_blue@example.com | circle, green | square, blue | London | 30  |
        And a user with email "user@example.com"
        And I am logged in with "user@example.com"
        And I am on the profile edit page
        And I fill in "chelsea_blue" for "profile_form_username"
        And I press "Save"
        Then I should see "Username has been taken"

    @profile
    Scenario: I update my own username
        Given the following profiles exist:
            | email                    | attributes    | requirements | city   | age |
            | chelsea_blue@example.com | circle, green | square, blue | London | 30  |
        And I am logged in with "chelsea_blue@example.com"
        And I am on the profile edit page
        And I fill in "chelsea_blue" for "profile_form_username"
        And I press "Save"
        And I should be on "/profile"
        And I should see "chelsea_blue"
        Then I should not see "Username has been taken"

   @profile
   Scenario: I can only enter a username with letters and numbers
       Given the following profiles exist:
           | email                    | attributes    | requirements | city   | age |
           | chelsea_blue@example.com | circle, green | square, blue | London | 30  |
       And a user with email "user@example.com"
       And I am logged in with "user@example.com"
       And I am on the profile edit page
       And I fill in "chelsea?blue" for "profile_form_username"
       And I press "Save"
       Then I should see "Username may only contain unaccented letters and numbers, no spaces"

    @javascript
    Scenario: I can fill in my profile
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in "NewUser" for "profile_form_username"
        And I select the location:
            | country        | region  | city   |
            | United Kingdom | England | London |
        And I select "Green" from "profile_form_color"
        And I select "Circle" from "profile_form_shape"
        And I fill in "This is some text about me" for "profile_form_about"
        And I select "1990" from "profile_form_dob_year"
        And I select "1" from "profile_form_dob_month"
        And I select "1" from "profile_form_dob_day"
        And I check "North"
        And I close the toolbar
        And I press "Save"
        Then I should see "NewUser"
        And I should see "England"
        And I should see "London"
        And I should see "This is some text about me"
        And I should see "Green"
        And I should see "Circle"
        And I should see the age for "1990" "1" "1"

    @javascript
    Scenario: I can fill in my profile without an about section
        Given a user with email "user@example.com"
        And I am logged in with "user@example.com"
        When I am on the profile edit page
        And I fill in "NewUser" for "profile_form_username"
        And I select the location:
            | country        | region  | city   |
            | United Kingdom | England | London |
        And I select "Green" from "profile_form_color"
        And I select "Circle" from "profile_form_shape"
        And I select "1990" from "profile_form_dob_year"
        And I select "1" from "profile_form_dob_month"
        And I select "1" from "profile_form_dob_day"
        And I close the toolbar
        And I press "Save"
        Then I should see "NewUser"
        And I should see "England"
        And I should see "London"
        And I should see "Green"
        And I should see "Circle"
        And I should see the age for "1990" "1" "1"