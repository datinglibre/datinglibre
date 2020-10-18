Feature:
  I can upload images up to S3

  @image
  Scenario:
    When I upload "cat.jpg"
    Then the image should be stored