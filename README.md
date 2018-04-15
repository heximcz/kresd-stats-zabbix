# Knot resolver - statistics for ZABBIX

## Prerequisites

PHP >= 7.1.3 with CURL

## Recommended
* Do not forget set up "sendmail_path" value in php.ini ;-)
* for example ```sendmail_path = "/usr/sbin/sendmail -t -i -f no-reply@your-domain.com"```

## How to install

```sh
$ cd /opt/
$ git clone https://github.com/heximcz/kresd-stats-zabbix
$ cd /opt/kresd-stats-zabbix/
$ composer install --no-dev --optimize-autoloader
$ cp ./config.default.yml ./config.yml
```
 -  **!! Do not forget to configure the config.yml !!**

## Print Usage

```php ./kresd-stats.php```

```php ./kresd-stats.php stats:prepare -h```

```php ./kresd-stats.php stats:get -h```

- override config.yml

```php ./kresd-stats.php stats:prepare -i <ip address> -p <port>```

```php ./kresd-stats.php stats:get <param> -i <ip address> -p <port>```

## Use crontab for prepare data

add this lines to your /etc/crontab:

```
*  *    * * *   root    /usr/local/sbin/php /opt/kresd-stats-zabbix/kresd-stats.php stats:prepare
*  *    * * *   root    sleep 30 && /usr/local/sbin/php /opt/kresd-stats-zabbix/kresd-stats.php stats:prepare
```
- multiple resolver servers
```
*  *    * * *   root    /usr/local/sbin/php /opt/kresd-stats-zabbix/kresd-stats.php stats:prepare -i <ip address> -p <port>
*  *    * * *   root    sleep 30 && /usr/local/sbin/php /opt/kresd-stats-zabbix/kresd-stats.php stats:prepare  -i <ip address> -p <port>
# repeat with different ip address
...
```

## Setting for Zabbix agent and template

[Zabbix template and UserParams](https://github.com/heximcz/kresd-stats-zabbix/tree/master/zabbix)

## License

MIT

