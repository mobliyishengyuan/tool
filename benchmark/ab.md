1 introduce
=====
ab的全称是ApacheBench，是Apache附带的一个小工具，专门用于HTTP Server的benchmark testing，可以同时模拟多个并发请求。
2 install
=====
need apache
2.1 install apr apr-util
-----
download url http://apr.apache.org/download.cgi<br/>
wget http://mirrors.cnnic.cn/apache//apr/apr-1.5.1.tar.gz<br/>
tar -zxvf apr-1.5.1.tar.gz<br/>
cd apr-1.5.1<br/>
./configure --prefix=/home/work/local/apr<br/>
make<br/>
make install<br/>
wget http://mirrors.cnnic.cn/apache//apr/apr-util-1.5.4.tar.gz<br/>
tar -xzvf apr-util-1.5.4.tar.gz<br/>
cd apr-util-1.5.4<br/>
./configure --prefix=/home/work/local/apr-util -with-apr=/home/work/local/apr/bin/apr-1-config<br/>
make<br/>
make install<br/>
2.2 install pcre
-----
donwload url http://sourceforge.net/projects/pcre/files/pcre/8.32/<br/>
wget http://sourceforge.net/projects/pcre/files/pcre/8.32/pcre-8.32.tar.gz/download<br/>
tar -xzvf pcre-8.32.tar.gz<br/>
cd pcre-8.32<br/>
./configure --prefix=/home/work/local/pcre<br/>
make<br/>
make install<br/>
2.3 install apaceh httpd
-----
download url http://httpd.apache.org/download.cgi#apache24<br/>
wget http://mirror.bit.edu.cn/apache//httpd/httpd-2.4.10.tar.gz<br/>
tar -xzvf httpd-2.4.10.tar.gz<br/>
cd httpd-2.4.10<br/>
./configure --with-apr=/home/work/local/apr/ --with-apr-util=/home/work/local/apr-util/ --with-pcre=/home/work/local/pcre/ --prefix=/home/work/local/apache2<br/>
make<br/>
make install<br/>
2.4 test
-----
home/work/local/apache2/bin/ab -c 10 -n 10 http://www.baidu.com/aa
3 script
=====
format : <br/>
  ./ab [options] [http://]hostname[:port]/path<br/>
