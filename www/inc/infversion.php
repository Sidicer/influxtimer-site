<?php
//
// Add more versions here.
//
define( 'INF_VER_NONE', 0 );

$INF_BUILDVERSIONS = array(
	new InfVersion( 2, 'bhop', 'Bhop' ),
	new InfVersion( 4, 'surf', 'Surf' ),
	new InfVersion( 8, 'bhoplite', 'BhopLite' ),
	new InfVersion( 16, 'deathrun', 'Deathrun' ),
	new InfVersion( 1, 'full', 'Full' )
);



class InfVersion
{
	public $bitflag;
	public $safename;
	public $fancyname;

	function __construct( $bitflag, $safename, $fancyname )
	{
		$this->bitflag = $bitflag;
		$this->safename = $safename;
		$this->fancyname = $fancyname;
	}
	
	// Helper to print the versions on page.
	function printVersionLink( $buildnum, &$added )
	{
		$filename = 'dl/'.$this->formatFileName( $buildnum );

		if ( !file_exists( $filename ) )
			return;

		echo ($added ? ' | ' : '') . '<a href="'.$filename.'">'.$this->fancyname.'</a>';
		$added = true;
	}

	// Builds a file name for the zip.
	function formatFileName( $buildnum )
	{
		$buildnum = (int)$buildnum;
		return sprintf( 'influx_%d_%s.zip', $buildnum, $this->safename );
	}

	function getDeployFileName()
	{
		return $this->safename.'.zip';
    }
    
    static function getFullVersion()
    {
        global $INF_BUILDVERSIONS;
        return $INF_BUILDVERSIONS[0];
    }

    static function getVersionFromName( $safename )
    {
        global $INF_BUILDVERSIONS;
		foreach ( $INF_BUILDVERSIONS as &$version )
		{
			if ( $version->safename == $safename )
			{
				return $version;
			}
        }
        
        return null;
    }
	
	// Safe string to a number.
	static function getVersionNumberFromName( $safename )
	{
        global $INF_BUILDVERSIONS;
		foreach ( $INF_BUILDVERSIONS as &$version )
		{
			if ( $version->safename == $safename )
			{
				return $version->bitflag;
			}
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
			$newflag = InfVersion::getVersionNumberFromName( $s );
			if ( $newflag == INF_VER_NONE )
				continue;
			
			$flags[$i] = $newflag;
			$i++;
		}
		
		return $flags;
	}
}
?>
