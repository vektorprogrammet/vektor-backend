# vektor-backend

## Set up development environment
### Requirements:
- [PHP](http://php.net/downloads.php) version 7.4
- [Node](https://nodejs.org/en/) version 14
- [Yarn](https://yarnpkg.com)
### Recommended:
- [Symfony CLI](https://symfony.com/download)
- [Docker](https://www.docker.com/products/docker-desktop)

### PHP dependencies
- php7.4-zip
- php7.4-gd
- php7.4-sqlite3
- php7.4-xml
- php7.4-mbstring

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
`symfony server:start` (requires Symfony CLI)

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
`yarn db:update` or `yarn db:reload`


### Dev:
Load db-schema:
`php bin/console doctrine:schema:update --force`

Load fixtures:
`php bin/console doctrine:fixtures:load`

Code style:
### Code Style Fixer [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
Install:
```
mkdir -p tools/php-cs-fixer
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
```

Then Run:
`tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src`


## Tests
**Run all tests:**\
`php bin/phpunit --configuration phpunit.xml.dist`

**Run individual test:**\
`php bin/phpunit "path/to/test/TestName.php"`