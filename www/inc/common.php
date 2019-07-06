<?php
define( '_INF', 1 );


define( 'INF_LOG_FILE', 'influx_log.txt' );


class InfCommon
{
	static function log( $msg )
	{
		if ( !$msg ) return;
		if ( !defined( 'INF_LOG_DIR' ) ) return;

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
}
?>
