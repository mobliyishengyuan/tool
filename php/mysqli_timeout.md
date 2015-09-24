前言
=====
用PHP做业务，最长打交道的后端存储是mysql，php到mysql的网络交互情况严重影响服务稳定情况。如何设置connect、write、read阶段的timeout？
connect
=====
设置方法
------
Mysqli::options可以设置一些选项信息，其中包含connect timeout对应的设置项MYSQLI_OPT_CONNECT_TIMEOUT。
验证方法
------
主要使用的命令如下：
	
        tc qdisc add dev eth0 root netem delay 100ms ： 表示对端口廷时100ms
        tc qdisc del dev eth0 root netem delya 100ms ： 表示对端口廷时命令删除
        
其中eth0为你使用的网卡。更详细的见[link](http://blog.chinaunix.net/uid-11344913-id-3506954.html)。
write & read
=====
设置方法
------
Mysql的client本身是支持设置的，php的mysqli扩展没有将其暴露出来。网上有一种方法，直接进行设置，文章见[link](http://www.jb51.net/article/27016.htm)。经实验不可行，决定分析下。

Mysql client opt声明的地方如下：

        Mysql/include/mysql.h

        159 enum mysql_option
        160 {
        161   MYSQL_OPT_CONNECT_TIMEOUT, MYSQL_OPT_COMPRESS, MYSQL_OPT_NAMED_PIPE,
        162   MYSQL_INIT_COMMAND, MYSQL_READ_DEFAULT_FILE, MYSQL_READ_DEFAULT_GROUP,
        163   MYSQL_SET_CHARSET_DIR, MYSQL_SET_CHARSET_NAME, MYSQL_OPT_LOCAL_INFILE,
        164   MYSQL_OPT_PROTOCOL, MYSQL_SHARED_MEMORY_BASE_NAME, MYSQL_OPT_READ_TIMEOUT,
        165   MYSQL_OPT_WRITE_TIMEOUT, MYSQL_OPT_USE_RESULT,
        166   MYSQL_OPT_USE_REMOTE_CONNECTION, MYSQL_OPT_USE_EMBEDDED_CONNECTION,
        167   MYSQL_OPT_GUESS_CONNECTION, MYSQL_SET_CLIENT_IP, MYSQL_SECURE_AUTH,
        168   MYSQL_REPORT_DATA_TRUNCATION, MYSQL_OPT_RECONNECT,
        169   MYSQL_OPT_SSL_VERIFY_SERVER_CERT, MYSQL_PLUGIN_DIR, MYSQL_DEFAULT_AUTH,
        170   MYSQL_ENABLE_CLEARTEXT_PLUGIN
        171 };
申明了mysql option，其中MYSQL_OPT_CONNECT_TIMEOUT为0，MYSQL_OPT_READ_TIMEOUT 11，MYSQL_OPT_WRITE_TIMEOUT 12。

php mysqli扩展设置options的部分如下：

	    php/ext/mysqli/mysqli_api.c
	    
	    // mysqli::options函数定义
        1676 /* {{{ proto bool mysqli_options(object link, int flags, mixed values)
        1677    Set options */
        1678 PHP_FUNCTION(mysqli_options)
        ……
        // 读取设置的mysql option项的变量类型
        1702     expected_type = mysqli_options_get_option_zval_type(mysql_option);
        ……
        1715     switch (expected_type) {
        1716         case IS_STRING:
        1717             ret = mysql_options(mysql->mysql, mysql_option, Z_STRVAL_PP(mysql_value));
        1718             break;
        1719         case IS_LONG:
        1720             l_value = Z_LVAL_PP(mysql_value);
        1721             ret = mysql_options(mysql->mysql, mysql_option, (char *)&l_value);
        1722             break;
        // 如果不是string、long的话不进行设置，返回flase
        1723         default:
        1724             ret = 1;
        1725             break;
        1726     }
        1727 
        1728     RETURN_BOOL(!ret);
        
        
        // mysqli_options_get_option_zval_type函数定义
        1614 /* {{{ mysqli_options_get_option_zval_type */
        1615 static int mysqli_options_get_option_zval_type(int option)
        1616 {
        1617     switch (option) {
        ……
        // if MYSQL_OPT_READ_TIMEOUT define时下述options项才可用，才会返回IS_LONG，不然就会返回IS_NULL。
        // 其中包含MYSQL_OPT_READ_TIMEOUT、MYSQL_OPT_READ_TIMEOUT。
        // enum申明的是变量，ifdef false。
        1637 #ifdef MYSQL_OPT_READ_TIMEOUT
        1638         case MYSQL_OPT_READ_TIMEOUT:
        1639         case MYSQL_OPT_READ_TIMEOUT:
        1640         case MYSQL_OPT_GUESS_CONNECTION:
        1641         case MYSQL_OPT_USE_EMBEDDED_CONNECTION:
        1642         case MYSQL_OPT_USE_REMOTE_CONNECTION:
        1643         case MYSQL_SECURE_AUTH:
        1644 #endif /* MySQL 4.1.1 */
        ……
        1654             return IS_LONG;
        ……
        1669         default:
        1670             return IS_NULL;
        ……
由此可以知道，原因是mysqli扩展中对于这些非法options不会受理。想要进行修复的话，需要注视掉1637和1644行，然后重新编译mysqli.so。

至于为什么网上流程的文章实验的可以，怀疑其对应版本的mysql，对于MYSQL_OPT_READ_TIMEOUT的申明使用的是define而不是enum。
验证方法
------
        <?php
        if (!defined('MYSQL_OPT_READ_TIMEOUT')) { 
        define('MYSQL_OPT_READ_TIMEOUT', 11); 
        } 
        if (!defined('MYSQL_OPT_WRITE_TIMEOUT')) { 
        define('MYSQL_OPT_WRITE_TIMEOUT', 12); 
        } 
        
        //设置超时 
        $mysqli = mysqli_init(); 
        $mysqli->options(MYSQL_OPT_READ_TIMEOUT, 3); 
        $mysqli->options(MYSQL_OPT_WRITE_TIMEOUT, 1); 
        
        //连接数据库 
        $mysqli->real_connect("localhost", "root", "root", "blazer_db"); 
        if (mysqli_connect_errno()) { 
        printf("Connect failed: %s\n", mysqli_connect_error()); 
        exit(); 
        } 
        //执行查询 sleep 1秒不超时 
        printf("Host information: %s\n", $mysqli->host_info); 
        if (!($res=$mysqli->query('select sleep(1)'))) { 
        echo "query1 error: ". $mysqli->error ."\n"; 
        } else { 
        echo "Query1: query success\n"; 
        } 
        
        //执行查询 sleep 9秒会超时 
        if (!($res=$mysqli->query('select sleep(9)'))) { 
        echo "query2 error: ". $mysqli->error ."\n"; 
        } else { 
        echo "Query2: query success\n"; 
        } 
        
        $mysqli->close(); 
        echo "close mysql connection\n"; 

相关的收获
=====
1. 了解mysqli option相关流程。
2. enum申明的是变量，ifdef false。之前这个是不明确。
3. 网络状态模拟工具的使用。
todo
=====
1. mysql client connect、write、read流程。
2. 如何设置ms级超时。
