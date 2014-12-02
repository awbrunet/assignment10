<?php
include "top.php";

$email = $_SESSION ['email'];

require_once('../bin/myDatabase.php');

$dbUserName = 'awbrunet_admin';
$whichPass = "a"; //flag for which one to use.
$dbName =  'AWBRUNET_UVM_Courses';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

$query = 'SELECT fldLogStatus FROM tblUser WHERE fldLogStatus=1';
    $checkLogin = $thisDatabase->select($query);

        if(empty($checkLogin)){
		print "<p> <a href = 'login.php'>Login</a></p>";
		}
        else{
        	
            try {
            $thisDatabase->db->beginTransaction();
            $query = 'UPDATE tblUser set fldLogStatus=0 WHERE fldEmail = "' .$email. '"'; 
            $data = array($email); 
            $results = $thisDatabase->update($query, $data); 


            if ($debug)
                print "<p>pmk= " . $primaryKey;

    // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOException $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data; please contact us directly.";
        }
        }

session_unset ();
session_destroy ();
?>

<article> 
	<h3>You are now logged out.</h3>    
    <?php 
    if(!empty($email)){
    print '<meta http-equiv="refresh" content="2;url=https://awbrunet.w3.uvm.edu/cs148/assignment10/index.php"/>'; 
    }
    ?>
    <p>Auto-redirecting, or navigate now: <a href='index.php'>Home</a>

</article>
<?php include "footer.php"; ?>
</body>
</html>