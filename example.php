<?php

require_once("LookeryTargeting.php");

$targeting = new LookeryTargeting("ENTER_YOUR_API_KEY", "ENTER_YOUR_SECRET_KEY");
$redirect = $targeting->redirect("http://www.example.com/?a={profile_yob}&g={profile_gender}");
print($redirect . "\n");

?>
