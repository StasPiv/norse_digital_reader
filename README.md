This application is based on Symfony 2.7.

For using do following steps:

1. Clone this repository to your local machine
2. Run `composer update` from root directory
3. Run `php app/console server:run`

Database is included into repository, you don't need to change parameters for your local environment.
BackboneJS is used for implementing RestAPI on frontend

There are two kinds of tests.

1. phpunit for backend. You can use it if run `phpunit` from src/AppBundle directory
2. behat for test frontend scenarios. Run `bin/behat` from root directory.