
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

## Development environment setup

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/Development).

## Deployment

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/Deploy-production).

## Documentation

See the [Wiki](https://github.com/datinglibre/DatingLibre/wiki/).

## Features

- [Automated deployment using Ansible](https://github.com/datinglibre/DatingLibre/wiki/Deploy-production)
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
