#!/bin/bash

echo Creating resources has been started...

for name in $(ls app/Models)
do
   IFS='.' read -a parts <<< "$name"
   model=${parts[0]}
   controllerName=${model}Controller


   if [ -f app/Http/Controllers/Admin/${controllerName}.php ];
    then
    echo Resource Controller ${controllerName} has been already exsists exsists!
    else
    $(php artisan make:controller App\\Http\\Controllers\\Admin\\${controllerName} --resource --quiet)
    echo ${controllerName} created succesfully!
   fi

done

echo Creating resources has been finished successfully :D
