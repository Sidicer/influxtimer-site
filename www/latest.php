<?php
require_once 'inc/common.php';
require_once 'inc/infversion.php';
require_once 'config.php';
require_once 'inc/db.php';


$inf = new InfSiteDb();

if ( isset( $_GET['branch'] ) && $_GET['branch'] == 'dev' )
{
	$b = $inf->getLatestDevBuild();
}
else
{
	$b = $inf->getLatestStableBuild();
}


if ( !$b )
{
	exit( 'Sorry, no build available at the moment.' );
}


$versionname = isset( $_GET['v'] ) ? $_GET['v'] : 'full';

$ver = InfVersion::getVersionFromName( $versionname );
if ( !$ver )
	$ver = InfVersion::getFullVersion();


$file = 'dl/'.$ver->formatFileName( $b['buildnum'] );


if ( !file_exists( $file ) )
{
	exit( 'Sorry, that version is not available!' );
}


header( 'Content-Description: File Transfer' );
header( 'Content-Type: application/zip' );
header( 'Content-Disposition: attachment; filename="'.basename( $file ).'"' );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate' );
header( 'Pragma: public' );
header( 'Content-Length: '.filesize( $file ) );
readfile($file);
exit;
?>
