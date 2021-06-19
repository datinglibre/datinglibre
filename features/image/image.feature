Feature:
    I can upload images up to S3

    @image
    Scenario:
        Given the following profiles exist:
            | email                          | attributes       | requirements | city   | age |
            | chelsea_blue@example.com       | circle, green    | square, blue | London | 30  |
        When I upload "cat.jpg" as the profile image for "chelsea_blue@example.com"
        Then the image should be set as the profile image for "chelsea_blue@example.com"