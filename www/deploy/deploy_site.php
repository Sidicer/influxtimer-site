<?php
require_once '../inc/common.php';
require_once '../config.php';
require_once '../inc/db.php';


if ( !defined( 'INF_DEPLOY_KEY_SITE' ) || !INF_DEPLOY_KEY_SITE )
{
	exit( 'Deploying disabled.' );
}



$extractdir = '../';


set_time_limit( 60 );


InfCommon::log( @date( 'Y-m-d H:i:s' ) . ' Starting site deployment...' );

$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
if ( !$key || $key != INF_DEPLOY_KEY_SITE )
{
	InfCommon::log( 'Wrong key!' );
	return;
}


$filecount = count( $_FILES );
if ( $filecount != 1 )
{
	InfCommon::log( "Wrong file count {$filecount}!" );
	return;
}

$file = reset( $_FILES );
if ( !$file )
{
	InfCommon::log( 'No files...' );
	return;
}


$filecount = count( $file['tmp_name'] );

// We should only have one file, the tar.
if ( $filecount != 1 )
{
	InfCommon::log( "Wrong inner file count {$filecount}!" );
	return;
}


// Open up the tar and extract.
if ( !file_exists( INF_TEMP_DIR ) && !mkdir( INF_TEMP_DIR ) )
{
	InfCommon::log( "Failed to create temporary folder!" );
	return;
}


$tar_success = true;

try
{
	$tar = new PharData( $file['tmp_name'] );

	try
	{
		$tar->delete( 'config.php' );
	}
	catch ( Exception $e )
	{
	}

	$tar->extractTo( $extractdir, null, true );
}
catch ( PharException $e )
{
	InfCommon::log( 'Failed to extract tar!' );
	$tar_success = false;
}


if ( !$tar_success )
{
	return;
}


InfCommon::log( @date( 'Y-m-d H:i:s' ) . ' Done deploying!' );
?>
