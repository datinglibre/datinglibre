Feature:
    As a user
    I can buy a subscription through CcBill

    @subscription
    Scenario: A CcBill NewSaleSuccessEvent is persisted as an event
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        Then a new "datinglibre.ccbill.newsale" event should be created for "newuser@example.com"
        And a new "ccbill" subscription is created for "newuser@example.com" with provider subscription ID "985938"

    @subscription
    Scenario: A CcBill NewSaleSuccessEvent without a user ID is raised as an error
        Given a new sale success event without a user ID
        Then a new "datinglibre.subscription.error" event with a data payload should be created

    @subscription
    Scenario: A CcBill NewSaleFailureEvent is persisted as an event
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has failed to buy a new CcBill subscription
        Then a new "datinglibre.ccbill.newsalefailure" event should be created for "newuser@example.com"

    @subscription
    Scenario: An error event raised as part of processing CcBill events is persisted as an event
        Given an error event has been raised processing CcBill events
        Then a new "datinglibre.subscription.error" event with a data payload should be created

    @subscription
    Scenario: I can view my subscription on the account page
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        Then I should see "985938"
        And I should see "Active"
        And I should see "August 20, 2012"

    @subscription
    Scenario: A CcBill RenewalSuccessEvent renews a subscription
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a rebill for subscription "985938" with next renewal date "2020-09-20"
        Then a new "datinglibre.ccbill.renewal" event should be created for "newuser@example.com"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        And I should see "Active"
        And I should see "September 20, 2020"

    @subscription
    Scenario: A CcBill RenewalFailureEvent moves subscription into renewal failed state
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a failed rebill for subscription "985938" with next retry date "2020-08-23"
        Then a new "datinglibre.ccbill.renewal.failure" event should be created for "newuser@example.com"
        And I am on "/account/subscription"
        And I am logged in with "newuser@example.com"
        And I should see "Renewal failure"
        And I should see "August 23, 2020"

    @subscription
    Scenario: A CcBill CancellationEvent cancels a subscription
        Given the following profiles exist:
           | email               | city   | age |
           | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a cancellation for "985938"
        Then a new "datinglibre.ccbill.cancellation" event should be created for "newuser@example.com"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        Then I should see "Cancelled"
        And I should see "Ended"

    @subscription
    Scenario: A CcBill ChargebackEvent marks the subscription as a chargeback
        Given the following profiles exist:
          | email               | city   | age |
          | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a chargeback for "985938"
        Then a new "datinglibre.ccbill.chargeback" event should be created for "newuser@example.com"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        Then I should see "Chargeback"
        And I should see "Ended"

    @subscription
    Scenario: a CcBill RefundEvent marks the subscription as refunded
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a refund for "985938"
        Then a new "datinglibre.ccbill.refund" event should be created for "newuser@example.com"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        Then I should see "Refund"
        And I should see "Ended"

    @subscription
    Scenario: a CcBill BillingDateChange event changes the next renewal date
        Given the following profiles exist:
            | email               | city   | age |
            | newuser@example.com | London | 30  |
        When the user "newuser@example.com" has bought a new CcBill subscription that has ID "985938"
        And there has been a billing date change for "985938" to "2021-02-01"
        Then a new "datinglibre.ccbill.billing.date.change" event should be created for "newuser@example.com"
        And I am logged in with "newuser@example.com"
        And I am on "/account/subscription"
        Then I should see "Active"
        And I should see "February 1, 2021"