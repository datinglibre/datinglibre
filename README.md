
# DatingLibre

[![Build Status](https://travis-ci.com/datinglibre/DatingLibre.svg?branch=master "Travis CI status")](https://travis-ci.com/github/datinglibre/datinglibre)

DatingLibre ([demo](https://github.com/datinglibre/DatingLibreDemo)) is a white-label open source Symfony 5.2 `PHP` 7.4 dating site backed by PostgreSQL,
Amazon `SES` and `S3` (or a compatible service). It can be installed and updated using Ansible.

DatingLibre can introduce people based on geographical location, requirements and attributes. The requirements and attributes can be setup
not only for relationships, but also for hobbies, such as finding a tandem language partner.

DatingLibre is made up of the following repositories:
- the reference implementation (this repository) which uses the dummy categories `color` and `shape`, and contains the database schema, and Behat features.
- the [datinglibre-app-bundle](https://github.com/datinglibre/datinglibre-app-bundle) which contains overridable Behat test contexts, controllers, repositories, services, entities.
- the [DatingLibreDemo](https://github.com/datinglibre/DatingLibreDemo) site, which shows how the `datinglibre-app-bundle` can be 
overridden to build a custom website: overriding functionality as required, whilst leaving the rest of the bundle upgradable via `composer upgrade`. The demo code is deployed to [datinglibre.com](http://datinglibre.com).
  
All PRs should be opened against this reference repository and/or the [datinglibre-app-bundle](https://github.com/datinglibre/datinglibre-app-bundle).

## Features

- Register account, with a private reminder if an email address already exists. 
- Confirm account through email.
- Reset password.
- Create a profile.
- Upload a profile image.
- Search by radius and/or region, through a provided [dataset](https://github.com/datinglibre/datinglibre#credits) of the latitudes and longitudes of the world's significant towns and cities.
- Browse through profiles, using keyset pagination.
- Block users.
- Moderate profile images.
- Delete account.

![Image showing profile edit page](https://raw.githubusercontent.com/datinglibre/datinglibre.github.io/main/profile.png "Profile edit page")

## Development

Ubuntu 20.04 is supported. Minimum requirements: 

  - `PHP` 7.4 (`sudo apt install php7.4 php7.4-json php7.4-curl php7.4-simplexml php7.4-pgsql php7.4-intl php7.4-mbstring`)
  - Composer (`sudo apt install composer`)
  - Docker and docker compose (`sudo apt install docker docker-compose` `sudo usermod -aG docker your_username` `sudo systemctl enable docker`, log out then in again to refresh groups).
  - The [Symfony command line tool](https://symfony.com/download).
  - Ansible (`sudo apt install ansible`)
  - The Ansible general collection (`ansible-galaxy collection install community.general:==1.3.1`)

Additional requirements to run Javascript tests:

 - Chromium (`snap install chromium`)
 - [Chromedriver](https://chromedriver.chromium.org/) (`sudo mv chromedriver /usr/local/bin`)
 - Java 11 (`sudo apt install openjdk-11-jre`) 
 - [Selenium Standalone](https://www.selenium.dev/downloads/)
  
### Code Style

`php-cs-fixer` is configured with PSR1 and PSR2 standards.

    ./vendor/bin/php-cs-fixer fix
                
### Testing
    
#### 1. Install dependencies:

    composer install    
    
#### 2. Start the internal webserver:

    symfony serve       
    
#### 3. Run the setup script

This will start `mailhog`, `S3Ninja` and `postgres` docker containers, run the database migrations
and install test fixtures:
    
    ./setup.sh
    
`docker` will expose the following services to `localhost`:

| Service    | Ports                                        |
| -----------|----------------------------------------------|
| [MailHog](https://github.com/mailhog/MailHog) | 1025/SMTP |
| MailHog UI                                    | [8025/HTTP](http://localhost:8025) |
| Postgres                                      | 5432/TCP  |
| [S3 ninja](https://s3ninja.net/)              | [9444/HTTP](http://localhost:9444/ui) | 

#### 4. Start Selenium (optional, for Javascript tests):     
    
    java -jar  selenium-server-standalone.jar
    
#### 5. Increase allowed number of open files

You might need to increase the number of open files that are allowed on your system, if running the tests
fails with "too many open files". You can do this temporarily with:

    ulimit -n 65535
        
#### 6. Run tests

##### Run `behat` BDD scenarios without Javascript tests:

    ./vendor/bin/behat --tags ~javascript

##### Run all `behat` BDD scenarios:

    ./vendor/bin/behat
    
The default password for all test accounts is `password`.

### Download and edit the `datinglibre-app-bundle`

This project is used as a skeleton to run the [datinglibre-app-bundle](https://github.com/datinglibre/datinglibre-app-bundle). The
project is separated in this way, so that [parts of the bundle can be overridden](https://symfony.com/doc/current/bundles/override.html),
whilst keeping the ability to keep updated with new DatingLibre features using `composer upgrade`. This has been done in the 
[DatingLibreDemo](https://github.com/datinglibre/DatingLibreDemo) project.

In order to contribute to the DatingLibre project, you will need to setup your own version of the [datinglibre-app-bundle](https://github.com/datinglibre/datinglibre-app-bundle)
as a local repository, add the following to your DatingLibre `composer.json` file, substituting `/home/datinglibre/git/datinglibre-app-bundle` with 
the path to the bundle in your filesystem:

        "repositories": [
            {
                "type": "path",
                "url": "/home/datinglibre/git/datinglibre-app-bundle",
                "options": {
                    "symlink": true
                }
            }
        ]

Update your `composer.json` to use 

           "datinglibre/datinglibre-app-bundle": "@dev"

Then run `composer update`. Make source code changes in `datinglibre-app-bundle` and add Behat tests in the main DatingLibre 
project, commit, and open PRs against the respective projects.

## Architectural Design Record

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/)

## Deployment

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/).

## Customization
    
See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/).

## User management

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/).

## Credits

The `countries.sql` `regions.sql` and `cities.sql` files were created by processing geographical data from [GeoNames](https://www.geonames.org/)
and are licensed under [Attribution 4.0 International (CC BY 4.0)](https://creativecommons.org/licenses/by/4.0/).

The `src/Security/LoginFormAuthenticator.php` is based on Symfony documentation [How to build a login form](https://symfony.com/doc/current/security/form_login_setup.html)
and is licensed under [Creative Commons BY-SA 3.0](https://creativecommons.org/licenses/by-sa/3.0/).

## Licence

Copyright 2020-2021 DatingLibre.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
