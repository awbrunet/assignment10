<?php

include "top.php";

?>

<article id="main">
	<?php
	$sessionEmail = $_SESSION['email'];
	if(!empty($_SESSION['email'])){print $_SESSION ['email'];}
	require_once('../bin/myDatabase.php');

    $dbUserName = 'awbrunet_reader';
    $whichPass = "r"; //flag for which one to use.
    $dbName =  'AWBRUNET_UVM_Courses';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

    $query = 'SELECT fldLogStatus FROM tblUser WHERE fldLogStatus=1';
    $checkLogin = $thisDatabase->select($query);


	if(empty($checkLogin)){//If no one is logged in

		print "<p> <a href = 'login.php'>Login</a></p>";
	}
	else{//Display WHO is logged in
		$query = 'SELECT pmkUserId, fldEmail, fldAllergy FROM tblUser WHERE fldLogStatus=1 AND fldEmail = "' .$sessionEmail. '"';
		$display = $thisDatabase->select($query);

		print "<p>Logged in as ";
		foreach ($display as $result){

			print $result['fldEmail'];
		}
		print "<br>";
		foreach ($display as $result){

			print $result['fldAllergy'];
		}
		print "<p><a href='logout.php'>Log out?</a></p>";
	}

	?>
</article>

<?php include "footer.php"; ?>

</body>
</html>