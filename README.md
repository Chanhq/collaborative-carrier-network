# Collaborative Carrier Network

## Installation
I have prepared this repo on a macos machine and thus I can at most guarantee for it to work on macos, 
nevertheless there is a high chance that this also works on linux in the same or similar way. 
For windows, it is also similar but probably way more complicated. So using a virtual machine might
come in very handy.

***Remark:*** directory changes are given assuming no other directory changes are performed whilst following this guide

### 1. Prerequisites
- Docker installed (Win, MacOs: Docker Desktop; Linux: Docker compose)
- ``npm`` installed (see official documentation)
- ``ssh`` setup and linked with your tuhh gitlab account 
- ``git`` setup and installed

### 2. Setup - Backend
Clone the github repository:
```
    git@collaborating.tuhh.de:e16/courses/software-development/ss23/group01.git collaborative-carrier-network
```

Navigate into the backend directory, install dependencies and start the backend server:

```
    cd collaborative-carrier-network/backend
```

```
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

```
    ./vendor/bin/sail up
```
I recommend creating an alias for ``./vendor/bin/sail`` since sail is the cli
to interact with the backends application docker container and you will need it 
for various tasks. 

Now we need to migrate the database and seed it, this can be achieved with the following commands

```
    ./vendor/bin/sail artisan migrate:refresh && ./vendor/bin/sail artisan db:seed
```

### 3. Setup - Frontend
Navigate back into the frontend directory: 
```
    cd ../frontend
```

Install dependencies and start frontend server
```
    npm install && npm start
```

### 4. Evaluation 
When accessing the following urls via your browser you should see 
the dummy landing pages of the respective project

- http://localhost:3000 (React frontend)
- http://localhost:80 (Laravel backend - should return a 404)
