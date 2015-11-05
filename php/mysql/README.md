mysqli不同初始化方式的差异
=====
		<?php
		$db_1 = new mysqli('127.0.0.1', 'root', 'root', 'blazer_db', 3307);
		$db_1->query('select * from test');

		var_dump($db_1->connect_errno, $db_1->errno);
		/**
		 * int(2002)
		 * NULL
		 */

		$db_2 = mysqli_init();
		$db_2->real_connect('127.0.0.1', 'root', 'root', 'blazer_db', 3307);
		$db_1->query('select * from test');

		var_dump($db_2->connect_errno, $db_2->errno);

		/**
		 * int(2002)
		 * int(2002)
		 */
