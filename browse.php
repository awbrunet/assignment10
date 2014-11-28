<?php

include "top.php";

?>

<article id="main">
<p> browse restaurants </p>

<?php
$checkboxes = $_POST['list'];
$_SESSION['list'] = $checkboxes;
$i='0';

		require_once('../bin/myDatabase.php');

        $dbUserName = 'awbrunet_writer';
        $whichPass = "w"; //flag for which one to use.
        $dbName =  'AWBRUNET_UVM_Courses';

        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

        $query = 'SELECT * FROM tblRestaurants';

        $results = $thisDatabase->select($query);


        $numberRecords = count($results);

        print "<h2>Restaurants: " . $numberRecords . "</h2>";

        print "<table>";

        $firstTime = true;

        /* since it is associative array display the field names */
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
            
            /* display the data, the array is both associative and index so we are
             *  skipping the index otherwise records are doubled up */
            print "<tr>";
            foreach ($row as $field => $value) {
                if (!is_int($field)) {
                    //print "<td>" . $value . "</td>";
                    print $value. " ";
                }
            }
            print "<br>";
            //print "<td>" .$i++. "</td>";
            //print "<td><input type='checkbox' name='list[" .$i. "]' value='/></td>";
            //print "</tr>";
        }
        print "</table>";

if (isset($_POST["btnSubmit"]))
{
	print "<p> ugh 1</p>";

foreach($_SESSION['list'] as $key => $value)
{
	echo '<input type="checkbox" name="list[' .$key. ']" value="'.$value.'" checked="checked >';
}
}

?>

<form action="<?php print $phpSelf; ?>"
              method="post">

	<input type="submit" id="btnSubmit" name="btnSubmit" value="Send my restaurants!" tabindex="500" class="button">

</form>
</article>

<?php include "footer.php"; ?>

</body>
</html>