<?php 
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)									@
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
# @*************************************************************************@
header ("Access-Control-Allow-Origin: *");
ini_set('display_errors', 0);
session_start();
define("PROJ_RP", dirname(dirname(__FILE__)));

$cl_tables = array('cl_bookmarks','cl_chats','cl_configs','cl_connections','cl_hashtags','cl_messages','cl_notifications','cl_posts','cl_publications','cl_publikes','cl_pubmedia','cl_sessions','cl_users', 'cl_profile_reports', 'cl_blocks', 'cl_affiliate_payouts', 'cl_ads', 'cl_wallet_history');
$page      = ((empty($_GET['p'])) ? 'terms' : strval($_GET['p']));

if (isset($_SESSION['init']) != true || is_array($_SESSION['init']) != true) {
	$_SESSION['init'] = array();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Installation - Colibri Social Media Platform</title>
	<script><?php require_once('content/js/jquery.min.js'); ?></script>
	<script><?php require_once('content/js/popper.min.js'); ?></script>
	<script><?php require_once('content/js/bootstrap.min.js'); ?></script>
	<script><?php require_once('content/js/install.master.script.js'); ?></script>

	<style><?php require_once('content/css/bootstrap.min.css'); ?></style>
	<style><?php require_once('content/css/install.master.style.css'); ?></style>

	<!-- 
		Please note that all the contents of the installation folder are only needed to install the script, and will be deleted after the script is installed.
		Due to the fact that this script has not yet defined URLs, static files are connected using php
	-->
</head>
<body>
	<div class="container">
		<div class="main-cont">
			<div class="row">
				<div class="col-sm-8 offset-sm-2">
					<div class="main-cont-header">
						<h1>
							Welcome to the ColibriSM installer!
						</h1>
						<p>
							Installing <b>ColibriSM</b> quick easy. In just a few steps, you get a modern social media sharing platform
						</p>
					</div>

					<?php if ($page == 'requirements'): ?>
						<?php require_once('content/temps/requirements.phtml'); ?>
					<?php elseif($page == 'installation' && isset($_SESSION['init']['reqs']) && empty($_SESSION['init']['reqs'])): ?>
						<?php require_once('content/temps/installation.phtml'); ?>
					<?php else: ?>
						<?php require_once('content/temps/terms.phtml'); ?>
					<?php endif; ?>
					
					<div class="main-cont-footer">
						<span>
							&copy; ColibriSM <?php echo date('Y'); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>