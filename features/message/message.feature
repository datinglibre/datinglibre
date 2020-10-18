Feature:
  As a user
  I want to be able to message another user

  @message
  Scenario: I can send a message to another user
    Given the following profiles exist:
      | email                          | characteristics  | requirements   | city    | age |
      | bristol_1@example.com          | Square, Blue     | Yellow, Circle | Bristol | 30  |
      | bath_1@example.com             | Yellow, Circle   | Blue, Square   | Bath    | 30  |
    When the user "bristol_1@example.com" sends the message "Hello" to "bath_1@example.com"
    Then  "bath_1@example.com" should have a new message with "Hello" from "bristol_1@example.com"
  
  @message
  Scenario: I should lose access to messages when that user blocks me
    Given the following profiles exist:
     | email                          | characteristics  | requirements   | city    | age |
     | bristol_1@example.com          | Square, Blue     | Yellow, Circle | Bristol | 30  |
     | bath_1@example.com             | Yellow, Circle   | Blue, Square   | Bath    | 30  |
    And the following blocks exist
      | email                    | block                 |
      | bristol_1@example.com    | bath_1@example.com |
    When the user "bath_1@example.com" sends the message "Hello" to "bristol_1@example.com"
    Then  "bristol_1@example.com" should have no messages

  @message @ui
  Scenario: I can see my matches
    Given the following profiles exist:
      | email                          | characteristics  | requirements   | city    | age |
      | bristol_1@example.com          | Square, Blue     | Yellow, Circle | Bristol | 30  |
      | bath_1@example.com             | Yellow, Circle   | Blue, Square   | Bath    | 30  |
    When the user "bristol_1@example.com" sends the message "Hello" to "bath_1@example.com"
    And I am logged in with "bristol_1@example.com"
    And I navigate to the matches page
    Then I should see "Hello"

  @message @ui
  Scenario: The recipient can see their matches
    Given the following profiles exist:
      | email                          | characteristics  | requirements   | city    | age |
      | bristol_1@example.com          | Square, Blue     | Yellow, Circle | Bristol | 30  |
      | bath_1@example.com             | Yellow, Circle   | Blue, Square   | Bath    | 30  |
    When the user "bristol_1@example.com" sends the message "Hello" to "bath_1@example.com"
    And I am logged in with "bath_1@example.com"
    And I navigate to the matches page
    Then I should see "Hello"


  @message @ui
  Scenario: I can send another user a message
    Given the following profiles exist:
      | email                          | characteristics  | requirements   | city    | age |
      | bristol_1@example.com          | Square, Blue     | Yellow, Circle | Bristol | 30  |
      | bath_1@example.com             | Yellow, Circle   | Blue, Square   | Bath    | 30  |
    And I am logged in with "bristol_1@example.com"
    And I navigate to message user "bath_1@example.com"
    And I send the message "Hello this is a new message"
    Then I should see "Sent message"
    And I should see "Hello this is a new message"
    And I should see "bristol_1"