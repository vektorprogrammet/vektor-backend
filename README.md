# vektor-backend

## Set up development environment
### Requirements:
- [PHP](https://php.net/downloads.php) version 8.2
- [Node](https://nodejs.org/en/) version 14
- [Yarn](https://yarnpkg.com)
### Recommended:
- [Symfony CLI](https://symfony.com/download)
- [Docker](https://www.docker.com/products/docker-desktop)

### PHP dependencies
- php8.2-zip
- php8.2-gd
- php8.2-sqlite3
- php8.2-xml
- php8.2-mbstring
- php8.2-intl

### Setup:

##### Docker:
Build docker image
`yarn docker:build`

Set up docker image
`yarn docker:setup`

Run commands in docker image:
`yarn docker:run <CMD>`


##### UNIX:
`yarn setup`

#### Start server on http://localhost:8000
`yarn start`

##### Alternatively
`symfony serve` (requires Symfony CLI)

##### Start server on Docker
`yarn docker:run`


#### Build static files
When adding new images or other non-code files, you can run:

`yarn build`

so that the files are put in the correct places. (this is automatically
done when doing `yarn start`)

### Users
| Position     | Username   | Password |        Role        |
| :----------: | :--------: |:--------:|:------------------:|
| Assistent    | assistent  |   1234   |      ROLE_USER     |
| Teammedlem   | teammember |   1234   |  ROLE_TEAM_MEMBER  |
| Teamleder    | teamleader |   1234   |  ROLE_TEAM_LEADER  |
| Admin        | admin      |   1234   |      ROLE_ADMIN    |


## Database

### Add new entities to the database and reload fixtures
`yarn db:update`


### Dev:
Load db-schema:
`php bin/console doctrine:schema:update --force --complete`

Load fixtures:
`php bin/console doctrine:fixtures:load`

### Code Style: [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
Install:
```
mkdir -p tools/php-cs-fixer
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer --dev
```

Then Run:
`tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src`


## Tests
**Run all tests:**\
`php bin/phpunit`

**Run individual test:**\
`php bin/phpunit "path/to/test/TestName.php"`


## API
To generate SSL keys:\
`php bin/console lexik:jwt:generate-keypair`

To get JWT token:\
`curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login -d '{"username":"<username>","password":"<password>"}'`

Pass the token in subsequent api calls.
Example shown using Postman:
![image](https://github.com/vektorprogrammet/vektor-backend/assets/46197518/b7a36722-93de-4b10-9853-0a8c36d0faa6)

