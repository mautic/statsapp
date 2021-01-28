# Mautic statsapp

This application is used to process telemetry from Mautic instances. It stores things like Mautic version, PHP version, etc. and currenty lives on https://updates.mautic.org/stats/.

## Local setup

If you want to make local changes to this repo, please do the following:

- Clone this repo
- Copy `app/config/parameters.yml.dist` to `app/config/parameters.yml` and make sure to put in your database host/credentials/etc. - you should be able to leave all other config options untouched.
- Run `export SYMFONY_ENV=dev` to make sure that Symfony uses the development environment.
- Run `composer install` to install the dependencies.
- Run `bin/console doctrine:fixtures:load` (ONLY in local environments!) to put some dummy data into the database

## Putting changes live on production

The live version on https://updates.mautic.org/stats doesn't update itself currently. Here's what you need to do to publish any changes you make in the `master` branch:

- SSH into the server (the infrastructure team can give you access)
- `cd /var/www/statsapp`
- `git pull` --> this will pull the latest changes from this Git repo
- `composer install` --> this will also clear the cache
- Good to go! Double-check if you can still access the site. 
