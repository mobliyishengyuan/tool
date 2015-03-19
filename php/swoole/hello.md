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
            $this->serv = new swoole_server('0.0.0.0', 8000);
            $this->serv->set(array(
                'worker_num' => 1,
                'daemonize' => false,
                'max_request' => 10, 
            ));    
    
            $this->serv->on('Start', array($this, 'onStart'));
            $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
            $this->serv->on('Connect', array($this, 'onConnect'));
            $this->serv->on('Receive', array($this, 'onReceive'));
            $this->serv->on('Close', array($this, 'onClose'));
    
            $this->serv->start();
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
handle.php
    <?php
    function handle_connect($serv, $fd, $from_id) {
        printf("Client Connect, fd[%s] from_id[%s]\n", $fd, $from_id);
    }
    
    function handle_receive($serv, $fd, $from_id, $data) {
        printf("Recevie Data, fd[%s] from_id[%s] data_size[%s] data[%s]\n", $fd, $from_id, strlen($data), $data);
        $serv->send($fd, "Hello World!\n");
    }
    
    function handle_close($serv, $fd, $from_id) {
        printf("Close Connection, fd[%s] from_id[%s]\n", $fd, $from_id);
    }
案例
======
{}
问题
======
{}
