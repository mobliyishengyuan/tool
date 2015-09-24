前言
=====
    用PHP做业务，最长打交道的后端存储是mysql，php到mysql的网络交互情况严重影响服务稳定情况。如何设置connect、write、read阶段的timeout？
connect
=====
    Mysqli::options可以设置一些选项信息，其中包含connect timeout对应的设置项MYSQLI_OPT_CONNECT_TIMEOUT。
    如何进行验证？详细介绍见[link](http://blog.chinaunix.net/uid-11344913-id-3506954.html)。
    主要使用的命令如下：
	
        tc qdisc add dev eth0 root netem delay 100ms ： 表示对端口廷时100ms
        tc qdisc del dev eth0 root netem delya 100ms ： 表示对端口廷时命令删除
        
	其中eth0为你使用的网卡。
write & read
=====
    Mysql的client本身是支持设置的，php的mysqli扩展没有将其暴露出来。网上有一种方法，直接进行设置。文章见[link](http://www.jb51.net/article/27016.htm)，经实验不可行，决定分析下。
    Mysql client opt声明的地方
	
    Mysql/include/mysql.h
