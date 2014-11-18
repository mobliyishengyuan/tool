### 1 注意变量作用域
begin end中定义的local variable在外面访问不了
### 2 table不要直接复制
lua table赋值是直接传指针，修改可能产生各种未知问题
