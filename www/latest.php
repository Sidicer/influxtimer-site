<?php
require_once 'inc/common.php';
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


$version = isset( $_GET['v'] ) ? $_GET['v'] : 'full';

$flag = InfCommon::getVersionFromName( $version );
if ( $flag == INF_VER_NONE )
	$flag = INF_VER_FULL;


$file = 'dl/'.InfCommon::formatFileName( $b['buildnum'], $flag );


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
