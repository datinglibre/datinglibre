Feature:
    Moderators can view reports

     @report
     Scenario: As a moderator I can view reports
         Given the following profiles exist:
             | email                          | attributes     | requirements   | city   | age |
             | chelsea_blue@example.com       | blue, square   | yellow, circle | London | 30  |
             | westminster_yellow@example.com | yellow, circle | blue, square   | London | 30  |
         And a moderator exists with email "moderator@example.com"
         And the user "chelsea_blue@example.com" has reported "westminster_yellow@example.com"
         And I am logged in with "moderator@example.com"
         And I am on "/moderator/reports"
         Then I should see "westminster_yellow"
         And I follow "westminster_yellow"
         And I follow "Reports"
         Then I should see "Spam"
         Then I should see "chelsea_blue"
         And I press "View"
         Then I should see "chelsea_blue"
         And I should see "Spam"
         And I should see "No messages between users"

    @report
    Scenario: As a moderator I can view empty reports page
       Given a moderator exists with email "moderator@example.com"
       And I am logged in with "moderator@example.com"
       And I am on "/moderator/reports"
       Then I should see "No reports"

    @report
    Scenario: As a moderator I can view a report with messages
        Given the following profiles exist:
            | email                          | attributes     | requirements   | city   | age |
            | chelsea_blue@example.com       | blue, square   | yellow, circle | London | 30  |
            | westminster_yellow@example.com | yellow, circle | blue, square   | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the user "westminster_yellow@example.com" sends the message "Lorem ipsum" to "chelsea_blue@example.com"
        And the user "chelsea_blue@example.com" sends the message "Dolor sit amet" to "westminster_yellow@example.com"
        And the user "chelsea_blue@example.com" has reported "westminster_yellow@example.com"
        And I am logged in with "moderator@example.com"
        And I am on "/moderator/reports"
        And I follow "View"
        And I should see "Lorem ipsum"
        And I should see "Dolor sit amet"

    @report
    Scenario: As a moderator I can view a report, when the reporter has deleted their account
        Given the following profiles exist:
            | email                          | attributes     | requirements   | city   | age |
            | chelsea_blue@example.com       | blue, square   | yellow, circle | London | 30  |
            | westminster_yellow@example.com | yellow, circle | blue, square   | London | 30  |
        And a moderator exists with email "moderator@example.com"
        And the user "chelsea_blue@example.com" has reported "westminster_yellow@example.com"
        And the user "chelsea_blue@example.com" has deleted their account
        And I am logged in with "moderator@example.com"
        And I am on "/moderator/reports"
        Then I should see "westminster_yellow"
        And I should not see "chelsea_blue"
        And I follow "View"
        Then I should see "westminster_yellow"
        And I should see "Spam"
        And I should see "No messages between users"

   @report
   Scenario: As a moderator I can close an open report
       Given the following profiles exist:
           | email                          | attributes     | requirements   | city   | age |
           | chelsea_blue@example.com       | blue, square   | yellow, circle | London | 30  |
           | westminster_yellow@example.com | yellow, circle | blue, square   | London | 30  |
       And a moderator exists with email "moderator@example.com"
       And the user "chelsea_blue@example.com" has reported "westminster_yellow@example.com"
       And I am logged in with "moderator@example.com"
       And I am on "/moderator/reports"
       And I follow "View"
       Then I should see "Spam"
       And I should see "Open"
       And I press "Close report"
       Then I should see "Closed report"
