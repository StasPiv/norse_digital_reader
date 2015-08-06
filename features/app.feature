Feature: App
    @javascript
    Scenario: Authorization failed
        Given I am on "/"
        Then I should see "Authorization"
        Then I fill in "email" with "fake"
        And I fill in "password" with "123"
        And I press "enter"
        Then I should see "Authorization"

    Scenario: Registration success
        Given I am on "/"
        Then I should see "Authorization"
        Then I press "show_register"
        Then I should see "Repeat Password"
        Then I fill in "register_email" with "testtesttest@gmail.com"
        Then I fill in "register_password" with "123456"
        Then I fill in "repeat_password" with "123456"
        And I press "register"
        And I should see "My Sources"