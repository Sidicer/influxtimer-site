<?php
$deploy_url = '../deploy/deploy_site.php';
?>
<form action=<?php echo( $deploy_url ); ?> method="post" enctype="multipart/form-data">
    Key: <input name="key" type="text" required/><br/>
    File: <input name="file" type="file" required/><br/>
    <br/>
    <input value="Submit" type="submit" required/><br/>
</form>
