# Update demo server of the project.

In this document we are going to update the project which deployed as the demo version of the ecommerce project in
server with example address of `172.16.10.12` as demo server and another server with address `178.216.249.218` as the
main server. Both servers have SSH service listening on port `22`.

First connect to the main server and create a backup of the database in the `/project/path/data/` directory:
```bash
ssh developer@178.216.249.218 #then enter the password.
cd /path/to/project
cd data
mysqldump -u db_user -h db_host -p db_name > db_name_current_data.sql #enter the db password.
#then close the ssh connection.
```

Next we have to make ssh connection to the demo server :
```bash
ssh root@172.16.10.12 #then enter the password to connect to the server.
```

Of course we have to change the current user to the owner of project root directory:
```bash
su - apache #or any other user which owns the root directory.
```

Change directory to the root of the demo project:
```bash
cd /path/to/the/root/of/the/demo/project #/var/www/demo.example.com for example
```

Then we have to sync the project directory with the main server:
```bash
rsync -avz --exclude '.env' developer@178.216.249.218:~/main/project/path/ ./
```

Restore the database:
```bash
mysql -u db_user -p -e "drop database demo_db_name; create database demo_db_name" #recreate demo_db
mysql -u db_user -p demo_db_name < ./data/db_name_current_data.sql #restore demo_db
```

Update project to latest demo changes:
```bash
cd /path/to/project
git checkout demo-branch #or any other branch you need. note that when you sync local dir with main server the branch changes
git pull
php artisan migrate
./node_modules/gulp/bin/gulp --production
cd data/template-project
git checkout demo-branch
git pull
./node_modules/gulp/bin/gulp --production
cp -rf public/views/* /path/to/project/resources/hc-template/originals/ && cp -rf public/HCMS-assets/ /path/to/project/public_html/
cd /path/to/project
php artisan hctemplate:init
#and enter any other commands needed for update process.
```

***Good luck***