Feature:
    I can upload images up to S3

    @image
    Scenario:
        When I upload "cat.jpg" as the profile image for "image@example.com"
        Then the image should be set as the profile image for "image@example.com"