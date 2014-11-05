#!/bin/bash

NGINX_HOME=/home/work/webserver
NGINX=$NGINX_HOME/sbin/nginx


/etc/rc.d/init.d/functions

RETVAL=0

start() {
    echo -n $"Starting nginx: "
    
    nohup $NGINX >/dev/null 2>&1 &

    RETVAL=$?
    if [ $RETVAL -eq 0 ]
    then
        echo "OK"
    else
        echo "Failed!"
    fi
    return $RETVAL
}

stop() {
    echo -n $"Stopping nginx: "
    
    $NGINX -s stop
    echo  $"Stop OK,please check it youself ";
}

restart() {
    stop
    sleep 2
    start
}


case "$1" in
start)
    start
    ;;

stop)
    stop
    ;;

restart)
    restart
    ;;

reload)
    $NGINX -s reload
    echo  $"reload OK,please check it youself";
    ;;

chkconfig)
    $NGINX -t
    ;;

*)
echo "Usage: $0 {start|stop|restart|chkconfg|reload}"
echo $NGINX
exit 1
esac
