<?php
session_start();
require_once('functions.php');
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>
		<div id="body_wrap">
			<h1 class="page_heading">Welcome to Tasky</h1>
			<h2>Tasky is a <b>Task Managment App</b> based on <a href="https://tent.io">Tent</a></h2>
			<?php
			if (!isset($_SESSION['entity']) OR isset($_GET['error'])) {
				if (isset($_GET['error'])) {
					echo "<h2 class='error'>Error: ".urldecode($_GET['error'])."</h2>";
				}?>
				<form align="center" action="auth.php" method="get"> 
					<p>Entity: <input type="url" name="entity" placeholder="https://cacauu.tent.is" /> 
					<input type="submit" /></p> 
				</form>
			<?}
			else { ?>
				<h2>Create a new task:</h2> <!-- This should be collapsible somehow, takes way to much space in this way -->
				<form align="center" action="send_post.php" method="post">
					<p><b>Title:</b> <input type="text" name="title" placeholder="Your awesome task" /></p>
					<p>Priority: <select name="priority" size="1"><option value="0">Low</option><option SELECTED value="1">Average</option><option value="2">High</option><option value="3">Urgent</option></select></p>
					<p><b>List:</b> <select name="list"><option>To Do</option></select></legend></p>
					<p>Notes:</p>
					<p><textarea name="notes" class="message"></textarea> </p>
					<p><input type="submit"></p>
				</form>
				<h2>Your Tasks:</h2>
					<?php
					$entity = $_SESSION['entity'];
					$entity_sub = substr($entity, 7, strlen($entity)-8);
					$nonce = uniqid('Tasky_', true);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http://cacauu.de/tasky/task/v0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http://cacauu.de/tasky/task/v0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'" Content-Type: application/vnd.tent.post.v0+json; type="https://tent.io/types/status/v0#"')); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					var_export($posts['posts']);
					//This returns the raw (json_decoded) data from the Tent server. That is where you could add your style and work with them.
					?>
				<h3><a href="logout.php">Logout</a></h3>
			<?}
		?>
		</div>

		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
		</footer>
	</body>
</html>