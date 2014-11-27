<?php

include "top.php";

?>

<article id="main">
	<?php
	require_once('../bin/myDatabase.php');

    $dbUserName = 'awbrunet_reader';
    $whichPass = "r"; //flag for which one to use.
    $dbName =  'AWBRUNET_UVM_Courses';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

    $query = 'SELECT fldLogStatus FROM tblUser WHERE fldLogStatus=1';
    $checkLogin = $thisDatabase->select($query);


	if(empty($checkLogin)){
		print "<p> <a href = 'login.php'>Login</a></p>";
	}
	else{
		$query = 'SELECT fldEmail FROM tblUser WHERE fldLogStatus=1';
		$email = $thisDatabase->select($query);

		print "<p>Logged in as ";
		foreach ($email as $result){

			print $result['fldEmail'];
		}
	}

	?>
</article>

<?php include "footer.php"; ?>

</body>
</html>