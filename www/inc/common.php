<?php
define( '_INF', 1 );


define( 'INF_LOG_FILE', 'influx_log.txt' );

// Add more versions here.
define( 'INF_VER_NONE', 0 );
define( 'INF_VER_FULL', 1 );
define( 'INF_VER_BHOP', 2 );
define( 'INF_VER_SURF', 4 );
define( 'INF_VER_BHOPLITE', 8 );


class InfCommon
{
	static function log( $msg )
	{
		if ( !$msg ) return;

		$fp = @fopen( INF_LOG_DIR.'/'.INF_LOG_FILE, 'a+' );

		if ( $fp )
		{
			fwrite( $fp, $msg."\n" );
			fclose( $fp );
		}
	}

	
	// Commit message and description is separated by new line.
	static function getCommitMsg( $msg )
	{
		$m = strstr( $msg, "\n", true );
		
		return $m ? $m : $msg;
	}
	
	// Helper to print the versions on page.
	static function printVersionLink( $buildnum, $verflags, $flag, &$added )
	{
		if ( $verflags & $flag )
		{
			echo ($added ? ' | ' : '') . '<a href="dl/'.InfCommon::formatFileName( $buildnum, $flag ).'">'.InfCommon::getVersionName( $flag ).'</a>';
			$added = true;
		}
	}

	// Builds a file name for the zip.
	static function formatFileName( $buildnum, $verflag )
	{
		$buildnum = (int)$buildnum;
		return sprintf( 'influx_%d_%s.zip', $buildnum, InfCommon::getVersionSafeName( $verflag ) );
	}
	
	
	// Version number to a string used in filenames, etc.
	static function getVersionSafeName( $version )
	{
		switch ( $version )
		{
			case INF_VER_FULL : return 'full';
			case INF_VER_BHOP : return 'bhop';
			case INF_VER_SURF : return 'surf';
			case INF_VER_BHOPLITE : return 'bhoplite';
		}
		
		return 'na';
	}
	
	// Version number to a string used on pages.
	static function getVersionName( $version )
	{
		switch ( $version )
		{
			case INF_VER_FULL : return 'Full';
			case INF_VER_BHOP : return 'Bhop';
			case INF_VER_SURF : return 'Surf';
			case INF_VER_BHOPLITE : return 'BhopLite';
		}
		
		return 'N/A';
	}
	
	// Version safe string to a number.
	static function getVersionFromName( $version )
	{
		switch ( $version )
		{
			case 'full' : return INF_VER_FULL;
			case 'bhop' : return INF_VER_BHOP;
			case 'surf' : return INF_VER_SURF;
			case 'bhoplite' : return INF_VER_BHOPLITE;
		}
		
		return INF_VER_NONE;
	}
	
	// In: 'full|bhop'
	// Out: Version bit flags.
	static function getVersionFlagsFromString( $str )
	{
		$arr = explode( '|', $str, 6 );
		
		$flags = array();
		$i = 0;
		
		foreach ( $arr as &$s )
		{
			$newflag = InfCommon::getVersionFromName( $s );
			if ( $newflag == INF_VER_NONE )
				continue;
			
			$flags[$i] = $newflag;
			$i++;
		}
		
		return $flags;
	}
}
?>
