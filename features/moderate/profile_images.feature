Feature:
    I can moderate user profile images

    Scenario: As a moderator I can see a list of new profile images
        Given the following profiles exist:
            | email                    | attributes       | requirements   | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term | Bristol | 30  |
        And the user "bristol_blue@example.com" has uploaded a profile image
        Then I should see the new profile image for "bristol_blue@example.com"

    Scenario: As a moderator I should be able to accept a profile image
        Given the following profiles exist:
            | email                    | attributes       | requirements   | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term | Bristol | 30  |
        And a moderator exists with email "moderator@example.com"
        And the user "bristol_blue@example.com" has uploaded a profile image
        And I am logged in with "moderator@example.com"
        And I navigate to the moderate profile images page
        And I can see the new profile image for "bristol_blue@example.com"
        And I press "Accept"
        Then I should see text matching "Accepted"
        And I should see text matching "No images to moderate"

    Scenario: As a moderator I should be able to reject a profile image
        Given the following profiles exist:
            | email                    | attributes       | requirements   | city    | age |
            | bristol_blue@example.com | woman, long_term | man, long_term | Bristol | 30  |
        And a moderator exists with email "moderator@example.com"
        And the user "bristol_blue@example.com" has uploaded a profile image
        And I am logged in with "moderator@example.com"
        And I navigate to the moderate profile images page
        And I can see the new profile image for "bristol_blue@example.com"
        And I press "Reject"
        Then I should see text matching "Rejected"
        And I should see text matching "No images to moderate"

