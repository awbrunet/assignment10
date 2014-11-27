<?php
include "top.php";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.

$debug = false;

if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$restName = "";
$email = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$restNameERROR = false;
$emailERROR = false;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();

$mailed=false; // have we mailed the information to the user?
$messageA = "";
$messageB = "";
$messageC = "";

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // 
    if (!securityCheck(true)) {
        $msg = "<p>ACCESS DENIED, DUDE";
        $msg.= "<i>WOOP WOOP</i> DAS DA SOUND OF DA POLICE</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.
    
    $restName = htmlentities($_POST["txtRestName"], ENT_QUOTES, "UTF-8");
    $foodType = htmlentities($_POST["btnFoodType"], ENT_QUOTES, "UTF-8");
    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Execute SQL - Passed Validation
        //
        // Process for when the form passes validation (the errorMsg array is empty)
        //

        $primaryKey = "";
        $dataEntered = false;

        require_once('../bin/myDatabase.php');

        $dbUserName = 'awbrunet_admin';
        $whichPass = "a"; //flag for which one to use.
        $dbName =  'AWBRUNET_UVM_Courses';

        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
	
	//CREATE IF IT DOESN'T EXIST
	$query = 'CREATE TABLE IF NOT EXISTS `tblRestaurants` ( ';
    $query .= 'pmkRestId int(11) NOT NULL AUTO_INCREMENT, ';
    $query .= 'fldRestName varchar(20) DEFAULT NULL, ';
    $query .= 'fldFoodType varchar(20) DEFAULT NULL, ';
    $query .= 'fldMenuType varchar(20) DEFAULT NULL, '; 
    $query .= 'fldMenuDesc varchar(20) DEFAULT NULL, ';
    $query .= 'fldStreetNum varchar(20) DEFAULT NULL, ';
    $query .= 'fldStreetName varchar(20) DEFAULT NULL, '; 
    $query .= 'fldCity varchar(20) DEFAULT NULL, ';
    $query .= 'fldZip varchar(10) DEFAULT NULL, ';
    $query .= 'fldPhone varchar(15) DEFAULT NULL, ';
    $query .= 'fldRestURL varchar(65) DEFAULT NULL, ';
    $query .= 'PRIMARY KEY (pmkRestId) ';
    $query .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ';
    $results = $thisDatabase->insert($query);

    $query = 'CREATE TABLE IF NOT EXISTS `tblSubmittedRestaurants` (  ';
    $query .= 'fnkUserId int(11) NOT NULL, ';
    $query .= 'fnkRestId int(11) NOT NULL, ';
    $query .= 'PRIMARY KEY (fnkUserId, fnkRestId) ';
    $query .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ';
    $results = $thisDatabase->insert($query);

        try {
            $thisDatabase->db->beginTransaction();
            $query = 'INSERT INTO tblRegister SET fldEmail = ?';
            $data = array($email);
            if ($debug) {
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }
            $results = $thisDatabase->insert($query, $data);

            $primaryKey = $thisDatabase->lastInsert();

            $query = "UPDATE tblRegister set fldConfirmed=1 WHERE fldEmail = ? ";
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
            $errorMsg[] = "There was a problem with accpeting your data; please contact us directly.";
        }
        // If the transaction was successful, give success message
        /*if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $query = "SELECT fldDateJoined FROM tblRegister WHERE pmkRegisterId=" . $primaryKey;
            $results = $thisDatabase->select($query);

            $dateSubmitted = $results[0]["fldDateJoined"];

            $key1 = sha1($dateSubmitted);
            $key2 = $primaryKey;

            if ($debug)
                print "<p>key 1: " . $key1;
            if ($debug)
                print "<p>key 2: " . $key2;*/


            //#################################################################
            //
            //Put forms information into a variable to print on the screen
            //

            $messageA = '<h2>Thank you for submitting a new restaurant to myGlutenFree Burlington!</h2>';
           
            $messageC .= "<p><b>You submitted:</b><i>   " . $restName . "</i></p>";

        
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $email; // the person who filled out the form
        $cc = "";
        $bcc = "";
        $from = "CRUD <noreply@uvm.edu>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "CRUD: " . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
        
    } // end form is valid
    }
} // ends if form was submitted.

//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

    <?php
    //####################################
    //
    // SECTION 3a.
    //
    // 
    // 
    // 
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h1>Your Request has ";

        if (!$mailed) {
            print "not ";
        }

        print "been processed</h1>";

        print "<p>A copy of this message has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $email . "</p>";
        print "<p>Mail Message:</p>";

        print $messageA . $messageC;
        
    } else {


        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form

        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }


        //####################################
        //
        // SECTION 3c html Form
        //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:

          value="<?php print $email; ?>

          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)

          NOTE this line:

          <?php if($emailERROR) print 'class="mistake"'; ?>

          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.

         */
        ?>
		</article>
		<div>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmClassSearch">

            <fieldset class="wrapper">
                <legend>CRUD</legend>
                
                <fieldset class="wrapperTwo">
                    <fieldset class="searchTerms">
                        <label for="txtRestName">Restaurant Name</label>
                            <input type="text" id="txtRestName" name="txtRestName"
                                   value="<?php print $RestName; ?>"
                                   tabindex="100" maxlength="130" placeholder="Enter the restaurant's name"
                                   onfocus="this.select()"
                                   autofocus><br>       
                        <label for="btnFoodType">Type</label>
                            <input type="radio" id="btnFoodType" name="btnFoodType" value="0">American
                            <input type="radio" id="btnFoodType" name="btnFoodType" value="1">Italian
                            <input type="radio" id="btnFoodType" name="btnFoodType" value="2">Mexican
                            <input type="radio" id="btnFoodType" name="btnFoodType" value="3">Asian
                            <input type="radio" id="btnFoodType" name="btnFoodType" value="4">Other                
                            <br>
                        <label for="chkMenuType">Menu Options</label>
                            <input type="checkbox" id="chkMenuType" name="chkMenuType" value="0">Gluten-Free Menu
                            <input type="checkbox" id="chkMenuType" name="chkMenuType" value="1">Gluten-Friendly Menu
                            <input type="checkbox" id="chkMenuType" name="chkMenuType" value="2">Gluten-Free Options
                            <br>
                        <label for="txtEmail">Email Address</label>
                            <input type="text" class="txtfield" id="txtEmail" name="txtEmail"
                                   value="<?php print $email; ?>"
                                   tabindex="120" maxlength="50" placeholder="Please enter your email address"
                                   onfocus="this.select()"
                                   autofocus><br>
						
                    </fieldset> <!-- ends contact -->
                    
                </fieldset> <!-- ends wrapper Two -->
                <br>
                <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register!" tabindex="500" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>

    <?php
    } // end body submit
    ?>

</div>

<?php include "footer.php"; ?>

</body>
</html>

