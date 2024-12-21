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
Then, run the next command to run the setup

```bash
make initial-setup 
```
After the setup, you're going to have:
    - Clean Laravel v11.*
    - Docker container with Apache and Php8.4
    - MySQL Server (Latest version)
    - Adminer (Webapp to handle data)