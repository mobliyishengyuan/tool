swoole重新定义PHP
swoole简介
======
{}
优势
======
{}
示例
======
### Server.php
    <?php
    class HelloWorld_Server {
        private $serv;
    
        public function __construct() {
            $this->serv = new swoole_server('0.0.0.0', 8000); // 监听全部地址，端口为8000
            $this->serv->set(array(
                'worker_num' => 1, // 设置worker进程数目，类似nginx worker_process
                'daemonize' => false, // 是否作为守护进程运行
                'max_request' => 10,  // 最大请求数目，超过此数目，则reload worker process
            ));    
    
            $this->serv->on('Start', array($this, 'onStart')); // 绑定server start事件
            $this->serv->on('WorkerStart', array($this, 'onWorkerStart')); // 绑定worker start事件
            $this->serv->on('Connect', array($this, 'onConnect')); // 绑定client connect事件
            $this->serv->on('Receive', array($this, 'onReceive')); // 绑定receive client data事件
            $this->serv->on('Close', array($this, 'onClose')); // 绑定client close事件
    
            $this->serv->start(); // 启动server
        }   
    
        public function onStart($serv) {
            echo "Start\n";
        }   
    
        public function onWorkerStart($serv, $worker_id) {
            print("include\n");
            include(__DIR__ . '/handle.php');
        }   
    
        public function onConnect($serv, $fd, $from_id) {
            handle_connect($serv, $fd, $from_id);
        }   
    
        public function onReceive($serv, $fd, $from_id, $data) {
            handle_receive($serv, $fd, $from_id, $data);
        }   
    
        public function onClose($serv, $fd, $from_id) {
            handle_close($serv, $fd, $from_id);
        }   
    }
    
    $server = new HelloWorld_Server();

### handle.php
    <?php
    function handle_connect($serv, $fd, $from_id) {
        printf("Client Connect, fd[%s] from_id[%s]\n", $fd, $from_id);
    }
    
    function handle_receive($serv, $fd, $from_id, $data) {
        printf("Recevie Data, fd[%s] from_id[%s] data_size[%s] data[%s]\n", $fd, $from_id, strlen($data), $data);
        $serv->send($fd, "Hello World!\n"); // 向client发送数据
    }
    
    function handle_close($serv, $fd, $from_id) {
        printf("Close Connection, fd[%s] from_id[%s]\n", $fd, $from_id);
    }
### php server.php，日志如下：
        
    Start
    include
### 模拟client
    telnet 127.0.0.1 8000
    Trying 127.0.0.1...
    Connected to 127.0.0.1.
    Escape character is '^]'.
    h
    Hello World!
client send 10次后，worker process重新加载，重新include handle.php
### server log
    Client Connect, fd[1] from_id[0]
    Recevie Data, fd[1] from_id[0] data_size[3] data[h
    ]
案例
======
{}
问题
======
{}
