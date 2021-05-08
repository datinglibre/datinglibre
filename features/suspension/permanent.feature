Feature:
    As a moderator
    I can queue a user for permanent suspension

    @suspension
    Scenario: A moderator can permanently suspend a user
        Given the following profiles exist:
            | email                 | city   | age |
            | reporter@example.com  | London | 30  |
            | suspended@example.com | London | 30  |
        And the user "reporter@example.com" has reported "suspended@example.com"
        And a moderator exists with email "moderator@example.com"
        And I am logged in with "moderator@example.com"
        And I am on "/moderator/reports"
        And I follow "suspended"
        And I follow "Permanently suspend"
        And I should see "Are you sure you want to suspend and enter this profile into the permanent suspension queue?"
        And I check "Abusive messages"
        And I press "Confirm"
        Then I should see "Profile has been entered into queue for permanent suspension"
        And I should not see "Are you sure you want to suspend and enter this profile into the permanent suspension queue?"
        And I follow "Suspensions"
        Then I should see "Permanent"
        Then I should see "Open"
        And I should see "Abusive messages"

    @suspension
    Scenario: An administrator permanently suspend a profile
        Given the following profiles exist:
            | email                 | city   | age |
            | reporter@example.com  | London | 30  |
            | suspended@example.com | London | 30  |
        And the user "reporter@example.com" has reported "suspended@example.com"
        And an administrator exists with email "admin@example.com"
        And I am logged in with "admin@example.com"
        And I follow "Moderate"
        And I follow "Reports"
        Then I follow "suspended"
        And I follow "Permanently suspend"
        And I check "Abusive messages"
        And I press "Confirm"
        Then I should see "Profile has been permanently suspended"
        And the user "suspended@example.com" should receive a permanent suspension email with "Abusive messages"
        And I follow "Suspensions"
        Then I should see "Open"

    @suspension
    Scenario: An administrator can confirm a permanent suspension
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has entered "newuser@example.com" into the permanent suspension queue
        And an administrator exists with email "admin@example.com"
        And I am logged in with "admin@example.com"
        And I follow "Moderate"
        And I follow "Permanent suspensions"
        Then I should see "newuser"
        And I should see "Spam"
        And I should not see "Profile has been permanently suspended"
        And I follow "newuser"
        And I follow "Permanently suspend"
        And I check "Abusive messages"
        And I press "Confirm"
        Then I should see "Profile has been permanently suspended"
        And I follow "Suspensions"
        Then I should see "Spam"
        Then I should see "Abusive messages"
        And I follow "Permanently suspend"
        Then I should see "Profile has been permanently suspended"

   @suspension
   Scenario: An administrator can close a permanent suspension
       Given the following profiles exist:
           | email               | city   | age |
           | newuser@example.com | London | 30  |
       And a moderator exists with email "moderator@example.com"
       And the moderator "moderator@example.com" has entered "newuser@example.com" into the permanent suspension queue
       And an administrator exists with email "admin@example.com"
       And I am logged in with "admin@example.com"
       And I follow "Moderate"
       And I follow "Permanent suspensions"
       And I follow "newuser"
       And I follow "Suspensions"
       And I follow "Close"
       And I press "Close"
       Then I should see "Closed"
       And I follow "Moderate"
       And I follow "Permanent suspensions"
       Then I should not see "newuser"
