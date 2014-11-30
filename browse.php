<?php

include "top.php";

?>

<article id="main">

<form action="<?php print $phpSelf; ?>" method="POST">
<?php

if(!empty($_SESSION['email']))
{
	$email = $_SESSION['email'];
}
else{
	$email = "";
}

$messageA = '<h2>You have saved a restaurant through myGlutenFree Burlington!</h2>';
$messageC = "<p><a href='https://awbrunet.w3.uvm.edu/cs148/assignment10/index.php'>Eat Safe!</a></p>";

$to = $email; // the person who filled out the form
$cc = "";
$bcc = "";
$from = "myGlutenFree Burlington <noreply@uvm.edu>";

// subject of mail should make sense to your form
$todaysDate = strftime("%x");


		require_once('../bin/myDatabase.php');

        $dbUserName = 'awbrunet_writer';
        $whichPass = "w"; //flag for which one to use.
        $dbName =  'AWBRUNET_UVM_Courses';

        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

        $query = "SELECT pmkUserId FROM tblUser WHERE fldEmail = '" .$email. "'";
        $userArr = $thisDatabase->select($query);
        foreach ($userArr as $result){
            $userId = $result['pmkUserId'];}
        
        $query = 'SELECT pmkRestId, (fldRestName) AS Name, (fldFoodType) AS Style, (fldMenuType) AS Accomodations, ';
        $query .= 'CONCAT(fldStreetAdd,", ",fldCity,", ",fldState,"  ",fldZip) AS Address, (fldPhone) AS Phone, (fldURL) AS Website FROM tblRestaurants';

        $results = $thisDatabase->select($query);


        $numberRecords = count($results);

        print "<h3>myGlutenFree Burlington has " . $numberRecords . " restaurants in our records!</h3>";

        //print "<table>";

        $firstTime = true;

        /* since it is associative array display the field names */
        foreach ($results as $row) {
            /*if ($firstTime) {
                print "<thead><tr>";
                $keys = array_keys(array_slice($row,1));
                foreach ($keys as $key) {
                    if (!is_int($key)) {
                        print "<th>" . $key . "</th>";
                    }
                }
                print "</tr>";
                $firstTime = false;
            }*/
            
            /* display the data, the array is both associative and index so we are
             *  skipping the index otherwise records are doubled up */
            
            $currId = $row[0];
            $currName = $row[1];

            print "<h3>" .$row[1]. "</h3>";
            print "This " .$row[2]. " restaurant offers: " .$row[3]. "<br>";
            print $row[4]. "<br>";
            print $row[5]. " <a href='" .$row[6]. "' target='blank'>Site</a> ";
            print "<br><input type='checkbox' name='list[]' value='" .$currId. "'/>Save this restaurant?";

            /*
            $i = 0;
            foreach (array_slice($row,1) as $field => $value) {
                if (!is_int($field)) {
                	$data[] = $value;
                	
                    if($i==4){
                        print $value ." | ";
                    }
                    elseif($i==5){
                        print $value. "<br>"; 
                    }
                	elseif($i==6){
                		print "<a href=" . $value . ">Site</a><br>";
                	}
                	else{
                    	print $value . "<br>";}
                    //print $value. " ";
                }
                $i++;
                
            }*/
            //print "<br>";
            //print "<td>" .$i. "</td>";
            //if(!empty($email)){
            //print "<input type='checkbox' name='list[]' value='" .$currId. "'/>Save this restaurant?";
            //}
            
            //print "<pre>";
            //print_r($data);
            //print "</pre>";
            
            
            $data="";
        }
        //print "</table>";

        $list = $_POST['list'];

if (isset($_POST["btnSubmit"]))
{
	if(empty($_POST['list'])){
		print "No restaurants saved! Please select any restaurants you want to save for later, then click save.";
	}
	else{		
		for($n=0; $n < count($_POST['list']); $n++){

			$query = 'SELECT fnkRestId FROM tblSavedRestaurants WHERE fnkRestId = ' .$_POST["list"][$n] . ' AND fnkUserId = $userId';
			$results = $thisDatabase->select($query); 

			if(empty($results)){
				if(count($_POST['list']>1)){
					print "<p>You have saved the following restaurant: <br>";
				}
				else{
					print "<p>You have saved the following restaurants: <br>";
				}

				$currId = $_POST['list'][$n];
				//print "<pre>";
				//print_r($_POST['list'][$n] . " ");
				//print "</pre>";

				$query = "INSERT INTO tblSavedRestaurants (fnkUserId, fnkRestId) VALUES ('$userId', '$currId') ";
	            $results = $thisDatabase->insert($query);

	            $query = 'SELECT fldRestName, fldFoodType, fldMenuType, ';
	        	$query .= 'CONCAT(fldStreetAdd,", ",fldCity,", ",fldState,"  ",fldZip) AS Address, fldPhone, fldURL FROM tblRestaurants WHERE pmkRestId = ' .$currId;

	      	 	$results = $thisDatabase->select($query);

	      	 	
	            foreach ($results as $row){
	            	foreach($row as $field => $value){
	            		if(!is_int($field)){
	            			$messageB .= $value .'<br>';
	            			print $value .'<br>';
	            		}
	            	}
	            }
	            $subject = "myGlutenFree Burlington restaurant saved: " . $row[0];
	            
	            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
	        }
	        else{
	        	print "<p>You have already saved one or more of these restaurants! But I appreciate your enthusiasm. <br>If you're unsure which restaurants you've saved, check your Account page!</p>";
	        }
		}
	}
	

}




	 
	if(!empty($email)){
		print '<br><input type="submit" id="btnSubmit" name="btnSubmit" value="Save" tabindex="500" class="button">';	
	}
?>	

</form>
</article>

<?php include "footer.php"; ?>

</body>
</html>