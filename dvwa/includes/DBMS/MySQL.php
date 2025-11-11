<?php

/*

此文件包含所有用于设置初始MySQL数据库的代码。(setup.php)

*/

if( !defined( 'DVWA_WEB_PAGE_TO_ROOT' ) ) {
	define( 'DVWA_WEB_PAGE_TO_ROOT', '../../../' );
}

/*
 * 尝试连接到 MySQL 数据库服务器。
 * 如果连接失败，将显示错误信息并重载页面。
 */
if( !@($GLOBALS["___mysqli_ston"] = mysqli_connect( $_DVWA[ 'db_server' ],  $_DVWA[ 'db_user' ],  $_DVWA[ 'db_password' ], "", $_DVWA[ 'db_port' ] )) ) {
	dvwaMessagePush( "无法连接到数据库服务。<br />请检查配置文件。<br />数据库错误 #" . mysqli_connect_errno() . ": " . mysqli_connect_error() . "." );
	if ($_DVWA[ 'db_user' ] == "root") {
		dvwaMessagePush( '您的数据库用户是root，如果您使用的是MariaDB，这将不起作用，请阅读README.md文件。' );
	}
	dvwaPageReload();
}

// 删除已存在的数据库（如果存在）
$drop_db = "DROP DATABASE IF EXISTS {$_DVWA[ 'db_database' ]};";
if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $drop_db ) ) {
	dvwaMessagePush( "无法删除现有数据库<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}

// 创建新的数据库
$create_db = "CREATE DATABASE {$_DVWA[ 'db_database' ]};";
if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $create_db ) ) {
	dvwaMessagePush( "无法创建数据库<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}
dvwaMessagePush( "数据库已创建。" );


// 使用新创建的数据库
if( !@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $_DVWA[ 'db_database' ])) ) {
	dvwaMessagePush( '无法连接到数据库。' );
	dvwaPageReload();
}

// 创建用户表 'users'
$create_tb = "CREATE TABLE users (user_id int(6),first_name varchar(15),last_name varchar(15), user varchar(15), password varchar(32),avatar varchar(70), last_login TIMESTAMP, failed_login INT(3), PRIMARY KEY (user_id));";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb ) ) {
	dvwaMessagePush( "无法创建表<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}
dvwaMessagePush( "'users' 表已创建。" );


// 向 users 表中插入初始数据
$base_dir= str_replace ("setup.php", "", $_SERVER['SCRIPT_NAME']);
$avatarUrl  = $base_dir . 'hackable/users/';

$insert = "INSERT INTO users VALUES
	('1','admin','admin','admin',MD5('password'),'{$avatarUrl}admin.jpg', NOW(), '0'),
	('2','Gordon','Brown','gordonb',MD5('abc123'),'{$avatarUrl}gordonb.jpg', NOW(), '0'),
	('3','Hack','Me','1337',MD5('charley'),'{$avatarUrl}1337.jpg', NOW(), '0'),
	('4','Pablo','Picasso','pablo',MD5('letmein'),'{$avatarUrl}pablo.jpg', NOW(), '0'),
	('5','Bob','Smith','smithy',MD5('password'),'{$avatarUrl}smithy.jpg', NOW(), '0');";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
	dvwaMessagePush( "无法向 'users' 表插入数据<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}
dvwaMessagePush( "数据已插入到 'users' 表中。" );


// 创建留言簿表 'guestbook'
$create_tb_guestbook = "CREATE TABLE guestbook (comment_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, comment varchar(300), name varchar(100), PRIMARY KEY (comment_id));";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_guestbook ) ) {
	dvwaMessagePush( "无法创建表<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}
dvwaMessagePush( "'guestbook' 表已创建。" );


// 向 guestbook 表中插入数据
$insert = "INSERT INTO guestbook VALUES ('1','这是一个测试评论。','test');";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
	dvwaMessagePush( "无法向 'guestbook' 表插入数据<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	dvwaPageReload();
}
dvwaMessagePush( "数据已插入到 'guestbook' 表中。" );




// 复制 .bak 文件以制造有趣的目录列表漏洞
$conf = DVWA_WEB_PAGE_TO_ROOT . 'config/config.inc.php';
$bakconf = DVWA_WEB_PAGE_TO_ROOT . 'config/config.inc.php.bak';
if (file_exists($conf)) {
	// 如果失败了也没关系。抑制错误。
	@copy($conf, $bakconf);
}

dvwaMessagePush( "备份文件 /config/config.inc.php.bak 已自动创建" );

// 完成
dvwaMessagePush( "<em>设置成功</em>!" );

if( !dvwaIsLoggedIn())
	dvwaMessagePush( "请<a href='login.php'>登录</a>。<script>setTimeout(function(){window.location.href='login.php'},5000);</script>" );
dvwaPageReload();

?>
