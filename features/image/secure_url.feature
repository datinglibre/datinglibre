Feature:
    Presigned image URLs can be refreshed

    @image
    Scenario:
        Given the following profiles exist:
            | email                          | attributes       | requirements | city   | age |
            | chelsea_blue@example.com       | circle, green    | square, blue | London | 30  |
        When I upload "cat.jpg" as the profile image for "chelsea_blue@example.com"
        And the profile image for "chelsea_blue@example.com" has expired
        When the secure image refresh task has run
        Then generate a new expiry date for the profile image of "chelsea_blue@example.com"