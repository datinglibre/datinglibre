# DatingLibre

DatingLibre is an alpha open source Symfony 4.4 `PHP` 7.4 project backed by PostgreSQL, which can introduce
people based on geographical location, requirements and attributes. The requirements and attributes can be setup
not only for relationships, but also for hobbies, such as finding a tandem language partner.

As an alpha release, it may include bugs and security issues.

![Image showing profile edit page](https://raw.githubusercontent.com/datinglibre/datinglibre.github.io/main/profile.png "Profile edit page")


## Development

To run all tests, install the following. Ubuntu 20.04 is recommended.

  - Chromium `snap install chromium`
  - [Chromedriver](https://chromedriver.chromium.org/) (`sudo mv chromedriver /usr/local/bin`)
  - [Java 11](https://docs.azul.com/zulu/zuludocs/ZuluUserGuide/PrepareZuluPlatform/AttachAPTRepositoryUbuntuOrDebianSys.htm) (for Selenium) 
  - [Selenium Standalone](https://www.selenium.dev/downloads/)
  - `PHP` 7.4 (`sudo apt install php7.4 php7.4-json php7.4-curl php7.4-simplexml php7.4-pgsql php7.4-intl`)
  - Composer (`sudo apt install composer`)
  - Docker and docker compose (`sudo apt install docker docker-compose` `sudo usermod -aG docker your_username` `sudo systemctl enable docker`, log out then in again to refresh groups).
  - [Symfony CLI](https://symfony.com/download).
  
### Code Style

`php-cs-fixer` is configured with PSR1 and PSR2 standards.

    ./vendor/bin/php-cs-fixer fix
        
### Testing 

#### 1. Start Selenium:    
    
    java -jar  selenium-server-standalone.jar
    
#### 2. Install dependencies:

    composer install    
    
#### 4. Start the internal webserver:

    symfony serve       
    
#### 5. Run the setup script

This will start `mailhog`, `S3Ninja` and `postgres` docker containers, run the database migrations
and install test fixtures:
    
    ./setup.sh
    
`docker` will expose the following services to `localhost`:

| Service    | Ports         |
| -----------|---------------|
| [MailHog](https://github.com/mailhog/MailHog)    | 1025/SMTP     |
| MailHog UI | 8025/HTTP     |
| Postgres   | 5432/TCP      |
| [S3 ninja](https://s3ninja.net/) | 9444/HTTP     | 
    
The setup script is required as the project repurposes Doctrine's entities as "projections", which are classes
that are not entities in their own right, but are used to display combinations of entity data straight to the view.
If the setup script isn't run, then doctrine will attempt to drop non-existent projection tables.

#### Run `behat` `BDD` scenarios:

    ./vendor/bin/behat
    
The default password for all test accounts is `password`.

#### Run `phpunit` tests:

    ./bin/phpunit

## Deployment

### Staging 

A staging environment can be created using [Vagrant](https://www.vagrantup.com/). It is intended to work as closely as possible to production, so also 
encrypts variables in the configuration using `ansible-vault`. Usually the Vault's password file should not be committed,
however as these are staging values, the password is available as `staging_vault_password`, and is used in the `Vagrantfile`.

The staging configuration contains self-signed certificates, so you will need to make a security exception for these
when your browser displays an error page.  

Install the following:

    sudo apt install vagrant virtualbox ansible
    vagrant plugin install vagrant-hostmanager

Then follow these steps:

#### 1. Update paths to the Vagrant private keys

The path to private keys has to be hardcoded, so copy `hosts.dist` and edit the path to your `datinglibre` directory: 

    cp deploy/inventories/staging/hosts.dist deploy/inventories/staging/hosts

#### 2. Create your own categories and attributes

Copy the distribution file and edit the values, or keep them as they are.

    cp deploy/roles/datinglibre/vars/datinglibre.yaml.dist deploy/roles/datinglibre/vars/datinglibre.yaml

Ansible will parse these values and enter them into the `datinglibre.categories` and `datinglibre.attributes` tables.
    
#### 3. Install the location files

The following files are distributed as part of the [separate DatingLibre locations repository](https://github.com/datinglibre/datinglibrelocations).
 - `countries.sql`
 - `regions.sql`
 - `cities.sql`

Copy them to`deploy/roles/datinglibre/locations`. 

#### 4. Start the servers 
   
    vagrant up 

The virtual machines are setup as follows:

| Hostname                 | IP            | Ports                                 |
| -------------------------|---------------|---------------------------------------|
| datinglibre.local        | 192.168.0.99  | 80/HTTP 443/HTTPS                     |
| datinglibredb.local      | 192.168.0.100 | 5432/POSTGRES SSL 6543/PGBOUNCER SSL  |
| datinglibretesting.local | 192.168.0.101 | 8025/HTTP 1025/SMTP 9444/HTTP         | 

If you need to provision any host again, use `vagrant provision`, e.g.:

    vagrant provision datinglibre
    
### Production

You will first need `SSH` access to two servers, or virtual servers, running Ubuntu 20.04 with at least 1GB RAM, which 
works for testing purposes. You will also need an `AWS` account with `IAM` users setup for `S3` for file storage
and `SES` for sending emails.

#### 1. Create configuration for your production inventory 

Create `production` inventory using `staging` inventory as a template:

    cp -R ./deploy/inventories/staging ./deploy/inventories/production    

Create a long password in a file in your home directory called `vault_password`. This should not be committed 
to source control. You can remove the `deploy/inventories/production/` exclusion in `.gitignore` if you want to commit
your inventory to a private git repository. 

#### 2. Generate certificates

Generate self-signed certificates for the database:

    openssl req -x509 -newkey rsa:4096 -sha256 -nodes -keyout database.key -out database.crt -days 3650
    ansible-vault encrypt_string --vault-password-file=~/vault_password < database.key

Copy and paste the output of the `ansible-vault` command into the `database_key` section of `all.yml`. 
Copy and paste the contents of `database.crt` into the `database_cert` and `database_root_cert` sections of `all.yml`.
Make sure there is a new line after `|` and `!vault |`. You can delete `database.crt` and `database.key` afterwards.

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

Copy and paste the bucket name into `images_bucket` in `webservers.yml`. You need to enter the access and secret keys of the `S3` user you created 
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
 
Enter the email address that will send users notifications and confirmation emails in `admin_email`. If you 
use the full format address, you'll need to escape the quotes for Ansible e.g.:

    admin_email: "\"Dating Libre\" <admin@datinglibre.com>"

#### 7. Run Ansible

Finally, install the site by running:

    ansible-playbook -i ./deploy/inventories/production ./deploy/sites.yml --vault-password-file=~/vault_password
    
You can provision the webserver and database separately with:

    ansible-playbook -i ./deploy/inventories/production ./deploy/webservers.yml --vault-password-file=~/vault_password
    ansible-playbook -i ./deploy/inventories/production ./deploy/databases.yml --vault-password-file=~/vault_password
    
Synchronise Symfony `PHP` files and run migrations with:

    ansible-playbook -i ./deploy/inventories/production ./deploy/webservers.yml --vault-password-file=~/vault_password --tags sync
    
#### 8. Add a user

Connect to your webserver and run the `app:users:create` console command:

    /var/www/datinglibre/bin/console app:users:create email@example.com pa$$w0rd USER
    /var/www/datinglibre/bin/console app:users:create admin@example.com p@ssw0rd MODERATOR

### Debugging

#### Connect to postgres via pgbouncer from webserver

    psql "sslmode=require port=6543 user=datinglibre dbname=datinglibre sslcert=/etc/datinglibre/database.crt sslkey=/etc/datinglibre/database.key hostaddr=host_address" --password
    
#### View Symfony logs

    tail -f /var/www/datinglibre/var/log/prod.log
    
#### View system services

    sudo systemctl status

## Design    
    
### Matching

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

## Credits

The `countries.sql` `regions.sql` and `cities.sql` files were created by processing geographical data from [GeoNames](https://www.geonames.org/)
and are licensed under [Attribution 4.0 International (CC BY 4.0)](https://creativecommons.org/licenses/by/4.0/).

The `src/Security/LoginFormAuthenticator.php` is based on Symfony documentation [How to build a login form](https://symfony.com/doc/current/security/form_login_setup.html)
and is licensed under [Creative Commons BY-SA 3.0](https://creativecommons.org/licenses/by-sa/3.0/).

## Licence

Copyright 2020 DatingLibre.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
