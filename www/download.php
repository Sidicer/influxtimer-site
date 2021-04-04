<?php
require_once 'inc/common.php';
require_once 'inc/infversion.php';
require_once 'config.php';
require_once 'inc/db.php';

$inf = new InfSiteDb();

if ( INF_DEBUG )
{
	$inf->createTables();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Influx Timer - Download</title>
<link rel="shortcut icon" type="image/png" href="img/icon.png"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="styles/main.css">
<script src="js/randbg.js"></script>
<meta name="description" content="Influx Timer, a SourceMod timer plugin for CSS and CS:GO game servers."/>
<meta name="keywords" content="influx, timer, plugin, sourcemod, counter-strike, source, global offensive, cs, sm, css, bhop, surf, server, gameserver, game, cs:go, csgo, addon, addons, package, scripting, plugins, script, sourcepawn, pawn, sp, download, dl"/>
<meta name="author" content="Miikka Ylätalo"/>
</head>
<body>
<div class="container">
	<div id="header-imgs">
		<a href="."><img id="header-logo" class="header-logo" src="img/influx_logo.png" alt="Influx"/></a>
	</div>
	<nav class="navbar navbar-expand-lg navbar-light bg-light inf-navbar">
		<ul class="navbar-nav text-uppercase">
			<li class="nav-item">
				<a class="nav-link inf-main-nav active" href="#">Download</a>
			</li>
			<li class="nav-item">
				<a class="nav-link inf-main-nav" href="steam://connect/servers.zmreborn.com:27016">Try it</a>
			</li>
			<li class="nav-item">
				<a class="nav-link inf-main-nav" href="guide.html">Guide</a>
			</li>
			<li class="nav-item">
				<a class="nav-link inf-main-nav" target="_blank" href="https://discord.gg/Mc5VDQT">Discord</a>
			</li>
			<li class="nav-item">
				<a class="nav-link inf-main-nav" href="index.html#donate">Donate</a>
			</li>
		</ul>
	</nav>

	<div class="inf-cont">
		<p class="mx-auto text-center display-4">Latest</p>
<?php
// Dev builds
$builds = $inf->getLatestBuilds( 'dev' );

// Latest dev build date
if ( $builds )
{
	$date = new DateTime( $builds[0]['builddate'] );
	echo '<p class="text-center">'.$date->format( 'Y-m-d' ).'</p>';
}
?>
		<table class="table table-sm table-striped mx-auto" style="max-width:99%">
			<thead class="inf-bg-myclr">
				<tr>
					<th class="text-center">#</th>
			  		<th>Commit Message</th>
					<th class="text-center">Package Download</th>
				</tr>
		  	</thead>
		  	<tbody>
<?php
//
// Dev builds
//
if ( $builds )
{
	foreach ( $builds as &$b )
	{
		echo '<tr>';

		// Build number
		echo '<th>' . $b['buildnum'] . '</th>';
		
		// Commit message
		$commitmsg = htmlspecialchars( $b['commitmsg'] );
		$commitlink = 'https://github.com/InfluxTimer/sm-timer/commit/'.$b['commithash'];

		echo '<td><a target="_blank" href="' . $commitlink . '">' . $commitmsg . '</a></td>';
		
		echo '<td class="text-center inf-wordspace-3">';
		
		// Download links
		$added = false;
		foreach ( $INF_BUILDVERSIONS as &$version )
		{
			if ( $version->bitflag & $b['verflags'] )
			{
				$version->printVersionLink( $b['buildnum'], $added );
			}
		}
		
		echo '</td>';
		echo '</tr>';
	}
}
else
{
	echo '<tr><th></th><td>Go tell Mehis that he\'s a lazy idiot.</td></tr>';
}
?>
			</tbody>
		</table>
	</div>
	<div class="inf-cont">
		<p class="mx-auto text-center display-4">Old Build</p>
		<p class="text-center inf-wordspace-10">
<?php
//
// Stable build
//
$b = $inf->getLatestStableBuild();

if ( $b )
{
	$date = new DateTime( $b['builddate'] );
	echo $date->format( 'Y-m-d' ).'<br>';

	$added = false;
	foreach ( $INF_BUILDVERSIONS as &$version )
	{
		if ( $version->bitflag & $b['verflags'] )
		{
			$version->printVersionLink( $b['buildnum'], $added );
		}
	}
}
else
{
	echo '<p class="text-center" style="word-spacing: 1px;">Go ahead and download one of the dev builds below :)</p>';
}
?>
		</p>
	</div>
	<div class="inf-cont">
		<p class="font-weight-light text-muted">Lite is a special version targeted for LAN usage, which strips all unnecessary content.</p>
		<p>Place the zip contents in your game directory (cstrike/csgo). Follow the <a href="guide.html">guide page</a> for further guidance.</p>
	</div>
	<div class="inf-cont">
		<h3 class="mx-auto text-center font-weight-light">Other plugins you might like</h3>
		<div style="display:flex; justify-content: center; align-items: center;">
			<div style="display: inline-block;">
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?t=310825">RNGFix (Slope boost &amp; bhop booster fix)</a><br>
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?p=2069582">Auto nav file generator</a><br>
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?p=808724">MP Bhop blocks (func_door bhop blocks)</a><br>
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?t=166468">Modify weapon speeds (260vel weapons)</a><br>
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?t=255298">CS:GO Movement Unlocker (prestrafing)</a><br>
				<a target="_blank" href="https://github.com/Franc1sco/FixHintColorMessages">CS:GO HUD fix</a><br>
				<a target="_blank" href="https://forums.alliedmods.net/showthread.php?t=320971">Surf Ramp Bug Fix</a><br>
			</div>
		</div>
	</div>
</div>
</body>
</html>