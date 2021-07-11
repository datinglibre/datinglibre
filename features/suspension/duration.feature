Feature:
    As a moderator or admin
    I can suspend a user

    @suspension
    Scenario: A moderator can suspend a user
        Given the following profiles exist:
            | email                 | city   | age |
            | reporter@example.com  | London | 30  |
            | suspended@example.com | London | 30  |
        And the user "reporter@example.com" has reported "suspended@example.com"
        And a moderator exists with email "moderator@example.com"
        And I am logged in with "moderator@example.com"
        And I am on "/moderator/reports"
        And I follow "suspended"
        And I follow "profile-menu-suspensions"
        Then I should see "Abusive messages"
        Then I should see "No suspensions"
        And I check "Spam"
        And I press "Suspend"
        Then the user "suspended@example.com" should receive a suspension email for "Spam" for "24" hours
        Then I should see "User suspended"
        And I should not see "Abusive messages"
        And I should see "Spam"

    @suspension
    Scenario: A user loses access to the site when they are suspended
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        Then the user "newuser@example.com" should receive a suspension email for "Spam" for "72" hours
        And I log in using email "newuser@example.com"
        And I am on "/profile"
        Then I should see "your profile has been suspended for the reasons below. It will be reviewed after 72 hours."
        And I am on "/search"
        Then I should see "your profile has been suspended for the reasons below. It will be reviewed after 72 hours."
        And I am on "/matches"
        Then I should see "your profile has been suspended for the reasons below. It will be reviewed after 72 hours."

    @suspension
    Scenario: When a moderator creates another suspension, the previous suspension will be closed
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        Then only one open suspension should exist for "newuser@example.com"

    @suspension
    Scenario: A moderator can view expired suspensions
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        When "73" hours has elapsed for the suspension under "newuser@example.com"
        And I log in using email "moderator@example.com"
        And I am on "/moderator/suspensions"
        Then I should see "newuser"
        And I follow "newuser"
        Then I should see "newuser"
        And I should see "London"

    @suspension
    Scenario: A moderator does not view suspensions that have not expired
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        And I log in using email "moderator@example.com"
        And I am on "/moderator/suspensions"
        Then I should not see "newuser"

    @suspension
    Scenario: A moderator can close a suspension
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "newuser@example.com" for "spam" for "72" hours
        And the user "newuser@example.com" should receive a suspension email for "Spam" for "72" hours
        When "73" hours has elapsed for the suspension under "newuser@example.com"
        And I log in using email "moderator@example.com"
        And I am on "/moderator/suspensions"
        And I follow "newuser"
        And I follow "Close"
        Then I should see "Are you sure you want to close this suspension?"
        And I press "Close"
        Then I should see "Closed suspension"
        And I follow "Moderate"
        And I follow "Suspensions"
        Then I should not see "newuser"
