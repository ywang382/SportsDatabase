<?php
function retrieve_leagues($db) {
	// request leagues data
	$uri = 'https://api-football-v1.p.rapidapi.com/leagues';
	$reqPrefs['http']['method'] = 'GET';
	$reqPrefs['http']['header'] = 'X-RapidAPI-Key: mGtlL0dL8QmshkBRwZ3bt92eIEssp13FVdSjsnbYn3JXpJAAXI';
	$stream_context = stream_context_create($reqPrefs);
	$response = file_get_contents($uri, false, $stream_context);
	$leagues = json_decode($response, true);
	$array_leagues = $leagues["api"]["leagues"];

 	// clear leagues table
 	$drop_leagues_sql = "DROP TABLE IF EXISTS Leagues;";
 	mysqli_query($db, $drop_leagues_sql);

 	// sql create leagues table
 	$leagues_sql = "CREATE TABLE Leagues (
						league_id INT NOT NULL PRIMARY KEY,
					    name VARCHAR(100) NOT NULL,
					    country VARCHAR(100) NOT NULL,
					    season VARCHAR(100) NOT NULL,
					    season_start VARCHAR(100) NOT NULL,
					    season_end VARCHAR(100) NOT NULL
						);";

	 mysqli_query($db, $leagues_sql);

	// populate leagues
	foreach ($array_leagues as $row) {
		$sql = "INSERT INTO Leagues
				VALUES ('" . $row["league_id"] ."', '" . $row["name"] ."', '" . $row["country"] ."',
			    '" . $row["season"] ."', '" . $row["season_start"] ."', '" . $row["season_end"] ."')";
		mysqli_query($db, $sql);
	}
}

function retrieve_fixtures($db) {
	$fixture_list = array("2","8","94","87","4");
	// clear fixtures table
 	$drop_fixtures_sql = "DROP TABLE IF EXISTS Fixtures;";
 	mysqli_query($db, $drop_fixtures_sql);

 	// sql create fixtures table
 	$fixtures_sql = "CREATE TABLE Fixtures (
						fixture_id INT NOT NULL PRIMARY KEY,
						event_time INT NOT NULL,
					    event_date VARCHAR(100) NOT NULL,
					    league_id INT NOT NULL,
					    round VARCHAR(100) NOT NULL,
					    homeTeam VARCHAR(100) NOT NULL,
					    awayTeam VARCHAR(100) NOT NULL,
					    status VARCHAR(100) NOT NULL,
					    goalHomeTeam INT NOT NULL,
					    goalAwayTeam INT NOT NULL,
					    final_score VARCHAR(100) NOT NULL
						);";
	mysqli_query($db, $fixtures_sql);

	foreach($fixture_list as $key_id) {
		// request fixtures data
		$uri = 'https://api-football-v1.p.rapidapi.com/fixtures/league/' . $key_id;
		$reqPrefs['http']['method'] = 'GET';
		$reqPrefs['http']['header'] = 'X-RapidAPI-Key: mGtlL0dL8QmshkBRwZ3bt92eIEssp13FVdSjsnbYn3JXpJAAXI';
		$stream_context = stream_context_create($reqPrefs);
		$response = file_get_contents($uri, false, $stream_context);
		$fixtures = json_decode($response, true);
		$array_fixtures = $fixtures["api"]["fixtures"];

		// populate fixtures
		foreach ($array_fixtures as $row) {
			$sql = "INSERT INTO Fixtures
					VALUES ('" . $row["fixture_id"] ."', '" . $row["event_timestamp"] ."', '" . $row["event_date"] ."', '" . $row["league_id"] ."',
				    '" . $row["round"] ."', '" . $row["homeTeam"] ."', '" . $row["awayTeam"] ."', '" . $row["status"] ."',
				    '" . $row["goalsHomeTeam"] ."', '" . $row["goalsAwayTeam"] ."', '" . $row["final_score"] ."')";
			mysqli_query($db, $sql);
		}
	}
}

function retrieve_standings($db) {
	$standings_list = array("2","8","94","87","4");
	// clear standings table
 	$drop_standings = "DROP TABLE IF EXISTS Standings;";
 	mysqli_query($db, $drop_standings);

 	// sql create standings table
 	$standings_sql = "CREATE TABLE Standings (
							league_id INT NOT NULL,
							team_name VARCHAR(100) NOT NULL,
							team_id INT NOT NULL PRIMARY KEY,
					    played INT NOT NULL,
					    win INT NOT NULL,
							draw INT NOT NULL,
							lose INT NOT NULL,
							goalsFor INT NOT NULL,
							goalsAgainst INT NOT NULL,
							goalsDiff INT NOT NULL,
							points INT NOT NULL
						);";
	mysqli_query($db, $standings_sql);

	foreach($standings_list as $key_id) {
		// request standings data
		$uri = 'https://api-football-v1.p.rapidapi.com/standings/' . $key_id;
		$reqPrefs['http']['method'] = 'GET';
		$reqPrefs['http']['header'] = 'X-RapidAPI-Key: mGtlL0dL8QmshkBRwZ3bt92eIEssp13FVdSjsnbYn3JXpJAAXI';
		$stream_context = stream_context_create($reqPrefs);
		$response = file_get_contents($uri, false, $stream_context);
		$standings = json_decode($response, true);
		$array_standings = $standings["api"]["standings"];

		// populate standings
		foreach ($array_standings as $row) {
			$sql = "INSERT INTO Standings
					VALUES ('" . $row["league_id"] ."', '" . $row["teamName"] ."', '" . $row["team_id"] ."', '" . $row["play"] ."',
				    '" . $row["win"] ."', '" . $row["draw"] ."', '" . $row["lose"] ."', '" . $row["goalsFor"] ."',
				    '" . $row["goalsAgainst"] ."', '" . $row["goalsDiff"] ."', '" . $row["points"] ."')";
			mysqli_query($db, $sql);
		}
	}
}

function retrieve_players($db) {
	$teams = array();
	$result = mysqli_query($db, "SELECT team_id FROM Standings;");

	while($row = $result->fetch_row()){
		array_push($teams, $row[0]);
	}
	foreach($teams as $id) {
		// request players data
		$uri = 'https://api-football-v1.p.rapidapi.com/players/2018/'.$id;
		$reqPrefs['http']['method'] = 'GET';
		$reqPrefs['http']['header'] = 'X-RapidAPI-Key: mGtlL0dL8QmshkBRwZ3bt92eIEssp13FVdSjsnbYn3JXpJAAXI';
		$stream_context = stream_context_create($reqPrefs);
		$response = file_get_contents($uri, false, $stream_context);
		$players = json_decode($response, true);
		$array_players = $players["api"]["players"];
		$coach = $players["api"]["coachs"][0];
		mysqli_query($db, "INSERT INTO Players VALUES ('$id', 'Coach', '" .$coach."');");

		foreach ($array_players as $row) {
			$sql = "INSERT INTO Players VALUES ('$id', '" . $row["number"] ."', '" . $row["player"] ."')";
			mysqli_query($db, $sql);
		}
	}
}
?>
