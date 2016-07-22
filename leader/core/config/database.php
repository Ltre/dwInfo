<?php

/**
 * 数据库连接配置
 */

if (DEV_MODEL) {

	class AiDBConfiguration{
		//数据库类型【暂未实现其它库】
		static $dbtype = 'mysql5';
		//数据库主机名或地址
		static $host = '127.0.0.1';
		//端口
		static $port = 3306;
		//数据库名
		static $db = 'duowaninfo';
		//用户
		static $user = 'root';
		//密码
		static $pwd = '';
		//表前缀
		static $table_prefix = 'dwi_';
	}	

} else {

	class AiDBConfiguration{
		//数据库类型【暂未实现其它库】
		static $dbtype = 'mysql5';
		//数据库主机名或地址
		static $host = 'sqld.duapp.com';
		//端口
		static $port = 4050;
		//数据库名
		static $db = 'dTIwxfQOtsFegyoxsCJh';
		//用户
		static $user = 'gurzoQmUf010iyL24l72n3jB';
		//密码
		static $pwd = 'CwG4gTqSRAN5yZVzkY2XnomG3DcmKBsf';
		//表前缀
		static $table_prefix = 'dwi_';
	}

}


