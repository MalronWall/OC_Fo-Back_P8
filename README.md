Projet_8
========

# Todo & Co

## Project installation

1.  Code recovery

    1. Git

        Connect with SSH key to your server.  
        Use the command : `git clone https://github.com/MalronWall/OC_Fo-Back_P8.git`

    1. FTP

        Download this [repository](https://github.com/MalronWall/OC_Fo-Back_P8/archive/master.zip).  
        Use a FTP client, for example [FileZilla](https://filezilla-project.org/) and connect to the server.  
        Use the FTP client to transfert the repository on your server.

1. Configuration

    Update environnements variables in the .env file with your values.
    At the very least you need to define the SYMFONY_ENV=prod

1. Vendors installation

    1. Composer

        If you can use Composer in your server, use `composer install --no-dev -ao` for optimized installation of vendors.  
        If you can't use Composer, download [Composer.phar](https://getcomposer.org/download/) and use `php composer.phar install --no-dev -ao`.

    1. FTP

        If you can't use the both solutions, use your FTP client to download all vendors.  
        This solution is to be used only if no solution with Composer works.

1. Assets install

    Run the command `yarn install` and `yarn encore production` for assets.

1. Database creation

    Use the command `php bin/console doctrine:database:create` for database creation.  
    Use the command `php bin/console doctrine:migrations:migrate` for creation of the tables.  
    Use the command `php bin/console doctrine:fixtures:load` for load some data in database.
