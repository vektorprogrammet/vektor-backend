# vektor-backend

(Currently in progress)

## Set up development environment
### Requirements:
- [PHP](http://php.net/downloads.php) version 7.4
- [Node](https://nodejs.org/en/) version 14
- [Git](https://git-scm.com/)

### Setup:

#### Clone files:
`git clone https://github.com/vektorprogrammet/vektor-backend.git`

##### UNIX:
`npm run setup`
##### Windows:
`npm run setup:win`

#### Start server on http://localhost:8000
`npm start`

##### Alternatively
you can also run the app using symfony's own local web server with `npm run start:symfony`

#### Build static files
When adding new images or other non-code files, you can run:

`npm run build`

so that the files are put in the correct places. (this is automatically
done when doing `npm start`)

### Users
| Position     | Username   | Password |        Role        |
| :----------: | :--------: |:--------:|:------------------:|
| Assistent    | assistent  |   1234   |      ROLE_USER     |
| Teammedlem   | teammember |   1234   |  ROLE_TEAM_MEMBER  |
| Teamleder    | teamleader |   1234   |  ROLE_TEAM_LEADER  |
| Admin        | admin      |   1234   |      ROLE_ADMIN    |


## Database

### Add new entities to the database and reload fixtures
`npm run db:update` or `npm run db:reload`
