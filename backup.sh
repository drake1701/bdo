#!/bin/bash
tar -czvf /var/www/bdo.drogers.net/var/bdo-$(date +%Y%m%d).sqlite.tgz /var/www/bdo.drogers.net/bdo.sqlite
cp /var/www/bdo.drogers.net/var/bdo-$(date +%Y%m%d).sqlite.tgz /var/www/bdo.drogers.net/var/bdo-$(date +%Y%m).sqlite.tgz
d=$(date -I -d "$d - 10 day")
rm /var/www/bdo.drogers.net/var/bdo-$(date -d "$d" +%Y%m%d).sqlite.tgz
