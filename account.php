<?php

include "top.php";

?>

<article id="main">
	<?php
	
	require_once('../bin/myDatabase.php');

    $dbUserName = 'awbrunet_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName =  'AWBRUNET_UVM_Courses';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
	
	if(!empty($_SESSION['email']))
	{
		$sessionEmail = $_SESSION['email'];
		print $_SESSION ['email'];
		$query = "SELECT pmkUserId FROM tblUser WHERE fldEmail = '" .$sessionEmail. "'";
    	$userArr = $thisDatabase->select($query);
    	foreach ($userArr as $result){
    	$userId = $result['pmkUserId'];}
	}
	
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


	if (isset($_POST["btnClear"])){
		$query = 'DELETE FROM tblSavedRestaurants WHERE fnkUserId = ' .$userId;
		$data = array($userId);
		$results = $thisDatabase->delete($query,$data);

	}	

	?>
	<form action="" method="POST">
		<input type="submit" id="btnClear" name="btnClear" value="Clear my saved restaurants" tabindex="510" class="button">
	</form>
</article>

<?php include "footer.php"; ?>

</body>
</html>