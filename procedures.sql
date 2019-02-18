-- Useful views. These are used for some of the stored procedures below
CREATE OR REPLACE VIEW TeamForm AS
(SELECT fixture_id, event_time, "W" as homeTeamForm, "L" as awayTeamForm
FROM Fixtures
WHERE goalHomeTeam > goalAwayTeam)
UNION
(SELECT fixture_id, event_time, "L" as homeTeamForm, "W" as awayTeamForm
FROM Fixtures
WHERE goalAwayTeam > goalHomeTeam)
UNION
(SELECT fixture_id, event_time, "D" as homeTeamForm, "D" as awayTeamForm
FROM Fixtures
WHERE goalHomeTeam = goalAwayTeam and status = "Match Finished");

CREATE OR REPLACE VIEW AggregateStats AS
SELECT team_name, round(win/played*100, 2) as winp, round(draw/played*100, 2) as drawp, round(lose/played*100, 2) as losep,
				round(goalsFor/played, 2) as GFavg, round(goalsAgainst/played, 2) as GAavg
FROM Standings;

CREATE OR REPLACE VIEW CleanSheet AS
SELECT team_name, count(DISTINCT fixture_id) as CS
FROM Fixtures as f, Standings as s
WHERE (s.team_name = f.homeTeam and f.goalAwayTeam = 0 and f.status = "Match Finished") OR
			(s.team_name = f.awayTeam and f.goalHomeTeam = 0 and f.status = "Match Finished")
GROUP BY team_name;


DELIMITER //

DROP PROCEDURE IF EXISTS FindUser //
CREATE PROCEDURE FindUser(IN uname varchar(100), mail varchar(100))
	SELECT * FROM users WHERE username=uname OR email=mail LIMIT 1;
//

DROP PROCEDURE IF EXISTS SearchUser //
CREATE PROCEDURE SearchUser(IN uname varchar(100), mail varchar(100))
SELECT username FROM users
WHERE UPPER(username) LIKE IF(uname = "", UPPER(username), UPPER(CONCAT('%',uname, '%'))) AND
			email = IF(mail = "", email, mail);
//

DROP PROCEDURE IF EXISTS RegisterUser //
CREATE PROCEDURE RegisterUser(IN uname varchar(100), mail varchar(100), pw varchar(100))
	INSERT INTO users(username, email, password) VALUES(uname, mail, pw);

DROP PROCEDURE IF EXISTS LoginUser //
CREATE PROCEDURE LoginUser(IN uname varchar(100), pw varchar(100))
	SELECT username FROM users
  WHERE username = uname and password = pw;
//

DROP PROCEDURE IF EXISTS GetMatch //
CREATE PROCEDURE GetMatch(IN tstart INT, tend INT)
	SELECT name, event_date, homeTeam, awayTeam, status, final_score
	FROM Leagues as l, Fixtures as f
	WHERE l.league_id = f.league_id and event_time >= tstart and event_time <= tend
	ORDER BY event_date;
//

DROP PROCEDURE IF EXISTS GetStanding //
CREATE PROCEDURE GetStanding(IN id INT)
	SELECT team_name, played, win, draw, lose, goalsFor, goalsAgainst, goalsDiff, points
	FROM Standings
	WHERE league_id = id;
//

DROP PROCEDURE IF EXISTS GetUsersTeams //
CREATE PROCEDURE GetUsersTeams(IN uname varchar(100))
	SELECT team_name, name, country
	FROM Standings as s, Leagues as l
	WHERE s.league_id = l.league_id and team_name in
		(SELECT team FROM Likes_Teams WHERE user = uname);
//

DROP PROCEDURE IF EXISTS GetSquad //
CREATE PROCEDURE GetSquad(IN tname varchar(100))
	SELECT pnumber, pname
	FROM Players as p, Standings as s
	WHERE p.team_id = s.team_id AND s.team_name = tname;
//

DROP PROCEDURE IF EXISTS GetFutureMatch //
CREATE PROCEDURE GetFutureMatch(IN tname varchar(100), IN curtime INT)
	SELECT fixture_id, event_date, homeTeam, awayTeam, status
	FROM Fixtures
	WHERE event_time > curtime AND (homeTeam = tname or awayTeam = tname)
	ORDER BY event_date;
//

DROP PROCEDURE IF EXISTS GetRecentMatch //
CREATE PROCEDURE GetRecentMatch(IN tname varchar(100))
SELECT fixture_id, event_date, homeTeam, awayTeam, final_score
FROM Fixtures WHERE status = "Match Finished"
	AND (homeTeam = tname or awayTeam = tname)
ORDER BY event_time DESC;
//

DROP PROCEDURE IF EXISTS GetMoreStats //
CREATE PROCEDURE GetMoreStats(IN tname varchar(100))
SELECT played, win, winp, draw, drawp, lose, losep, CS, GFavg, GAavg, S.points
FROM Standings as S, AggregateStats as agg, CleanSheet as c
WHERE S.team_name = tname AND S.team_name = agg.team_name AND agg.team_name = c.team_name
//

DROP PROCEDURE IF EXISTS Form //
CREATE PROCEDURE Form(IN tname varchar(100))
SELECT F FROM
(SELECT homeTeamForm as F, f.event_time as T FROM TeamForm as t, Fixtures as f
WHERE t.fixture_id = f.fixture_id and f.homeTeam = tname
UNION
SELECT awayTeamForm as F, f.event_time as T FROM TeamForm as t, Fixtures as f
WHERE t.fixture_id = f.fixture_id and f.awayTeam = tname) FormTable
ORDER BY T DESC;
//

DROP PROCEDURE IF EXISTS ShowBetRequests //
CREATE PROCEDURE ShowBetRequests(IN rqter varchar(100))
SELECT request_id, bet_amount, requested, IF(winSide='home', homeTeam, awayTeam),
			f.fixture_id, event_date, homeTeam, awayTeam, status
FROM Bet_Requests as b, Fixtures as f
WHERE b.fixture_id = f.fixture_id and b.requester = rqter;
//

DROP PROCEDURE IF EXISTS ShowRequestedBets //
CREATE PROCEDURE ShowRequestedBets (IN rqted varchar(100))
SELECT request_id, bet_amount, requester, IF(winSide='home', awayTeam, homeTeam),
			f.fixture_id, event_date, homeTeam, awayTeam, status
FROM Bet_Requests as b, Fixtures as f
WHERE b.fixture_id = f.fixture_id and b.requested = rqted;
//

DROP PROCEDURE IF EXISTS PlaceBets //
CREATE PROCEDURE PlaceBets (IN rid INT)
BEGIN
INSERT INTO Bets (user1, winSide1, user2, winSide2, bet_amount, fixture_id)
SELECT requester, winSide, requested, IF(winSide="home", "away", "home"),
				bet_amount, fixture_id
FROM Bet_Requests as b
WHERE b.request_id = rid;
DELETE FROM Bet_Requests WHERE request_id = rid;
END //

DROP PROCEDURE IF EXISTS ShowBets //
CREATE PROCEDURE ShowBets (IN uname varchar(100))
SELECT bet_id, bet_amount,IF(uname=user1, user2, user1) as opponent,
			IF(uname=user1, IF(winSide1='home', homeTeam, awayTeam), IF(winSide2='home', homeTeam, awayTeam)) as winTeam,
      f.fixture_id, event_date,	homeTeam, awayTeam, status
FROM Bets as b, Fixtures as f
WHERE b.fixture_id = f.fixture_id AND (uname = user1 OR uname = user2);
//

DROP PROCEDURE IF EXISTS ShowTransact //
CREATE PROCEDURE ShowTransact(IN uname varchar(100))
SELECT fixture_id, time, amount, fromUser, toUser, IF(uname=fromUser, "Lose", "Win") as status
FROM Bet_Results
WHERE uname = fromUser or uname = toUser
ORDER BY time DESC;
//
DELIMITER ;
