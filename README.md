Simple API Email Sender
========================

This system is still a work in progress and not 100% complete yet, so there are some things that are still not working as they should.

## Quick run with docker (not installed properly)

This docker has 3 service namely app, db and webserver

- Open your terminal dan go to project directory
- Compose the docker
```
docker compose up --build
```
- Open new terminal and go to project_directory/src
```
composer install
```
- Check if the docker working properly, open your browser and visit http://127.0.0.1:8080/api/ and you will see a blank page
- Open service app's terminal and running queue consumer
```
cd /var/www/html/background
php consumer.php
```
## How to use the API

- You can use postman and import postman collection (API-Email.postman_collection)

**Important Notice For Authentication**

- Email: mochamad.rangga@gmail.com
- password: password