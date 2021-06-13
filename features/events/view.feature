Feature:
    An administrator should be able to view events

    @event
    Scenario:
        Given an administrator exists with email "admin@example.com"
        And I am logged in with "admin@example.com"
        And an error event has been raised processing CcBill events
        When I am on "/admin/subscription/events/"
        Then I should see "datinglibre.subscription.error"
        And I should see "Year"
        And I should see "Month"
        And I should see "Day (optional)"