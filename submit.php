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
$city = "";
$state = "";
$streetAdd = "";
$zip = "";
$phone = "";
$url = "";
$menuType = "";

$email = $_SESSION['email'];

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$restNameERROR = false;
$streetAddERROR = false;
$cityERROR = false;
$stateERROR = false;
$zipERROR = false;
$phoneERROR = false;
$urlERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();

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
    $menuType = htmlentities($_POST["btnMenuType"], ENT_QUOTES, "UTF-8");
    $streetAdd = htmlentities($_POST["txtStreetAdd"], ENT_QUOTES, "UTF-8");
    $city = htmlentities($_POST["txtCity"], ENT_QUOTES, "UTF-8");
    $state = htmlentities($_POST["txtState"], ENT_QUOTES, "UTF-8");
    $zip = htmlentities($_POST["txtZip"], ENT_QUOTES, "UTF-8");
    $phone = htmlentities($_POST["txtPhone"], ENT_QUOTES, "UTF-8");
    $url = htmlentities($_POST["txtURL"], ENT_QUOTES, "UTF-8");
    
   //$menuType = $_POST['chkMenuType'];

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    if ($restName == "") {
        $errorMsg[] = "Please enter a restaurant name";
    }
    if ($streetAdd == "") {
        $errorMsg[] = "Please enter a street address";
    }
    if ($city == "") {
        $errorMsg[] = "Please enter a city";
    }
    if ($state == "") {
        $errorMsg[] = "Please enter a state";
    }
    if ($zip == "") {
        $errorMsg[] = "Please enter a zip code";
    }

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
	$query = 'CREATE TABLE IF NOT EXISTS tblRestaurants ( ';
    $query .= 'pmkRestId int(11) NOT NULL AUTO_INCREMENT, ';
    $query .= 'fldRestName varchar(50) DEFAULT NULL, ';
    $query .= 'fldFoodType varchar(20) DEFAULT NULL, ';
    $query .= 'fldMenuType varchar(30) DEFAULT NULL, '; 
    $query .= 'fldStreetAdd varchar(50) DEFAULT NULL, ';
    $query .= 'fldCity varchar(20) DEFAULT NULL, '; 
    $query .= 'fldState varchar(20) DEFAULT NULL, ';
    $query .= 'fldZip varchar(10) DEFAULT NULL, ';
    $query .= 'fldPhone varchar(15) DEFAULT NULL, ';
    $query .= 'fldURL varchar(100) DEFAULT NULL, ';
    $query .= 'PRIMARY KEY (pmkRestId) ';
    $query .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ';
    $results = $thisDatabase->insert($query);

    $query = 'CREATE TABLE IF NOT EXISTS tblSubmittedRestaurants (  ';
    $query .= 'fnkUserId int(11) NOT NULL, ';
    $query .= 'fnkRestId int(11) NOT NULL, ';
    $query .= 'PRIMARY KEY (fnkUserId, fnkRestId) ';
    $query .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ';
    $results = $thisDatabase->insert($query);

        try {
            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblRestaurants (fldRestName, fldFoodType, fldMenuType, fldStreetAdd, fldCity, fldState, fldZip, fldPhone, fldURL) VALUES ('$restName', '$foodType', '$menuType', '$streetAdd', '$city', '$state', '$zip', '$phone', '$url') ";            
            $data = array($restName);
            $data = array($foodType);
            $data = array($streetAdd);
            $data = array($city);
            $data = array($state);
            $data = array($zip);
            $data = array($phone);
            $data = array($url);
            if ($debug) {
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }
            $results = $thisDatabase->insert($query, $data);

            $query = "SELECT pmkRestId FROM tblRestaurants WHERE fldRestName = '" .$restName. "'";
            $restArr = $thisDatabase->select($query);

            foreach ($restArr as $result){
                $restId = $result['pmkRestId'];}

            $query = "SELECT pmkUserId FROM tblUser WHERE fldEmail = '" .$email. "'";
            $userArr = $thisDatabase->select($query);

            foreach ($userArr as $result){
                $userId = $result['pmkUserId'];}

            $query = "INSERT INTO tblSubmittedRestaurants (fnkUserId, fnkRestId) VALUES ('$userId', '$restId') ";
            $results = $thisDatabase->insert($query);

            $primaryKey = $thisDatabase->lastInsert();
            /*
            $query = "UPDATE tblRegister set fldConfirmed=1 WHERE fldEmail = ? ";
            $data = array($email); 
            $results = $thisDatabase->update($query, $data); */


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
            $messageB = "<p><a href='https://awbrunet.w3.uvm.edu/cs148/assignment10/index.php'>Eat Safe!</a></p>";
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
        $from = "myGlutenFree Burlington <noreply@uvm.edu>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "myGlutenFree Burlington restaurant submission: " . $restName;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
        if(!empty($email)){
            print '<meta http-equiv="refresh" content="4;url=https://awbrunet.w3.uvm.edu/cs148/assignment10/browse.php"/>';
        }
        
    } // end form is valid
    
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
        print "<h3>Your submission has ";
        print "been processed</h3>";
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
              id="frmSubmitRest">

            <fieldset class="wrapper">
                <h3>Submit a new restaurant!</h3>
                <p>As a registered user of myGlutenFree Burlington, you have the chance to make the lives of those with Celiac's a <i>little</i> easier.
                    <br>Fill out this form, and other users of myGlutenFree Burlington can benefit from your experience!
                    <br><br>
                    <b>Please, take this form seriously.</b>
                    <br><br>
                    While a phone number and website address are not required, all other elements are. 
                    <br>Fill out this form when you're sure you have <i>all</i>
                    of the necessary information.

                </p> 
                
                <fieldset class="wrapperTwo">
                    <fieldset class="submitRest">
                        <label for="txtRestName">Restaurant Name</label>
                            <input type="text" id="txtRestName" name="txtRestName"
                                   value="<?php print $restName; ?>"
                                   tabindex="100" maxlength="130" placeholder="Enter the restaurant's name"
                                   onfocus="this.select()"
                                   autofocus>
                                   <br><br>       
                        <label>Type:</label>
                            <input type="radio" name="btnFoodType" tabindex="110" value="American">American
                            <input type="radio" name="btnFoodType" tabindex="111" value="Italian">Italian
                            <input type="radio" name="btnFoodType" tabindex="112" value="Mexican">Mexican
                            <input type="radio" name="btnFoodType" tabindex="113" value="Asian">Asian
                            <input type="radio" name="btnFoodType" tabindex="114" value="Cafe">Cafe
                            <input type="radio" name="btnFoodType" tabindex="115" value="Other" checked="checked">Other                
                            <br>
                            <label>Menu Options*:</label><br>
                            <input type="radio" name="btnMenuType" tabindex="120" value="Gluten-Free Menu">Gluten-Free Menu
                            <input type="radio" name="btnMenuType" tabindex="121" value="Gluten-Friendly Menu">Gluten-Free Friendly Menu
                            <input type="radio" name="btnMenuType" tabindex="122" value="Gluten-Free Options" checked="checked">Gluten-Free Options
                            <br>
                        <br>
                        <fieldset>
                        <label for="txtStreetAdd">Address</label>
                            <input type="text" class="txtfield" id="txtStreetAdd" name="txtStreetAdd"
                                   value="<?php print $streetAdd; ?>"
                                   tabindex="150" maxlength="50" placeholder="Enter the street address"
                                   onfocus="this.select()"
                                   autofocus><br>
                        <label for="txtCity">City</label>
                            <input type="text" id="txtCity" name="txtCity"
                                   value="<?php print $city; ?>"
                                   tabindex="160" maxlength="20" placeholder="Enter the city"
                                   onfocus="this.select()"
                                   autofocus>
                        <label>State</label>
                            <input type="text" id="state" name="txtState"
                                   value="<?php print $state; ?>"
                                   tabindex="170" maxlength="3" 
                                   onfocus="this.select()"
                                   autofocus>
                        <label>Zip</label>
                            <input type="text" id="zip" name="txtZip"
                                   value="<?php print $zip; ?>"
                                   tabindex="180" maxlength="5" 
                                   onfocus="this.select()"
                                   autofocus><br>
                        <label for="txtPhone">Phone Number</label>
                            <input type="text" id="txtPhone" name="txtPhone"
                                   value="<?php print $phone; ?>"
                                   tabindex="190" maxlength="50" placeholder="Enter the phone #"
                                   onfocus="this.select()"
                                   autofocus><br>
                        <label for="txtURL">Restaurant Website</label>
                            <input type="text" id="txtURL" name="txtURL"
                                   value="<?php print $url; ?>"
                                   tabindex="191" maxlength="100" placeholder="Enter the website"
                                   onfocus="this.select()"
                                   autofocus><br>
                        </fieldset>

						
                    </fieldset> <!-- ends contact -->
                    
                </fieldset> <!-- ends wrapper Two -->
                <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit!" tabindex="500" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
            <br><br>
            <p style='width: 40%; margin: auto; font-size:.8em; text-align:justify;'> * Menu types:
                <br>
                <b>Gluten-Free Menu:</b> This restaurant offers a distinct and separate Gluten-Free menu. 
                <br>Your experience here was great!
                <br>
                <b>Gluten-Free Friendly Menu:</b> This restaurant has a Gluten-Free section OR specific Gluten-Free menu options. 
                <br>Your experience here may have been great, but patrons should be aware that you're going to have to do some searching.
                <br>
                <b>Gluten-Free Options:</b>  This restaurant offers some Gluten-Free dishes that may or may not be marked. 
                <br>Your experience here may have been great, but patrons should be aware that it may not be easy to determine <i>exactly</i> what is Gluten-Free here.
            </p>
            
        </form> 
            
    <?php
    } // end body submit
    ?>

</div>

<?php include "footer.php"; ?>

</body>
</html>

