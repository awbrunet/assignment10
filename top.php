<!DOCTYPE html>
<html lang="en">
	<head>    
		<title>myGlutenFree Burlington</title>
		<meta charset="utf-8">
		<meta name="author" content="Aaron Brunet">
		<meta name="description" content="CS148 Final Assignment">
		
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" href="mystyle.css" media="screen">
        <link rel="shortcut icon" href="lib/favicon.ico" type="image/x-icon">
        <link rel="icon" href="lib/favicon.ico" type="image/x-icon">		

	  <?php 
	  $debug = false;


// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// PATH SETUP
//
//  $domain = "https://www.uvm.edu" or http://www.uvm.edu;

        $domain = "http://";
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS']) {
                $domain = "https://";
            }
        }

        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");

        $domain .= $server;

        $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

        $path_parts = pathinfo($phpSelf);

        if ($debug) {
            print "<p>Domain" . $domain;
            print "<p>php Self" . $phpSelf;
            print "<p>Path Parts<pre>";
            print_r($path_parts);
            print "</pre>";
        }

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// inlcude all libraries
//

        require_once('lib/security.php');

        include "lib/mail-message.php";
	include "lib/validation-functions.php";
        ?>	

    </head>
    <!-- ################ body section ######################### -->

    <?php
    print '<body id="' . $path_parts['filename'] . '">';

    include "header.php";
    include "nav.php";
    print '<div id="backgroundSpacer"></div>';
    ?>






