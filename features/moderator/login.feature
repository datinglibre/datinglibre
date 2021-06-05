Feature:
    A moderator is redirected to the moderate profile images page when they login

    @security
    Scenario: As a moderator I can see a list of new profile images
        Given a moderator exists with email "moderator@example.com"
        And I am logged in with "moderator@example.com"
        Then I should be on "/moderator/profile/images"

    @security
    Scenario: As a moderator my homepage should be the moderate profile images page
        Given a moderator exists with email "moderator@example.com"
        And I am logged in with "moderator@example.com"
        And I am on the homepage
        Then I should be on "/moderator/profile/images"

