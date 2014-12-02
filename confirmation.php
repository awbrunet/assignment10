<?php
/* This page allows the Admin account to approve
 * new admin account requests.
 * It declines to use hashing as this is not a site that even uses passwords, but is rather a handy community tool.
 */

include "top.php";

print '<article id="main">';

print '<h1>Registration Confirmation</h1>';

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
$debug = false;
if (isset($_GET["debug"])) {
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%

$adminEmail = "awbrunet@uvm.edu";
$message = "<p>Error! Contact the admin at " .$adminEmail. "</p>";


//##############################################################
//
// SECTION: 2 
// 
// process request

if (isset($_GET["q"])) {
    $key1 = htmlentities($_GET["q"], ENT_QUOTES, "UTF-8");
    $key2 = htmlentities($_GET["w"], ENT_QUOTES, "UTF-8");

    $data = array($key2);
    //##############################################################
    // get the membership record 

    require_once('../bin/myDatabase.php');

        $dbUserName = 'awbrunet_admin';
        $whichPass = "a"; //flag for which one to use.
        $dbName =  'AWBRUNET_UVM_Courses';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

    $query = "SELECT pmkUserId FROM tblUser WHERE pmkUserId = " .$key1;
    $results = $thisDatabase->select($query, $data);

    $confirmId = $results[0]['pmkUserId'];
    $email = $results[0]["fldEmail"];

    $k1 = sha1($provEmail);

    if ($debug) {
        print "<p>Date: " . $dateSubmitted;
        print "<p>email: " . $email;
        print "<p><pre>";
        print_r($results);
        print "</pre></p>";
        print "<p>k1: " . $k1;
        print "<p>q : " . $key1;
    }
    //##############################################################
    // update confirmed
    //if ($key1 == $k1) {
        if ($debug)
            print "<h1>Confirmed</h1>";

        $query = "UPDATE tblUser set fldAdmin=1 WHERE pmkUserId = " .$confirmId;
        $results = $thisDatabase->update($query, $data);

        if ($debug) {
            print "<p>Query: " . $query;
            print "<p><pre>";
            print_r($results);
            print_r($data);
            print "</pre></p>";
        }
        
        // notify approver
        $message = "<p>The user has been approved as an admin.</p>";

        print $message;
        
        
    
} // ends isset get q
?>



<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</article>
</body>
</html>