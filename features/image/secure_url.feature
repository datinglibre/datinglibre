Feature:
    Presigned image URLs can be refreshed

    @image
    Scenario:
        Given I upload "cat.jpg" as the profile image for "image@example.com"
        And the profile image for "image@example.com" has expired
        When the secure image refresh task has run
        Then generate a new expiry date for the profile image of "image@example.com"