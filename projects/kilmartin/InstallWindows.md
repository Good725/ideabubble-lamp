# Install guide - Windows

Use this guide to set up our platform locally for the first time. In the lines below we'll try to set up the project "Kilmartin".

As you are going to run a web project locally, you'll need to have a web server running in your computer. For that purpose,  in Windows we recommend using WAMP or XAMP.

### Requirements

* **PHP up to 7.0.*** (newer than that will raise compatibility errors).
* MySQL 5.*.
* A GIT client.
* Make sure you have enabled these modules in Apache: **"alias_module", "rewrite_module", "vhost_alias_module", "mod_headers"**.
* Make sure you have enabled this config in PHP.ini: **"short_open_tag"**.

### Tips
* We use PHPSTORM and aim to have proper style/formatting (PSR0/1).
* We recommend: MAMP PRO for MacOS, LAMP for *nix, and WAMP/XAMPP for Windows.
* **PLEASE ENSURE TO READ THE COMPLETE SETUP GUIDE** before attempting any steps

## About the platform

### Our stack is made up of:
* Kohana, MySQL 5.6, PHP 7.0, Apache 2.2+, GIT 1.8+.
* We have a DALM (database abstraction layer model) setup that allows for all SQL updates on Model folders to auto install on each database.
* Bootstrap Framework is used for all backend screens.

### Our platform components include:
* **Engine**: Contains our core, kohana, vendor, products plugins. The engine drives all our products, websites and custom solutions that we deliver for our customers.
* **Projects**: Contains our customer websites, custom projects and our products plugins.

## Steps

### Step 1 - GIT

1. Please create a BITBUCKET account and email your ID to michael@ideabubble.ie. Once approved you will have access to the codebase needed to install our platform locally.

2. If when cloning through git you get this error: 
> gnome-ssh-askpass: Gtk-WARNING **: cannot open display

Type this in console: 
> unset SSH_ASKPASS.

### Step 2 - Codebase

Create a folder named **wms**, which will be the root of all the projects, including the engine. Open a console in that folder and execute the following:
> ``mkdir engine,media,projects``
> ``cd media/``
> ``mkdir kilmartin``
> ``cd ..``
> ``cd projects/``
> ``mkdir kilmartin``
> ``cd kilmartin/``
> ``mkdir www``
> ``cd www/``
> ``mkdir media``

2. The folder structure should look as follows:
>
                 -/wms
                        -/engine
                        -/media
                                -/kilmartin
                        -/projects
                                -/kilmartin
                                        -/www
                                                -/media

3. Now using git we'll check out the two components of every project: the *engine* (common to all projects) and the *project's code* itself. In the next step you'll find the git command needed for checking out, but first have a look on **where** you should be checking out each thing:

                 -/wms
                        -/engine (https://bitbucket.org/ideabubble2/wms-engine/)
                        -/media
                                -/kilmartin
                        -/projects
                                -/kilmartin (https://bitbucket.org/ideabubble2/wms-kilmartin)
                                        -/www
                                                -/media

5.  Use this command to checkout the projects (remember to write your username, and change the project address if needed).
> git clone -b testing https://\<username\>@bitbucket.org/ideabubble2/wms-engine

Notice that we are checking out the **testing** branch; unless told otherwise, use that branch.

5. Creating the symlinks. We'll communicate the projects and the engine through symlinks (symbolic links). To create them, open the console on the root folder (wms/) and execute:
> cd projects\kilmartin\
> ``mklink /D engine ..\..\engine``
> ``cd www\``
> ``mklink /D media ..\..\..\media\kilmartin``
> ``mkdir shared_media``
> ``cd shared_media\``
> ``mklink /D kilmartin ..\..\..\..\media\kilmartin``

6. Repeat these steps for every NEW project you might be working on. As the engine code is shared, checking it out once will suffice.

### Step 3- Webserver

1. Add a new Apache vhost for ``kilmartin.websitecms.local`` site using this code below: 
>
    <VirtualHost *:80>

        ServerName kilmartin.websitecms.local

        # Base Directory
        DocumentRoot /var/www/wms/projects/kilmartin

        # Redirecting Rules (WARNING! BE CAREFUL WITH THE ORDER OF THE RULES!)
        RewriteEngine On
    
        # Static removal - DocRoot in projects
        RewriteRule ^/engine/plugins/([^/]+)/(.+) /engine/plugins/$1/development/assets/$2 [L]
        RewriteRule ^/engine/(.+) /engine/application/assets/$1 [L]
        RewriteRule ^/plugins/([^/]+)/(.+) /plugins/$1/development/assets/$2 [L]
        #RewriteCond %{HTTP_HOST}%{REQUEST_URI} ^([^\.]+)\.[^/]+/plugins/([^/]+)/(.+)
        #RewriteRule ^/ /plugins/%2/development/assets/%3 [L]
    
        # Public Project Content (e.g. www.customer.domain.com/(assets|media)/path/to/content)
        RewriteRule ^/(assets|media|shared_media)/(.+) /www/$1/$2 [L]
    
        # Project Root (e.g. www.customer.domain.com/controller/action)
        RewriteRule ^/ /www/index.php/%{REQUEST_URI} [L]
    
        # Base Directory
        <Directory "/var/www/wms/projects/kilmartin">
            Options -Indexes +FollowSymLinks +MultiViews
            AllowOverride All
            Order Allow,Deny
            Allow From All
        Require all granted
        </Directory>
    
        # Enable CORS
        <FilesMatch "\.(ttf|otf|eot|woff|php)$">
            Header Set Access-Control-Allow-Origin "*"
        </FilesMatch>
    
        # Set Kohana Environment
        SetEnv KOHANA_ENV DEVELOPMENT
    
        # Database connection parameters
        SetEnv dbhostname localhost
        SetEnv dbusername ib_test
        SetEnv dbpassword 1b_t35t
    
    </VirtualHost>

2. Add this host entry in the hosts file:
>127.0.0.1 kilmartin.websitecms.local

3. URL configurations (informative). We run a URL configuration for different modes of our applications.
* Local mode: PROJECTNAME.websitecms.local (local mode on your machine)
* Test mode: PROJECTNAME.test.ibplatform.ie (used in our local network only, not available externally)
* Uat mode: PROJECTNAME.uat.ibplatform.ie (we use this mode for testing internal and showcasing to customers)
* Production mode : Custom URL http://www.custom-url.com

### Step 4 - Database

1. We'll create a user with admin privileges, which we'll use throughout the projects. Run these scripts in your MySQL client:

> ``CREATE USER 'ib_test'@'localhost' IDENTIFIED BY '1b_t35t'; GRANT GRANT OPTION ON *.* TO 'ib_test'@'localhost';``

> ``GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, RELOAD, SHUTDOWN, PROCESS, FILE, REFERENCES, INDEX, ALTER, SHOW DATABASES, SUPER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, REPLICATION SLAVE, REPLICATION CLIENT, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, CREATE USER, EVENT, TRIGGER ON *.* TO 'ib_test'@'localhost';``

2. Next we'll create the project's database. Run these scripts:

>  ``CREATE DATABASE wms_development_kilmartin DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci; ``

> ``GRANT ALL PRIVILEGES ON wms_development_kilmartin.* TO 'ib_test'@'%' IDENTIFIED BY '1b_t35t'; ``

>`` GRANT ALL PRIVILEGES ON wms_development_kilmartin.* TO 'ib_test'@'localhost' IDENTIFIED BY '1b_t35t'; ``

> ``flush privileges;``

3. The system will try to install and populate the database skeleton when you run the front end or backend using DALM. If for some reason it fails to populate it, please request the latest DB script for the project you are working on. Latest dump for Kilmartin's project can be found here: http://uat.ideabubble.ie/wms_development_kilmartin.sql.gz

4. With the DB populated, we'll create an admin user which we'll use for testing. Run:

> ``INSERT IGNORE INTO \`engine_users\` (\`role_id\`,\`email\`,\`password\`,\`name\`,\`surname\`,\`registered\`,\`email_verified\`,\`can_login\`,\`deleted\`,\`status\`) SELECT engine_project_role.id, 'devadmin@ideabubble.com', '7a46ac225fdc315fce0192a5017238ae39676fe7b75eaf0b848dbfb4ad519593', 'Test', 'Ideabubble', CURRENT_TIMESTAMP, 1, 1, 0, 1 FROM engine_project_role WHERE role = 'Administrator';``

5. And **only** if you imported a TEST database (see point 3), execute this query:

> ``UPDATE engine_settings SET value_dev = value_test;``

### Step 5 - Media files

1. Download this file: http://uat.ideabubble.ie/kilmartin-media.tgz
2. And extract it in /www/wms/media/

### Step 6 - Verify setup

* The local copy of the code should be available at end on link: http://kilmartin.websitecms.local/
* The front end which should be visible by running http://kilmartin.websitecms.local
* The backend login screen which should be visible by running http://kilmartin.websitecms.local/admin
* The backend dashboard screen you access when you login above with the credentials below:
* The login user for this access is devadmin@ideabubble.com / 2016password

**To complete the setup please ping Mike with screenshots of the system working locally for the frontend and the backend.**

Any questions you might have about this guide contact Mike (michael@ideabubble.ie).
