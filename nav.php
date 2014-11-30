<!--
Aaron Brunet
CS148 Assignment 5 Nav
-->
<nav>
    <ol>
        <li><a href="index.php">Home</a></li>
        <?php 
        if(empty($_SESSION['email'])){
        print "<li><a href='register.php'>Register</a></li>";}
        
        ?>
        <li><a href="account.php">My Account</a></li>
        <?php 
        if(!empty($_SESSION['submitAuth'])){
        print "<li><a href='submit.php'>Submit New Restaurant</a></li>";
        }
        ?>
        <li><a href="browse.php">Browse Restaurants</a></li>        
        
    </ol>
</nav>