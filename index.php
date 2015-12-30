<?php
 	session_start();
	//echo "start.<br>";
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo"posted";
   	if ($_POST['action'] == "login") {
   		include 'config.php';
			include 'opendb.php';
			//echo "query start<br>";
			//$query_session = "SELECT login_session_id FROM users where user_number='".$_POST['username']."' AND password='".md5("vote2015".$_POST['userpassword'])."';";
			
			//echo md5("vote2015".$_POST['userpassword']);
			$query = "SELECT user_number, login_session_id, last_activity FROM users where user_number='".$_POST['username']."' AND password='".md5("vote2015".$_POST['userpassword'])."';";
			//echo mysql_numrows(mysql_query($query,$conn));
			if (!mysql_query($query,$conn)) {
				echo mysql_errno($conn) . ": " . mysql_error($conn) . ".<br>";
			} elseif (mysql_numrows(mysql_query($query,$conn))=='1') {
				$result = mysql_query($query);				
				$row = mysql_fetch_row($result);
				//echo $row['0']."+".$row['1']."+".$row['2']."+".time()."<br>";
				if ($row['1'] == NULL) {
					//echo "Session is empty.<br>";
					//echo session_id()."<br>";
					$update_session_query ="UPDATE users SET last_activity=now(),login_session_id='".session_id()."' WHERE user_number = '".$_POST['username']."';";
					if (!mysql_query($update_session_query,$conn)) {
						echo mysql_errno($conn) . ": " . mysql_error($conn) . ".<br>";
					} else {
						$_SESSION['username'] = $row['0'];
						//echo "session updated.<br>";
						header("Location: vote.php");						
					}				
				} elseif (session_id() == $row['1']) {
					$update_session_query ="UPDATE users SET last_activity=now() WHERE user_number = '".$_POST['username']."';";
					if (!mysql_query($update_session_query,$conn)) {
						echo mysql_errno($conn) . ": " . mysql_error($conn) . ".<br>";
					} else {
						//echo "update time stamp.<br>";
						header("Location: vote.php");					
					}
				} elseif ((session_id() <> $row['1']) AND (time() - strtotime($row['2']) > 1800)) {
					$update_session_query ="UPDATE users SET last_activity=now(),login_session_id='".session_id()."' WHERE user_number = '".$_POST['username']."';";
					if (!mysql_query($update_session_query,$conn)) {
						echo mysql_errno($conn) . ": " . mysql_error($conn) . ".<br>";
					} else {
						//echo "update time stamp.<br>";
						//echo "session expired.<BR>";
						header("Location: vote.php");					
					}
				} else {
					//echo (time() - $row['2']);
					echo "We detected your ID had log in another station.<br>";	
					echo "If you believe no one else is using your ID,<br>";
					echo "please proceed to counter to reset your session personally.";
				}
				//echo "Welcome ".$_SESSION['username'];
				//header("Location: vote.php");
				//echo "User account created successfully.";
			} else {
				echo "User name and password not matching.<br>";
			};
			//echo "Unknown error.<br>";
			include 'closedb.php';
   	}
	}
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Voting System</title>
 	</head>
 	<body>
 		<p>Please log in.</p>
 		<form action="index.php" method="post">
 			<p>User Name <input type="text" name="username"></p>
 			<p>Password <input type="password" name="userpassword"></p>
 			<input type="hidden" name="action" value="login">
 			<input type="submit" value="Login">
 		</form>
 	</body>
</html>