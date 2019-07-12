<?php
require_once '../inc/common.php';
require_once '../inc/infversion.php';
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
$tar = null;

try
{
	$tar = new PharData( $file['tmp_name'] );
}
catch ( Exception $e )
{
	InfCommon::log( 'Failed to open tar!' );
	return;
}


foreach ( $INF_BUILDVERSIONS as &$version )
{
	$deployfilename = $version->getDeployFileName();

	$success = false;
	try
	{
		$success = $tar->extractTo( INF_TEMP_DIR, $deployfilename );
	}
	catch ( Exception $e )
	{
		InfCommon::log( "Deployment tar didn't have {$deployfilename} in it..." );
	}


	if ( !$success )
	{
		continue;
	}


	$input = INF_TEMP_DIR.'/'.$deployfilename;
	$output = $publicdir.'/'.$version->formatFileName( $buildnum );


	if ( !file_exists( $input ) )
	{
		InfCommon::log( "File '{$input}' doesn't exist anymore! Wut?" );
		continue;
	}

	if ( file_exists( $output ) )
	{
		InfCommon::log( "File '{$output}' already existed! Deleting..." );
		unlink( $output );
	}

	if ( !rename( $input, $output ) )
	{
		$success = false;
	}

	if ( $success )
	{
		$verflags |= $version->bitflag;
	}
	else
	{
		unlink( $input );
	}
}


unset( $tar );


if ( !$verflags )
{
	InfCommon::log( 'Failed to extract any files!' );
	return;
}


// Save to db
$inf = new InfSiteDb();
$inf->createTables();

$inf->saveBuild( $buildnum, $verflags, $branch, $commithash, $commitmsg );



//
// Remove old dev builds
//
$num_builds_keep = 8;

$oldbuilds = $inf->getOldBuilds( $buildnum, 'dev', $num_builds_keep );
if ( $oldbuilds )
{
	foreach ( $oldbuilds as &$b )
	{
		foreach ( $INF_BUILDVERSIONS as &$version )
		{
			$filename = '../dl/'.$version->formatFileName( $b['buildnum'] );

			if ( file_exists( $filename ) )
			{
				unlink( $filename );
			}
		}
	}

	$inf->removeOlderBuilds( $buildnum - $num_builds_keep, 'dev' );
}


InfCommon::log( @date( 'Y-m-d H:i:s' ) . ' Done deploying!' );
?>
