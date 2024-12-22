# Installation Guide

Before proceeding with the installation, please make sure that Git and Docker are working on your system.

## Initial Steps

First, clone using Git:

```bash
git clone https://github.com/dduenas-niomads/innoscripta-backend-challenge
```
Go to the project directory:

```
cd innoscripta-backend-challenge
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

* Clean Laravel v11.*
* Docker container with Apache and Php8.4
* MySQL Server (Latest version)
* Adminer (Webapp to handle data)

Verify the running containers with 

> docker ps

## Database
1. change .env values
2. run make migrate
## Code
1. Auth => Complete Auth API with Sanctum
2. Password reset with API => Complete flow with code validation. To use email notification, credentials are needed.
## Testing
1. All feature tests for Auth API
2. All feature tests for PwdRestore API