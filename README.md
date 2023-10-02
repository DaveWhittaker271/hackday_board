# Hackday Board Thing

- Vue3 & [Vue3-styled-components](https://www.npmjs.com/package/vue3-styled-components)
- Quasar v2
- PHP8 & MySQL8
- Apollo for GraphQL

## Project Setup

 - Install npm, PHP8, MYSQL8, nginx


 - Create a new database in MYSQL


 - There is a sample nginx config file in `/conf/nginx.sample.conf`


 - There is an `.env.example` file in `/conf/` - rename this to `.env` and change details as necessary


 - Open bash terminal into top level directory and run `npm install` and `composer install`


 - Once installed you can run `npm run dev` to launch a hot-reload dev environment which should be on `https://hackdayboard.com:9000`

## Migrations
[Doctrine Migrations](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.6/index.html) are used to install database tables, initially there are no tables or migrations to run

 - There is a terminal script used for managing and creating migrations, run `php bin/migrations` to see a list of commands


 - To install migrations use `php /bin/migrations migrate`


 - To create a migration first create the DB table or change manually onto the local database, then run `php /bin/migrations diff`


 - Once database changes have stabilised `php /bin/migrations rollup` can be used to remove all existing migrations and create one new one with the current state of the database

## ORM
[Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/2.16/reference/basic-mapping.html) is used for entity declarations, stored in the `/server/classes/entity` folder

## Making database changes
When adding new tables/columns etc. to the database this will also involve creating a migration and updating the entities to match:

 - Make the changes necessary on the DB first


 - Create or update the relevant entity class in `/server/classes/entity`


 - Create or update mapping .xml file in the `/server/classes/entity/mapping` directory


 - Run `php bin/doctrine orm:validate-schema` to ensure the XML mapping is correct


 - If the validation fails then run `php bin/doctrine orm:schema-tool:update --dump-sql` which will list SQL changes necessary to bring the mapping into line with the actual DB table, useful for figuring out what is different between XML and DB


 - Generate a migration with `php bin/migrations diff`

## Documentation pages
 - [Quasar Config](https://quasar.dev/quasar-cli-vite/quasar-config-file#property-sourcefiles)
 - [Vue Apollo](https://v4.apollo.vuejs.org/guide-option/mutations.html)
 - [Bootstrap Icons](https://icons.getbootstrap.com/)
 - [Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/2.16/reference/basic-mapping.html)
 - [Doctrine Migrations](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.6/index.html) 