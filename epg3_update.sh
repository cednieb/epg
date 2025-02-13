#!/bin/sh

# crontab -e          0 1,12,19 * * * sh /var/www/html/epg3_update.sh

cd /var/www/html/
rm xmltv_fr.zip
rm xmltv_fr.xml
curl  -s -L  -O https://xmltvfr.fr/xmltv/xmltv_fr.zip && unzip xmltv_fr.zip && php /var/www/html/epg3.php