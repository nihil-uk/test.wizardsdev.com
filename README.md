It's a test work for [wizardsdev.com](https://wizardsdev.com/) - Parser [finance.yahoo.com](https://finance.yahoo.com/)

INSTALLATION
------------

### Clone

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Clone project and install vendor package:

~~~
composer install --profile --prefer-dist --optimize-autoloader --no-plugins
~~~

### Migration

Run command for start migration:

~~~
php yii migrate/up --interactive=true
~~~

### Prepare folders

Create uploads dir
~~~
mkdir -p web/uploads
~~~

Set dir mod

~~~
chmod -R 0777 web/assets/ runtime/ web/uploads/
~~~

### Configure

Configure params file `config/params.php`. Url with RSS and sleep time

```php
return [
   'parser' => [
      'url' => 'https://finance.yahoo.com/rss/',
      'sleep' => [10, 15],
   ]
];
```
### Run parser in manual

~~~
php yii parser
~~~

### Crontab

When you want to run parse script at every 2nd hour, you can configure crontab

Open CronTab config file...

~~~
sudo crontab -e
~~~

... and write here

~~~
0 */2 * * * php /path/to/project/yii parser
~~~

**NOTES:**
Change `/path/to/project` for you installed path of project