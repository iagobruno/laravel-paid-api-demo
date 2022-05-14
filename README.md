# laravel-paid-api-demo

...escrever uma descrição...

## Run Locally

Clone this repo and run commands in the order below:

```bash
composer install
cp .env.example .env # And edit the values
php artisan key:generate
```

Then start Docker containers using [Sail](https://laravel.com/docs/8.x/sail):

```bash
sail up -d
```

And run the migrations:

```bash
sail artisan migrate
sail artisan db:seed # Optional
```

### Front-end assets

Open another terminal tab and run the command below to compile front-end assets:

```bash
sail yarn install
sail yarn run watch
```

Now you can access the project at http://localhost in the browser.

## Running tests

To run tests, first create a database named "testing-laravel"

```sql
CREATE DATABASE "testing-laravel";
```

And run the following command:

```bash
sail artisan test
# sail artisan test --filter GetUserTest
# sail artisan test --filter "Deve retornar um erro ..."
# sail artisan test --stop-on-failure
```

> **NOTE**: Make sure you started the docker containers first.

## Stripe Webhooks

Use the [Stripe CLI](https://stripe.com/docs/stripe-cli) to simulate Stripe events in your local environment. [Learn more about Webhooks](https://stripe.com/docs/webhooks).

```
stripe listen --forward-to localhost/stripe/webhook
```

```
stripe trigger payment_intent.succeeded
```
