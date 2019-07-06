<?php
$deploy_url = '../deploy/deploy.php';
?>
<form action=<?php echo( $deploy_url ); ?> method="post" enctype="multipart/form-data">
    Key: <input name="key" type="text" required/><br/>
    Branch: <input name="branch" type="text" required/><br/>
    Build number: <input name="buildnum" type="text" required/><br/>
    Commit hash: <input name="commithash" type="text" required/><br/>
    Commit message: <input name="commitmsg" type="text" required/><br/>
    File: <input name="file" type="file" required/><br/>
    <br/>
    <input value="Submit" type="submit" required/><br/>
</form>
