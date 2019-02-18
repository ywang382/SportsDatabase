-- Stored procedures for filtering queries on database page
-- Some of the inputs parameters may be empty
DELIMITER //
DROP PROCEDURE IF EXISTS Filter_Fixtures //
CREATE PROCEDURE Filter_Fixtures(IN start_time VARCHAR(100), end_time VARCHAR(100), p_league_id VARCHAR(100), p_round VARCHAR(100),
									p_homeTeam VARCHAR(100), p_awayTeam VARCHAR(100), p_status VARCHAR(100),
									p_goalHomeTeam VARCHAR(100), p_goalAwayTeam VARCHAR(100), p_final_score VARCHAR(100))
	SELECT fixture_id, l.name, round, event_date, homeTeam, goalHomeTeam, awayTeam, goalAwayTeam, status, final_score
	FROM Fixtures as f, Leagues as l
	WHERE f.league_id = l.league_id and
			event_time >= IF(start_time = "", 0, start_time)
			and event_time <= IF(end_time = "", 9999999999, end_time)
			and l.name LIKE IF(p_league_id = "", l.name, CONCAT('%',p_league_id,'%'))
			and round LIKE IF(p_round = "", round, CONCAT('%- ', p_round))
			and homeTeam LIKE IF(p_homeTeam = "", homeTeam, CONCAT('%',p_homeTeam,'%'))
			and awayTeam LIKE IF(p_awayTeam = "", awayTeam, CONCAT('%',p_awayTeam,'%'))
			and status = IF(p_status = "", status, p_status)
			and goalHomeTeam = IF(p_goalHomeTeam = "", goalHomeTeam, p_goalHomeTeam)
			and goalAwayTeam = IF(p_goalAwayTeam = "", goalAwayTeam, p_goalAwayTeam)
			and final_score = IF(p_final_score = "", final_score, p_final_score)
	ORDER BY l.league_id, event_time DESC;
//

DROP PROCEDURE IF EXISTS Filter_Leagues //
CREATE PROCEDURE Filter_Leagues(IN p_name VARCHAR(100), p_country VARCHAR(100), p_season VARCHAR(100))
	SELECT *
	FROM Leagues
	WHERE name LIKE IF(p_name = "", name, CONCAT('%',p_name,'%')) and
		  country = IF(p_country = "", country, p_country) and
		  season = IF(p_season = "", season, p_season);
//

DROP PROCEDURE IF EXISTS Filter_Players //
CREATE PROCEDURE Filter_Players(IN p_team VARCHAR(100), p_pnumber VARCHAR(100), p_pname VARCHAR(100))
	SELECT s.team_name, p.pnumber, p.pname
	FROM Standings as s, Players as p
	WHERE s.team_id = p.team_id and
	      s.team_name LIKE IF(p_team = "", s.team_name, CONCAT('%',p_team,'%')) and
	      p.pnumber = IF(p_pnumber = "", p.pnumber, p_pnumber) and
	      p.pname LIKE IF(p_pname = "", p.pname, CONCAT('%',p_pname,'%'));
//

DROP PROCEDURE IF EXISTS Filter_Performance //
CREATE PROCEDURE Filter_Performance(IN winp_u VARCHAR(100), winp_b VARCHAR(100), drawp_u VARCHAR(100),
									drawp_b VARCHAR(100), losep_u VARCHAR(100), losep_b VARCHAR(100),
									GFavg_u VARCHAR(100), GFavg_b VARCHAR(100), GAavg_u VARCHAR(100),
									GAavg_b VARCHAR(100))
	SELECT team_name, winp, drawp, losep, GFavg, GAavg
	FROM AggregateStats
	WHERE winp <= IF(winp_u = "", winp, (0+winp_u)) and winp >= IF(winp_b = "", winp, (0+winp_b)) and
		  drawp <= IF(drawp_u = "", drawp, (0+drawp_u)) and drawp >= IF(drawp_b = "", drawp, (0+drawp_b)) and
		  losep <= IF(losep_u = "", losep, (0+losep_u)) and losep >= IF(losep_b = "", losep, (0+losep_b)) and
		  GFavg <= IF(GFavg_u = "", GFavg, (0+GFavg_u)) and GFavg >= IF(GFavg_b = "", GFavg, (0+GFavg_b)) and
		  GAavg <= IF(GAavg_u = "", GAavg, (0+GAavg_u)) and GAavg >= IF(GAavg_b = "", GAavg, (0+GAavg_b));
//

DROP PROCEDURE IF EXISTS Best_Team //
CREATE PROCEDURE Best_Team(IN league_name VARCHAR(100), min_max VARCHAR(100))
	SELECT L.name, S.team_name, S.win
	FROM Standings as S, Leagues as L
	WHERE L.league_id = S.league_id and
		  L.name = IF(league_name = "", L.name, league_name) and
		  IF(min_max = "MAX",
		  	 S.win >= ALL(
			  SELECT s.win
			  FROM Standings as s, Leagues as l
			  WHERE l.league_id = s.league_id and
		  			l.name = IF(league_name = "", l.name, league_name)
		    ),
			S.win <= ALL(
			  SELECT s.win
			  FROM Standings as s, Leagues as l
			  WHERE l.league_id = s.league_id and
		  			l.name = IF(league_name = "", l.name, league_name)
		    )
		  );
//

DROP PROCEDURE IF EXISTS Clean_Sheet //
CREATE PROCEDURE Clean_Sheet(IN league_name VARCHAR(100), min_max VARCHAR(100))
	SELECT L.name, S.team_name, CS
	FROM Standings as S, Leagues as L, CleanSheet as c
	WHERE L.league_id = S.league_id and c.team_name = S.team_name and
		  L.name = IF(league_name = "", L.name, league_name) and
		  IF(min_max = "MAX",
		  	CS >= ALL(
			  SELECT CS
			  FROM CleanSheet as c, Leagues as l, Standings as s
			  WHERE l.league_id = s.league_id and s.team_name = c.team_name and
		  			l.name = IF(league_name = "", l.name, league_name)
		    ),
				CS <= ALL(
				SELECT CS
	 			 FROM CleanSheet as c, Leagues as l, Standings as s
	 			 WHERE l.league_id = s.league_id and s.team_name = c.team_name and
	 					 l.name = IF(league_name = "", l.name, league_name)
		    )
		  );
//

DROP PROCEDURE IF EXISTS Worst_Team //
CREATE PROCEDURE Worst_Team(IN league_name VARCHAR(100), min_max VARCHAR(100))
	SELECT L.name, S.team_name, S.lose
	FROM Standings as S, Leagues as L
	WHERE L.league_id = S.league_id and
		  L.name = IF(league_name = "", L.name, league_name) and
		  IF(min_max = "MAX",
		  	 S.lose >= ALL(
			  SELECT s.lose
			  FROM Standings as s, Leagues as l
			  WHERE l.league_id = s.league_id and
		  			l.name = IF(league_name = "", l.name, league_name)
		    ),
			S.lose <= ALL(
			  SELECT s.lose
			  FROM Standings as s, Leagues as l
			  WHERE l.league_id = s.league_id and
		  			l.name = IF(league_name = "", l.name, league_name)
		    )
		  );
//


DELIMITER ;
