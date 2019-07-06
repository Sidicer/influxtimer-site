<?php
require_once '../inc/common.php';
require_once '../config.php';
require_once '../inc/db.php';


if ( !defined( 'INF_DEPLOY_KEY' ) || !INF_DEPLOY_KEY )
{
	exit( 'Deploying disabled.' );
}



$publicdir = __DIR__.'/../dl';


set_time_limit( 60 );


InfCommon::log( @date( 'Y-m-d H:i:s' ) . ' Starting deployment...' );

$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
if ( !$key || $key != INF_DEPLOY_KEY )
{
	InfCommon::log( 'Wrong key!' );
	return;
}


$buildnum = isset( $_POST['buildnum'] ) ? (int)$_POST['buildnum'] : 0;
if ( !$buildnum )
{
	InfCommon::log( "Invalid build number '{$buildnum}'!" );
	return;
}


$branch = isset( $_POST['branch'] ) ? $_POST['branch'] : '';
if ( !$branch )
{
	InfCommon::log( 'No branch set!' );
	return;
}


$commithash = isset( $_POST['commithash'] ) ? $_POST['commithash'] : '';
if ( !$commithash || strlen( $commithash ) != 40 )
{
	InfCommon::log( "Invalid commit hash '{$commithash}'!" );
	return;
}


$commitmsg = isset( $_POST['commitmsg'] ) ? $_POST['commitmsg'] : '';
$commitmsg = InfCommon::getCommitMsg( $commitmsg );
if ( !$commitmsg )
{
	InfCommon::log( "Invalid commit msg '{$commitmsg}'!" );
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


if ( !file_exists( INF_TEMP_DIR ) && !mkdir( INF_TEMP_DIR ) )
{
	InfCommon::log( "Failed to create temporary folder!" );
	return;
}


$verflags = 0;


// Open up the tar and extract the zips.
$tar_success = true;

try
{
	$tar = new PharData( $file['tmp_name'] );

	if ( $tar->extractTo( INF_TEMP_DIR, 'full.zip' ) )
	{
		$verflags |= INF_VER_FULL;
	}
	if ( $tar->extractTo( INF_TEMP_DIR, 'bhop.zip' ) )
	{
		$verflags |= INF_VER_BHOP;
	}
	if ( $tar->extractTo( INF_TEMP_DIR, 'surf.zip' ) )
	{
		$verflags |= INF_VER_SURF;
	}
	if ( $tar->extractTo( INF_TEMP_DIR, 'bhoplite.zip' ) )
	{
		$verflags |= INF_VER_BHOPLITE;
	}
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


if ( !$verflags )
{
	InfCommon::log( 'Failed to extract any files!' );
	return;
}



function MoveUpload( &$flags, $f, $buildnum, $outputdir, $inputfile )
{
	$success = true;

	if ( $flags & $f )
	{
		$output = $outputdir.'/'.InfCommon::formatFileName( $buildnum, $f );

		if ( file_exists( $output ) )
		{
			unlink( $output );
		}

		if ( !rename( $inputfile, $output ) )
		{
			$success = false;
		}
	}
	else
	{
		$success = false;
	}

	if ( !$success )
	{
		$flags = $flags & (~$f);
	}
}

// Move the zips to public directory for download.
MoveUpload( $verflags, INF_VER_FULL, $buildnum, $publicdir, INF_TEMP_DIR.'/full.zip' );
MoveUpload( $verflags, INF_VER_BHOP, $buildnum, $publicdir, INF_TEMP_DIR.'/bhop.zip' );
MoveUpload( $verflags, INF_VER_SURF, $buildnum, $publicdir, INF_TEMP_DIR.'/surf.zip' );
MoveUpload( $verflags, INF_VER_BHOPLITE, $buildnum, $publicdir, INF_TEMP_DIR.'/bhoplite.zip' );


// Save to db
$inf = new InfSiteDb();
$inf->createTables();

$inf->saveBuild( $buildnum, $verflags, $branch, $commithash, $commitmsg );



//
// Remove old builds
//
function RemoveFile( $file )
{
	if ( file_exists( $file ) )
	{
		unlink( $file );
	}
}

$num_builds_keep = 8;

$oldbuilds = $inf->getOldBuilds( $buildnum, 'dev', $num_builds_keep );
if ( $oldbuilds )
{
	foreach ( $oldbuilds as &$b )
	{
		RemoveFile( 'dl/' . InfCommon::formatFileName( $b['buildnum'], INF_VER_FULL ) );
		RemoveFile( 'dl/' . InfCommon::formatFileName( $b['buildnum'], INF_VER_BHOP ) );
		RemoveFile( 'dl/' . InfCommon::formatFileName( $b['buildnum'], INF_VER_SURF ) );
		RemoveFile( 'dl/' . InfCommon::formatFileName( $b['buildnum'], INF_VER_BHOPLITE ) );
	}
	$inf->removeOlderBuilds( $buildnum - $num_builds_keep, 'dev' );
}


InfCommon::log( @date( 'Y-m-d H:i:s' ) . ' Done deploying!' );
?>
