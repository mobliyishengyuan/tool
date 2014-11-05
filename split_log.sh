#! /bin/sh

LOG_DIR_PATH=/home/work/log
NGINX_PID=/home/work/var/nginx.pid
PHP_FPM_PID=/home/work/var/php-fpm.pid

YESTERDAY=$(date -d last-day +%Y%m%d)

find $LOG_DIR_PATH -name '*.log' -exec mv "{}" "{}".${YESTERDAY} \;

kill -USR1 `cat $NGINX_PID`
kill -USR1 `cat $PHP_FPM_PID`
