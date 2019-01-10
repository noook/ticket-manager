# Tickr

A simple Ticket Manager written in Vue and Symfony

Check out the live version at [https://tickr.nook.sh](https://tickr.nook.sh) !

## Installation

### Client

Go the the `/client` folder and run `npm i` to install the dependencies, then build the project with `npm run serve`.

As the Vue app is using the history mode of the Vue Router, we have to setup our webserver to redirect all request to `index.html`. We will do it later.

If you intend to use the client locally, all request will be made to `http://ticket-manager.ml`, so set an alias host to your machine if you need so :)

### API

Now go to the `/api` folder. first, be sure to edit your `.env` file or make a copy of it such as `.env.local` to extend the default one.

Set `APP_ENV` to `prod` and fill in your credentials at `DATABASE_URL` like `DATABASE_URL=pgsql://username:"password"@127.0.0.1:5432/dbname`.

Once done, install them with `composer install`.

Run `php bin/console doctrine:database:create` to create the database then `php bin/console doctrine:migrations:migrate` to make database structure compatible with our data structure.

If you need to put some data to start the app **on the first launch** (Otherwise it will crash as data will be added twice and two users can't have the same username.), you can run `php bin/console doctrine:fixtures:load --env=prod --append`.

### Database

I'm on Postgresql, version 10.5, I guess you can any similar version, but it works perfect on 11.1 too

### Web server configuration

I'm using nginx, and you can ask me at [me@neilrichter.com](mailto:me@neilrichter.com) for the webserver configuration.
