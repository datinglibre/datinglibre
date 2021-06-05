Feature:
    As a user is notified when another user is suspended and they want to continue exchanging messages

    @suspension
    Scenario: A user who has exchanged messages with another user can see when they are suspended
        Given the following profiles exist:
            | email                 | city   | age |
            | user@example.com      | London | 30  |
            | suspended@example.com | London | 30  |
        And the user "user@example.com" sends the message "Hello" to "suspended@example.com"
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "suspended@example.com" for "spam" for "72" hours
        And I am logged in with "user@example.com"
        And I follow "Matches"
        When I follow "suspended"
        Then I should see "This user has been suspended for at least 72 hours. They will be unable to reply."

    @suspension
    Scenario: A user who has exchanged messages with a suspended user does not see this when suspension is closed
        Given the following profiles exist:
            | email                 | city   | age |
            | user@example.com      | London | 30  |
            | suspended@example.com | London | 30  |
        And the user "user@example.com" sends the message "Hello" to "suspended@example.com"
        And a moderator exists with email "moderator@example.com"
        And the moderator "moderator@example.com" has suspended "suspended@example.com" for "spam" for "72" hours
        And the moderator "moderator@example.com" has closed the suspension for "suspended@example.com"
        And I am logged in with "user@example.com"
        And I follow "Matches"
        When I follow "suspended"
        Then I should not see "This user has been suspended"