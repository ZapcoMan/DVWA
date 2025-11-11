<?php

/**
 * 定义Web根目录相对路径常量
 */
define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );

/**
 * 引入DVWA页面初始化文件
 */
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

/**
 * 启动DVWA页面，检查用户认证状态和PHPIDS防护
 * @param array $requirements 页面启动所需条件数组，包括'authenticated'(已认证)和'phpids'(入侵检测系统)
 */
dvwaPageStartup( array( 'authenticated', 'phpids' ) );

/**
 * 创建新的页面对象
 * @return array 返回包含页面基本信息的数组
 */
$page = dvwaPageNewGrab();

/**
 * 设置页面标题、ID和按钮配置
 */
$page[ 'title' ]   = 'Vulnerability: Brute Force' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'brute';
$page[ 'help_button' ]   = 'brute';
$page[ 'source_button' ] = 'brute';

/**
 * 建立数据库连接
 */
dvwaDatabaseConnect();

/**
 * 根据安全级别设置请求方法和漏洞文件路径
 */
$method            = 'GET';
$vulnerabilityFile = '';

// 根据Cookie中的安全级别选择对应的漏洞文件
switch( $_COOKIE[ 'security' ] ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		$method = 'POST';
		break;
}

/**
 * 引入对应安全级别的漏洞实现文件
 */
require_once DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/brute/source/{$vulnerabilityFile}";

/**
 * 构建页面主体内容，包括登录表单和相关链接
 */
$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>漏洞: 爆破(Brute Force)</h1>

	<div class=\"vulnerable_code_area\">
		<h2>登陆</h2>

		<form action=\"#\" method=\"{$method}\">
			用户名:<br />
			<input type=\"text\" name=\"username\"><br />
			密码:<br />
			<input type=\"password\" AUTOCOMPLETE=\"off\" name=\"password\"><br />
			<br />
			<input type=\"submit\" value=\"点击登录\" name=\"Login\">\n";

// 高级和不可能级别添加CSRF令牌字段
if( $vulnerabilityFile == 'high.php' || $vulnerabilityFile == 'impossible.php' )
	$page[ 'body' ] .= "			" . tokenField();

$page[ 'body' ] .= "
		</form>
		{$html}
	</div>

	<h2>更多参考信息</h2>
	<ul>
		<li>" . dvwaExternalLinkUrlGet( 'https://owasp.org/www-community/attacks/Brute_force_attack' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'http://www.symantec.com/connect/articles/password-crackers-ensuring-security-your-password' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'http://www.sillychicken.co.nz/Security/how-to-brute-force-http-forms-in-windows.html' ) . "</li>
	</ul>
</div>\n";

/**
 * 输出完整的HTML页面
 * @param array $page 包含页面所有信息的数组，包括标题、内容等
 */
dvwaHtmlEcho( $page );

?>
