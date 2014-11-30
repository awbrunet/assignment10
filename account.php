<?php

include "top.php";

?>

<article id="main">
	<form action="<?php print $phpSelf; ?>" method="POST">
	<?php
	
	require_once('../bin/myDatabase.php');

    $dbUserName = 'awbrunet_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName =  'AWBRUNET_UVM_Courses';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
	
	if(!empty($_SESSION['email']))
	{
		$email = $_SESSION['email'];
		//print $_SESSION ['email'];
		$query = "SELECT pmkUserId FROM tblUser WHERE fldEmail = '" .$email. "'";
    	$userArr = $thisDatabase->select($query);
    	foreach ($userArr as $result){
    	$userId = $result['pmkUserId'];}
    	//print $userId;
	}
	else{
		$email = "";
	}
	
    $query = 'SELECT fldLogStatus FROM tblUser WHERE fldLogStatus=1';
    $checkLogin = $thisDatabase->select($query);

	if(empty($email)){//If no one is logged in

		print "<p> <a href = 'login.php'>Login now to save restaurants...and your stomach!</a></p>";
	}
	else{//Display WHO is logged in
		$query = 'SELECT pmkUserId, fldEmail, fldAllergy FROM tblUser WHERE fldLogStatus=1 AND fldEmail = "' .$email. '"';
		$display = $thisDatabase->select($query);

		print "<p>Logged in as <b>";
		foreach ($display as $result){

			print $result['fldEmail'];
		}
		print "</b><br> Your account is: ";
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

		$query = "SELECT DISTINCT tblRestaurants.pmkRestId, (tblRestaurants.fldRestName) AS Name, (tblRestaurants.fldFoodType) AS Style, (tblRestaurants.fldMenuType) AS Accomodations, ";
        $query .= 'CONCAT(tblRestaurants.fldStreetAdd,", ",tblRestaurants.fldCity,", ",tblRestaurants.fldState,"  ",tblRestaurants.fldZip) AS Address, ';
        $query .= '(tblRestaurants.fldPhone) AS Phone, (tblRestaurants.fldURL) AS Website FROM tblRestaurants, tblSavedRestaurants ';
        $query .= 'WHERE tblRestaurants.pmkRestId = tblSavedRestaurants.fnkRestId AND tblSavedRestaurants.fnkUserId = ' .$userId;

        $results = $thisDatabase->select($query);
		$numberRecords = count($results);

        if($numberRecords < 1){
        	print "<h2>You have saved " . $numberRecords . " restaurants.</h2>";
    	}
    	elseif($numberRecords > 1){
    		print "<h2>You have saved " . $numberRecords . " restaurants.</h2>";
    	}
    	else{
    		print "<h2>You have saved " . $numberRecords . " restaurant.</h2>";
    	}

        print "<table>";

        $firstTime = true;
        
        /* since it is associative array display the field names */
        foreach ($results as $row) {
        	$i=0;
            if ($firstTime) {
                print "<thead><tr>";
                $keys = array_keys(array_slice($row,1));
                foreach ($keys as $key) {
                    if (!is_int($key)) {
                        print "<th>" . $key . "</th>";
                    }
                }
                print "</tr>";
                $firstTime = false;
            }
            
            /* display the data, the array is both associative and index so we are
             *  skipping the index otherwise records are doubled up */
            print "<tr>";
            $currId = $row[0];
            foreach (array_slice($row,1) as $field => $value) {
                if (!is_int($field)) {
                    $i++; 
                    if($i>5){
                        print "<td><a href='" . $value . "' target='blank'>Site</a></td>";
                    }
                    else{
                    	print "<td>" . $value . "</td>";
                    }

                }
            }
            print "<td><input type='checkbox' name='list[]' value='" .$currId. "'/>Unsave</td>";//add chkbox
            print "</tr>";
        }
        print "</table>";

    if (isset($_POST["btnDel"]))
	{
		if(empty($_POST['list'])){
			print "No restaurants selected! Please select any restaurants you want to unsave.";
		}
		else{		
			for($n=0; $n < count($_POST['list']); $n++){
				$query = 'DELETE FROM tblSavedRestaurants WHERE fnkRestId = ' .$_POST["list"][$n]. ' AND fnkUserId = ' .$userId;
				$data = array($userId);
				$data = array($_POST["list"][$n]);
				$results = $thisDatabase->delete($query,$data);
				print '<meta http-equiv="refresh" content="1">';
			}
		}
	}

	
		if(!empty($email)){	
			print "<br><br>";
			print '<input type="submit" id="btnDel" name="btnDel" value="Remove selected restaurants" tabindex="510" class="button">';
			print '<input type="submit" id="btnClear" name="btnClear" value="Clear my saved restaurants" tabindex="510" class="button">';
		}		
	?>
	</form>
</article>

<?php include "footer.php"; ?>

</body>
</html>