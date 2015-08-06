Feature: Search
    In order to see a word definition
    As a website user
    I need to be able to search for a word

    Scenario: Searching for a page that does exist
        Given I am on "/"
        Then I should see "Read Symfony documentation to learn"