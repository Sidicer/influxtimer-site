<?php
define( 'DB_TABLE_BUILDS', 'infbuilds' );


class InfSiteDb
{
	private $pdo;
	
	function __construct()
	{
		$options = array();
		
		if ( INF_SQL_PERSISTENT )
		{
			$options[PDO::ATTR_PERSISTENT] = true;
		}
		
		try
		{
			$this->pdo = new PDO(
				'mysql:host=' . INF_DB_HOST . ';port=' . INF_DB_PORT . ';dbname=' . INF_DB_NAME . ';charset=utf8',
				INF_DB_USER,
				INF_DB_PASS,
				$options );
		}
		catch ( PDOException $e )
		{
			if ( INF_DEBUG )
			{
				exit( 'Cannot connect to database! Error: ' . $e->getMessage() );
			}
			
			exit( 'Sorry, something went wrong with the database! Enable debug mode in the configuration file to know more.' );
		}
	}
	
	// Get PDO.
	public function getDB()
	{
		return $this->pdo;
	}
	
	
	public function createTables()
	{
		$this->pdo->query(	'CREATE TABLE IF NOT EXISTS '.DB_TABLE_BUILDS.' (' .
							'bid INTEGER AUTO_INCREMENT PRIMARY KEY,' .
							'builddate datetime NOT NULL DEFAULT NOW(),' .
							'buildnum INTEGER NOT NULL UNIQUE,' .
							'verflags INTEGER NOT NULL,' .
							'branch VARCHAR(64) NOT NULL,' .
							'commithash VARCHAR(40) NOT NULL UNIQUE,' .
							'commitmsg VARCHAR(512) NOT NULL)' );
	}
	
	public function getLatestStableBuild()
	{
		return $this->getLatestBuild( 'master' );
	}
	
	public function getLatestDevBuild()
	{
		return $this->getLatestBuild( 'dev' );
	}
	
	private function getLatestBuild( $branch )
	{
		$res = $this->pdo->prepare( 'SELECT buildnum,builddate,verflags FROM '.DB_TABLE_BUILDS." WHERE branch=:branch ORDER BY builddate DESC LIMIT 1" );
		
		if ( !$res )
		{
			return false;
		}
		
		$res->bindParam( ':branch', $branch );
		
		if ( !$res->execute() )
		{
			return false;
		}
		
		
		return $res->fetch();
	}
	
	public function getLatestBuilds( $branch, $limit = 5 )
	{
		$res = $this->pdo->prepare( 'SELECT buildnum,verflags,commithash,commitmsg,builddate FROM '.DB_TABLE_BUILDS." WHERE branch=:branch ORDER BY builddate DESC LIMIT :limit" );
		
		if ( !$res )
		{
			return false;
		}
		
		
		$res->bindParam( ':branch', $branch );
		$limit = (int)$limit;
		$res->bindParam( ':limit', $limit, PDO::PARAM_INT );
		
		
		if ( !$res->execute() )
		{
			return false;
		}
		
		
		return $res->fetchAll();
	}
	
	public function saveBuild( $buildnum, $verflags, $branch, $commithash, $commitmsg )
	{
		$res = $this->pdo->prepare( 'INSERT INTO '.DB_TABLE_BUILDS.' (buildnum,verflags,branch,commithash,commitmsg) VALUES (:buildnum,:verflags,:branch,:commithash,:commitmsg)' );
		
		if ( !$res )
		{
			return false;
		}
		
		$buildnum = (int)$buildnum;
		$verflags = (int)$verflags;
		$res->bindParam( ':buildnum', $buildnum, PDO::PARAM_INT );
		$res->bindParam( ':verflags', $verflags, PDO::PARAM_INT );
		$res->bindParam( ':branch', $branch );
		$res->bindParam( ':commithash', $commithash );
		$res->bindParam( ':commitmsg', $commitmsg );
		
		
		return $res->execute();
	}
	
	public function getOldBuilds( $curbuildid, $branch, $num_ignore = 10 )
	{
		$curbuildid = (int)$curbuildid;
		
		$res = $this->pdo->prepare( 'SELECT * FROM '.DB_TABLE_BUILDS.' WHERE buildnum<:lastbuildnum AND branch=:branch' );
	
		if ( !$res )
		{
			return false;
		}
		
		
		$lastbuildnum = $curbuildid - (int)$num_ignore;
		$res->bindParam( ':lastbuildnum', $lastbuildnum, PDO::PARAM_INT );
		$res->bindParam( ':branch', $branch );
		
		if ( !$res->execute() )
		{
			return false;
		}
		
		
		return $res->fetchAll();
	}
	
	public function removeOlderBuilds( $buildnum, $branch )
	{
		$res = $this->pdo->prepare( 'DELETE FROM '.DB_TABLE_BUILDS.' WHERE buildnum<:buildnum AND branch=:branch' );
	
		if ( !$res )
		{
			return false;
		}
		

		$buildnum = (int)$buildnum;
		$res->bindParam( ':buildnum', $buildnum, PDO::PARAM_INT );
		$res->bindParam( ':branch', $branch );
		
		return $res->execute() ? true : false;
	}
}
?>
