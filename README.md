
# DatingLibre

[![Build Status](https://travis-ci.com/datinglibre/datinglibre.svg?branch=master "Travis CI status")](https://travis-ci.com/github/datinglibre/datinglibre)

DatingLibre ([demo](https://datinglibre.com)) is a white-label open source Symfony 5.2 `PHP` 7.4 dating site backed by PostgreSQL, which can introduce
people based on geographical location, requirements and attributes. The requirements and attributes can be setup
not only for relationships, but also for hobbies, such as finding a tandem language partner.

DatingLibre uses Amazon SES and S3 (or a compatible service) and can be installed and updated automatically using Ansible. The demo runs on two $5 virtual servers.

The project is currently in alpha.

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

Ubuntu 20.04 is recommended. Minimum requirements: 

  - `PHP` 7.4 (`sudo apt install php7.4 php7.4-json php7.4-curl php7.4-simplexml php7.4-pgsql php7.4-intl`)
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

#### Run `phpunit` tests:

    ./bin/phpunit

## Deployment

### Staging 

A staging environment can be created using [Vagrant](https://www.vagrantup.com/). First, install the following:

    sudo apt install vagrant virtualbox ansible
    vagrant plugin install vagrant-hostmanager

Then follow these steps:

#### 1. Update paths to the Vagrant private keys

The path to private keys has to be hardcoded, so copy `hosts.dist`:

    cp deploy/inventories/staging/hosts.dist deploy/inventories/staging/hosts
    
Then edit the path to Vagrant's private key:
    
    ansible_ssh_private_key_file='/home/your_username/path/to/datinglibre/.vagrant/machines/datinglibre/virtualbox/private_key'
    
#### 2. Install the location files

The following files are distributed as part of the [separate DatingLibre locations repository](https://github.com/datinglibre/datinglibrelocations).
 - `countries.sql`
 - `regions.sql`
 - `cities.sql`

Copy them to`deploy/roles/datinglibre/locations`. 

#### 3. Start the servers 
   
    vagrant up datinglibretesting
    vagrant up datinglibredb
    vagrant up datinglibre

The virtual machines are setup as follows:

| Hostname                 | IP            | Ports                                 |
| -------------------------|---------------|---------------------------------------|
| datinglibre.local        | 192.168.0.99  | 80/HTTP [443/HTTPS](https://datinglibre.local) |
| datinglibredb.local      | 192.168.0.100 | 5432/POSTGRES SSL 6543/PGBOUNCER SSL  |
| datinglibretesting.local | 192.168.0.101 | [8025/HTTP](http://datinglibretesting.local:8025) 1025/SMTP [9444/HTTP](http://datinglibretesting.local:9444/ui) | 

If you need to provision any host again, use `vagrant provision`, e.g.:

    vagrant provision datinglibre
    
    
The staging environment is supposed to work as closely as possible to production, so it also 
encrypts variables in the configuration using `ansible-vault`.

Usually the Vault's password file should not be committed, however as these are staging values, 
the password is available as `staging_vault_password`, and is used in the `Vagrantfile`.

The staging configuration contains self-signed certificates, so you will need to make a security exception for these
when your browser displays an error page.  
    
### Production

You will first need `SSH` access to two servers, or virtual servers, running Ubuntu 20.04 with at least 1GB RAM, which 
works for testing purposes. You will also need an `AWS` account with `IAM` users setup for `S3` for file storage
and `SES` for sending emails.

#### 1. Create configuration for your production inventory 

Create `production` inventory using `staging` inventory as a template:

    cp -R ./deploy/inventories/staging ./deploy/inventories/production    

Create a long password in a file in your home directory called `vault_password`. *This password file should not be committed 
to GIT*. 

You can remove the `deploy/inventories/production/` exclusion in `.gitignore` if you want to commit
your inventory to a private GIT repository. 

#### 2. Generate certificates

Generate self-signed certificates for the database:

    openssl req -x509 -newkey rsa:4096 -sha256 -nodes -keyout database.key -out database.crt -days 3650
    ansible-vault encrypt_string --vault-password-file=~/vault_password < database.key

Copy and paste the output of the `ansible-vault` command into the `database_key` section of `all.yml`.

Copy and paste the contents of `database.crt` into the `database_certificate` and `database_root_certificate` sections of `all.yml`.
Make sure there is a new line after `|` and `!vault |`. You can delete `database.crt` and `database.key` afterwards.

The self-signed certificate will give you the following: "I want my data to be encrypted, and I accept the overhead. I trust that the network will make sure I always connect to the server I want",
as explained by the [PostgreSQL documentation](https://www.postgresql.org/docs/13/libpq-ssl.html). To this point, Dating Libre disallows provisioning
and connecting to a database using a public IP. 

Generate a certificate for your domain:

    openssl req -new -newkey rsa:2048 -nodes -keyout datinglibre.key -out datinglibre.csr
        
Enter your domain as the `Common Name`, e.g. `datinglibre.com`. This will create a private key, and a certificate signing
request, which you should send to a web hosting or domain name company.

Concatenate the certificate with the chain of trust for `nginx`. 

    cat datinglibre_com.crt datinglibre_com.ca-bundle > datinglibre.crt
    ansible-vault encrypt_string --vault-password-file=~/vault_password < datinglibre.key
    
Copy and paste the contents of `datinglibre.crt` as `datinglibre_certificate` in `webservers.yml`. Copy and paste 
the output of `ansible-vault` into `datinglibre_key` in `webservers.yml`.

#### 3. Enter a database password 

In `all.yml` you will need to generate a password for `database_password`:

    ansible-vault encrypt_string --vault-password-file=~/vault_password d@tab@se_pa$$word

#### 4. Enter your `AWS` details and `S3` bucket name and endpoint

The site saves images in a single private `S3` bucket. You will need to create a private bucket in the `AWS` 
administration panel and a user that can access `S3` in the `IAM` panel. 

Copy and paste the bucket name into `images_bucket` in `dating_libre.yaml`. You need to enter the access and secret keys of the `S3` user you created 
as encrypted values `storage_access_key`and `storage_secret_key` in `all.yml`, again using `ansible-vault` as above:

     ansible-vault encrypt_string --vault-password-file=~/vault_password AKIAZXYWVUTSRQP
     ansible-vault encrypt_string --vault-password-file=~/vault_password tH3s3cr3tp4ssw0rd

Next update the `storage_endpoint` and `storage_region` variables in `all.yml` e.g. `storage_region` as `eu-west-2` and `storage_endpoint` as `https://s3-eu-west-2.amazonaws.com`.
    
#### 5. Update hosts to allow ansible to connect to your production servers

Edit `./deploy/production/hosts`, changing `ansible_ssh_host`, `ansible_ssh_user` and `ansible_ssh_private_key_file` to 
use the user and `SSH` key that you use to connect to your production servers. You can remove the section`[testing]` 
and the host.

#### 6. Configure email

The default mailer is Amazon `SES`, so sign up the Amazon `SES` to get an `SMTP` username and password.

Your `SMTP` password may have special characters, which you will need to `URL` encode:

    php -a 
    php > echo urlencode('s3cr3tp/4assw0r?d');
    s3cr3tp%2F4assw0r%3Fd
    php > 

Copy and paste the password to [create the connection string](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport), which will look something like this:

    ses+smtp://AKIABCDEFGH:s3cr3tp%2F4assw0r%3Fd@default?region=us-east-1
    
Encrypt the `DSN` and enter is into `mailer_dsn`:

    ansible-vault encrypt_string --vault-password-file=~/vault_password ses+smtp://AKIABCDEFGH:s3cr3tp%2F4assw0r%3Fd@default?region=us-east-1
 
Enter the email address that will send users notifications and confirmation emails in `dating_libre.yaml`, e.g.

    admin_email: '"Dating Libre" <admin@example.com>'

#### 7. Run Ansible

Finally, install the site by running:

    ansible-playbook -i ./deploy/inventories/production ./deploy/sites.yml --vault-password-file=~/vault_password
    
You can provision the webserver and database separately with:

    ansible-playbook -i ./deploy/inventories/production ./deploy/webservers.yml --vault-password-file=~/vault_password
    ansible-playbook -i ./deploy/inventories/production ./deploy/databases.yml --vault-password-file=~/vault_password
    
Synchronize Symfony `PHP` files and run migrations with:

    ansible-playbook -i ./deploy/inventories/production ./deploy/webservers.yml --vault-password-file=~/vault_password --tags sync
    
#### 8. Add a moderator 

Connect to your webserver and run the `app:users:create` console command:

    /var/www/datinglibre/bin/console app:users:create admin@example.com p@ssw0rd MODERATOR

### Debugging

#### Connect to postgres via pgbouncer from webserver

    psql "sslmode=require port=6543 user=datinglibre dbname=datinglibre sslcert=/etc/datinglibre/database.crt sslkey=/etc/datinglibre/database.key hostaddr=host_address" --password
    
#### View Symfony logs

    tail -f /var/www/datinglibre/var/log/prod.log
    
#### View system services

    sudo systemctl status

## Customization
    
### Categories and attributes

There are four tables that define allow users to match either other:

- `categories`
- `attributes` 
- `user_attributes`
- `requirements`

A user can have one attribute in each category. They can have many requirements in one category.
For two users to match, for each category, they must share at least one of their attributes and requirements.
The categories used for the demo application are as below. To make this application work, you have to create
your own categories and attributes, and modify the forms which save them.

The demo categories and attributes are as below.

         Color                              Shape
     +------------------------+         +------------------------+
     | Attributes             |         | Attributes             |
     | ==========             |         | ==========             |
     |   - Red                |         |   - Triangle           |
     |   - Green              |         |   - Square             |
     |   - Yellow             |         |   - Circle             |
     |                        |         |                        |
     +------------------------+         +------------------------+
     
The following users will match. Here User B has two Shape requirements
and User A matches one of them in that category.

         User A                             User B
         ======                             ======

         Color                              Color
     +------------------------+         +------------------------+
     | Attributes             |         | Attributes             |
     | ==========             |         | ==========             |
     |   - Red                +----+----+   - Green              |
     |                        |    |    |                        |
     | Requirements           |    |    | Requirements           |
     | ============           |    |    | ============           |
     |   - Green              +----+----+   - Red                |
     |                        |         |                        |
     +------------------------+         +------------------------+

         Shape                              Shape
     +------------------------+         +------------------------+
     | Attributes             |         | Attributes             |
     | ==========             |         | ==========             |
     |   - Square             +----+  +-+   - Triangle           |
     |                        |    |  | |                        |
     | Requirements           |    |  | | Requirements           |
     | ============           |    |  | | ============           |
     |   - Triangle           +-------+ |   - Triangle           |
     |                        |    +----+   - Square             |
     +------------------------+         +------------------------+

The categories and attributes can be configured by editing the values in `config/packages/dating_libre.yaml`. 
The names are key values, so you should enter them in lowercase, using underscores for spaces e.g. `long_term`. You can add your own 
translations by editing the attributes and messages translation files:

    long_term: "Long term relationship"

The values you enter into `dating_libre.yaml` will be automatically entered into the database, during
Symfony fixtures processing (in `AppFixtures.php`), and during deployment with Ansible to staging or production. So if you change 
the default values, you'll need to "find and replace" the appropriate values in the Behat tests:

        @search
        Scenario: I can find another user when our first and second categories match
            Given the following profiles exist:
                | email                    | attributes       | requirements   | city   | age |
                | chelsea_blue@example.com | woman, long_term | man, long_term | London | 30  |
        ...
        
Altogether, you will also need to update the following files with your new categories:

- `ProfileEditController.php`
- `SearchIndexController.php`
- `ProfileForm.php`
- `search/index.html.twig`
- `profile/edit.html.twig`
- `ProfileEditPage.php`
- `RequirementsForm.php`
- `RequirementsFormType.php`

You can view a pull request that shows where this has been done for the demo site. GitHub's file filters are useful to show 
what has changed: 

- [View `PHP`, `YAML` and twig changes](https://github.com/datinglibre/datinglibre/pull/9/files?file-filters%5B%5D=.php&file-filters%5B%5D=.twig&file-filters%5B%5D=.yaml).
- [View `.feature` test file changes](https://github.com/datinglibre/datinglibre/pull/9/files?file-filters%5B%5D=.feature). 

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
