# Installation Guide

Hi! this is a demo backend project for Innoscripta in #laravel-v11.*
Before continue, please make sure you have this working on your computer:

* Git (Recommended Git version 2.34.*)
* Docker (Recommended Docker version 24.*)
* Docker compose (Recommended Docker Compose version v2.23.*)

## Initial Steps

First, clone using Git:

```bash
git clone https://github.com/dduenas-niomads/innoscripta-backend-challenge
```
Go to the project directory:

```bash
cd laravel-backend-app
```

## Docker Setup

This particular docker environment works with docker-compose and make commands. 
So you need to have make. If you don't, please run this following command:

```bash
sudo apt install make
```
After the installation, you're going to have Make. 
MAKE allows you to use Docker in an easy (and professional) way
without enter to the repository to do basic tasks as migrations, testing or jobs.

To continue, run the next command to run the setup the environment

```bash
make initial-setup 
```
After the setup, you're going to have:

* Clean Laravel v11.* in _/backend_ folder
* Docker container with Apache and Php8.4
* MySQL Server (Latest version)
* Adminer (Webapp to handle data)
* Api running at localhost:5000 

Verify the running containers with 

> docker ps

If you need to change the ports of the containers, just replace it in docker-compose.yml

## Database

The database is fundamental to the operation of this API. Before proceeding, you need to fill in the values for 
_prod_ and _test_ databases. Here is an example of my local environment:

```bash
DB_CONNECTION=mysql
DB_HOST=mysql_server
DB_PORT=3306
DB_DATABASE=innoscripta_prod
DB_USERNAME=root
DB_PASSWORD=root

DB_HOST_TEST=mysql_server
DB_PORT_TEST=3306
DB_DATABASE_TEST=innoscripta_test
DB_USERNAME_TEST=root
DB_PASSWORD_TEST=root
```
Much of the development is geared towards consuming information from external sources. 
Therefore, it is necessary that you enter the apiKeys of these sources that were used in the API.

NYT_URL=https://api.nytimes.com/svc/mostpopular/v2/viewed/1.json
NYT_APIKEY=xxx

NEWS_API_ORG_URL=https://newsapi.org/v2/everything
NEWS_API_ORG_APIKEY=xxx

NEWS_API_AI_URL=https://eventregistry.org/api/v1/article/getArticles
NEWS_API_AI_APIKEY=xxx

**Important => If you don't have a valid apiKey, the resource is not going to work properly.

## Environment

When the instalation is completed, please running all tests with the next command

```bash
make test
```
You are going to see this output: 
```bash
  Tests:    41 passed (44 assertions)
  Duration: 25.32s
```

If everything goes right with testing, please run the next command to populate the production database
```bash
make migrate-seed
```
After this migration and seeding, you're going to have a demo user and password. Use it wisely ;)

* email => dduenas@niomads.com
* password => Backend#2024

## Documentation

The documentation was created with Scramble and you can find it here => localhost:5000/docs/api

## Coding

This coding adhere to Laravel best practices and coding standards. 
I try to write clean, maintainable, and well-documented code.

## Testing

Highly committed software testing to use cases. Here you're going to find:
    
* 40 feature tests
* 1 unit test. 
* Independent database for testing (Phpunit.xml configuration)

Enjoy running all tests with _make test_ command on the root folder.

## Performance

Here you will find interesting things like:
    
* Limiting API calls with Throttle (Different limits for users and guests...)
* Caching strategies for fetching data
* Scheduling commands to pull data from external sources
* Concurrency! To handle multiple operations at the same time.
* Personal tips with Eloquent local scopes, Data Resources and traits!

## Security

Here I applied my favorite strategies:
    
* Auth middleware to handle data
* API to reset password using a code (Email information is needed)
* Very detailed request validations for important resources
* Handling of primary keys to have user-friendly urls in show method (Avoid using resource/{id})
* And more!

Thanks for watching and don't forget to rate this project!

Best,
Daniel