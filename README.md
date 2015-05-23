# yaf-phpunit

simple code to show how to test controller in yaf with phpunit

### directory structure ###

    .
    ├── application # project code directory
    │   ├── Bootstrap.php
    │   ├── controllers
    │   │   ├── Base.php
    │   │   ├── Index.php
    │   │   └── User.php
    │   ├── library
    │   │   └── DB
    │   │       └── Manager.php
    │   └── models
    │       └── User.php
    ├── conf
    │   └── application.ini
    ├── index.php
    ├── README.md
    └── test      # test code directory
         ├── controller    # test code for controller
         │   ├── TestIndexController.php
         │   └── TestUserController.php
         ├── init.xml   # setup inital state for database
         ├── phpunit.xml # config for phpunit
         └── TestController.php

### remember ###

add `yaf.use_spl_autoload=1` to php.ini

### more information ###

for more information, refer to [here](http://cstdlib.com/jekyll/update/2015/05/23/yaf-phpunit/)

feel free to contact me at `ruochen.xu at gmail`
