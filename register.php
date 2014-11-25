<!--Aaron Brunet
CS148 Assignment 5 Form 
Largely based on Bob's example form-->
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

$professor = "";
$subject = "";
$courseNum = "";
$building = "";
$startTime = "";
$zSection = "";
$type = "";

//I'M NOT SURE IF THESE PARTS ARE NECESSARY BUT I DON'T WANNA ACCIDENTALLY BREAK MY CODE
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
// Make the button do the thing!
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // The security check included with the example form
    if (!securityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.
    
    // Associate variables with the form data

    $subject = htmlentities($_POST["txtSubject"], ENT_QUOTES, "UTF-8");
    $courseNum = htmlentities($_POST["txtCourseNum"], ENT_QUOTES, "UTF-8");
    $professor = htmlentities($_POST["txtProfessor"], ENT_QUOTES, "UTF-8");
	  $startTime = htmlentities($_POST["txtStartTime"], ENT_QUOTES, "UTF-8");
    $building = htmlentities($_POST["lstBuilding"], ENT_QUOTES, "UTF-8");
    $zSection = htmlentities($_POST["chkZSection"], ENT_QUOTES, "UTF-8");
    $type = htmlentities($_POST["lstType"], ENT_QUOTES, "UTF-8");

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //

    // LET'S GET TO THE HARD WORK NOW

    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Execute SQL - Passed Validation
        //
        // Process for when the form passes validation (the errorMsg array is empty)
        //

        /* ##### Step one 
         * 
         * create your database object using the appropriate database username
         */
        
        // Create database
        require_once('../bin/myDatabase.php');

        $dbUserName = 'awbrunet_reader';
        $whichPass = "r"; //flag for which one to use.
        $dbName =  'AWBRUNET_UVM_Courses';

        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
        

        /* ##### Step two
         * Build the query
         *      
         */
		//whole table 
        $query  = '';
        $query .= ' SELECT concat(tblCourses.fldDepartment, " ", tblCourses.fldCourseNumber) AS Course, '; //Course (duh)
        $query .= ' tblCourses.fldCourseName AS Name, '; //Course Name
        $query .= ' tblSections.fldCRN AS CRN, '; //CRN
        $query .= ' concat(tblTeachers.fldFirstName, " " , tblTeachers.fldLastName) AS Professor, '; //Professor
        $query .= ' tblSections.fldSection AS Section, '; //Section
        $query .= ' tblSections.fldType AS Type, '; //Class type
        $query .= ' tblSections.fldStart AS Start, '; //Start time
        $query .= ' tblSections.fldStop AS End, '; //End time
        $query .= ' tblSections.fldDays AS Days, '; //Days held
        $query .= ' tblSections.fldBuilding AS Building, '; //Location
        $query .= ' tblSections.fldRoom AS Room '; //Room #
        //$query .= ' concat(tblSections.fldBuilding, " " , tblSections.fldRoom) as Location, '; //For some reason this kept breaking my code
        //so I just separated them        
        $query .= ' FROM tblCourses, tblSections, tblTeachers ';
        $query .= ' WHERE pmkNetId = fnkTeacherNetId ';
        $query .= ' AND pmkCourseId = fnkCourseId';
		//search terms
        $query .= ' AND tblCourses.fldDepartment LIKE "' . $subject . '%"';
		$query .= ' AND tblCourses.fldCourseNumber LIKE "' . $courseNum . '%"';
		$query .= ' AND tblTeachers.fldLastName LIKE "' . $professor . '%"';
        $query .= ' AND tblSections.fldStart LIKE "' . $startTime . '%"';
        $query .= ' AND tblSections.fldBuilding LIKE "' . $building . '%"';
		//remove annoying CS overlap (searching CS returned CSD and CSYS)
		if($subject == "CS"){
			$query .= ' AND tblCourses.fldDepartment NOT LIKE "CSD" ';
			$query .= ' AND tblCourses.fldDepartment NOT LIKE "CSYS" ';
		}
		//check extras
        if($type != "All"){
            $query .= ' AND tblSections.fldType LIKE "' . $type . '"';
        }
		if($zSection != "Z"){
            $query .= ' AND tblSections.fldSection NOT LIKE "%Z%" ';
        }
        
        /* ##### Step three
         * Execute the query
         *      
         */

        // DO THE THING
        $results = $thisDatabase->select($query);
        
        
    } // end form is valid
    
} // ends if form was submitted.

//#############################################################################
//
// SECTION 3 Display Form
//
?>
	<!-- MAKE THE MAGIC HAPPPENNNNNN -->
	<article id="main">
    <?php
    //####################################
    //
    // SECTION 3a.
    //
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        /* ##### Step four
         * prepare output and loop through array
         *
         */
        $numberRecords = count($results);
        if(count($results)==0){
          print "<h3>No classes found! <a href='form.php'>Search again?</a></h3>";
        }
        print "<br><table>";
        $firstTime = true;

        foreach ($results as $row) {
            if ($firstTime) {
                print "<thead><tr>";
                $keys = array_keys($row);
                foreach ($keys as $key) {
                    if (!is_int($key)) {
                        print "<th>" . $key . "</th>";
                    }
                }
                print "</tr>";
                $firstTime = false;
            }
            
            print "<tr>";
            foreach ($row as $field => $value) {
                if (!is_int($field)) {
                    print "<td>" . $value . "</td>";
                }
            }
            print "</tr>";
        }
        print "</table><br>";
        
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
		

		<div id="formHolder"> 
		<br>
        <form action="<?php print $phpSelf; ?>" 
              method="post"
              id="frmClassSearch"> <!-- make form -->

            <fieldset class="wrapper">
                <legend><h3>UVM Spring Course Search</h3></legend>
                <p>Fill out any or all of the search terms below to begin the hunt!</p>

                
                    <fieldset class="searchTerms">
                        <label for="txtSubject">Subject</label>
                            <input type="text" class="txtfield" id="txtSubject" name="txtSubject"
                                   value="<?php print $subject; ?>"
                                   tabindex="100" maxlength="50" placeholder="Enter course subject (ex: CS)"
                                   onfocus="this.select()"
                                   autofocus><br>                        
                        <label for="txtcourseNum">Course #</label>
                            <input type="text" class="txtfield" id="txtCourseNum" name="txtCourseNum"
                                   value="<?php print $courseNum; ?>"
                                   tabindex="120" maxlength="50" placeholder="Enter course # (ex: 148)"
                                   onfocus="this.select()"
                                   autofocus><br>
						            <label for="txtProfessor">Professor</label>
                            <input type="text" class="txtfield" id="txtProfessor" name="txtProfessor"
                                   value="<?php print $professor; ?>"
                                   tabindex="140" maxlength="50" placeholder="Enter professor's last name (ex: Erickson)"
                                   onfocus="this.select()"
                                   autofocus><br>
                        <label for="txtStartTime">Start Time</label>
                            <input type="text" class="txtfield" id="txtStartTime" name="txtStartTime"
                                   value="<?php print $startTime; ?>"
                                   tabindex="160" maxlength="50" placeholder="Enter start time (ex: 13:00)"
                                   onfocus="this.select()"
                                   autofocus><br><br>                    
                        <!-- this is what I did to make the buildings and class types NOT be hard-coded. 
                        I toyed with a few methods that all crashed and burned, but this way was suggested
                        to me by a classmate and it seems to work -->           	
                        <?php
						             //pull building and class types rather than hardcode
                         require_once('../bin/myDatabase.php');
						             $dbUserName = 'awbrunet_reader';
                         $whichPass = "r"; //flag for which one to use.
                         $dbName =  'AWBRUNET_UVM_Courses';
                         $pullDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
						             //build a query to pull building names from
                         $buildingPull = "SELECT DISTINCT fldBuilding ";
						             $buildingPull .= " FROM tblSections ";
						             $buildingPull .= " ORDER BY fldBuilding ASC";
                         $buildingList = $pullDatabase->select($buildingPull);
						             //make field
                         print "<label for=\"lstBuilding\"> Building
                            <select id=\"lstBuilding\"
									class=\"txtfield\"
                                    name=\"lstBuilding\"
                                    tabindex=\"200\" >";
                         //fill field
                         for ($row = 0; $row < count($buildingList); $row++) {
                              for ($col = 0; $col < 1; $col++) {
                                echo "<option value=\"".$buildingList[$row][$col]."\">".$buildingList[$row][$col]."</option>";
                              }
                        }
                            print "</select></label>";
					             	//do it all again
                        $typePull = "SELECT DISTINCT fldType ";
					             	$typePull .= " FROM tblSections ";
						            $typePull .= " ORDER BY fldType ASC";
                        $typeList = $pullDatabase->select($typePull);
						            //make a default entry for class types (more important than building types because there's no blank)
                        $default = array("All", "set default");//otherwise the default search is the alphabetically first classtype
                        array_unshift($typeList, $default);
                        //make it appear 
                        //it's like magic
                        print "<label for=\"lstType\">Type
                               <select id=\"lstType\"
									      name=\"lstType\"
									      tabindex=\"220\" >";
                        //fill it 
                         for ($row = 0; $row < count($typeList); $row++) {
                              for ($col = 0; $col < 1; $col++) {
                                echo "<option value=\"".$typeList[$row][$col]."\">".$typeList[$row][$col]."</option>\n";
                              }
                        }
                            print "</select></label>";//cap it    
                         ?>

                         <label>Z-Sections?</label>
						 <input type="checkbox" id="chkZSection" class="txtfield"		
                                name="chkZSection" value="Z" tabindex="300" checked>
                    </fieldset> <!-- ends contact -->
                    
                
                <br>
                <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Search for courses" tabindex="500" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>
        </div>
        </article>

    <?php
    } // end body submit
    ?>

<?php include "footer.php"; ?>

</body>
</html>

