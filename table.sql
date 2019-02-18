-- User information
CREATE TABLE users (
  username varchar(100) NOT NULL PRIMARY KEY,
  email varchar(100) NOT NULL,
  password varchar(100) NOT NULL,
  credit INT NOT NULL,
  CHECK (credit >= 0)
);

-- Each user's favorite teams
CREATE TABLE Likes_Teams (
  user varchar(100) NOT NULL,
  team varchar(100) NOT NULL,
  CONSTRAINT no_duplicate UNIQUE(user,team),
  CONSTRAINT fk FOREIGN KEY (user) REFERENCES users (username)
    ON DELETE CASCADE
)

-- Requested bets
CREATE TABLE Bet_Requests (
  request_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  requester varchar(100) NOT NULL,
  winSide varchar(100) NOT NULL,
  requested varchar(100) NOT NULL,
  bet_amount INT NOT NULL,
  fixture_id INT NOT NULL,
  fixture_time INT NOT NULL CHECK(fixture_time > UNIX_TIMESTAMP()),
  CONSTRAINT no_duplicate UNIQUE(requester, winSide, requested, fixture_id),
  CONSTRAINT fk1 FOREIGN KEY (requester) REFERENCES users (username) ON DELETE CASCADE,
  CONSTRAINT fk2 FOREIGN KEY (requested) REFERENCES users (username) ON DELETE CASCADE
);

-- Placed bets between two users. winSide is either 'home' or 'away'
CREATE TABLE Bets (
  bet_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user1 varchar(100) NOT NULL,
  winSide1 varchar(100) NOT NULL,
  user2 varchar(100) NOT NULL,
  winSide2 varchar(100) NOT NULL,
  bet_amount INT NOT NULL,
  fixture_id INT NOT NULL,
  CONSTRAINT fk1 FOREIGN KEY (user1) REFERENCES users (username) ON DELETE CASCADE,
  CONSTRAINT fk2 FOREIGN KEY (user2) REFERENCES users (username) ON DELETE CASCADE
);

-- Credit transaction records between users after a betted match is finished.
-- Used to keep track of past transaction history
CREATE TABLE Bet_Results(
  fromUser varchar(100) NOT NULL,
  toUser varchar(100) NOT NULL,
  amount INT NOT NULL,
  time INT NOT NULL,
  CONSTRAINT fk1 FOREIGN KEY (fromUser) REFERENCES users (username) ON DELETE CASCADE,
  CONSTRAINT fk2 FOREIGN KEY (toUser) REFERENCES users (username) ON DELETE CASCADE
);

-- Supported leagues in the database
CREATE TABLE Leagues (
  league_id INT NOT NULL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  country VARCHAR(100) NOT NULL,
  season INT NOT NULL,
  season_start VARCHAR(100) NOT NULL,
  season_end VARCHAR(100) NOT NULL
);

-- Matches, including teams, dates, scores...
CREATE TABLE Fixtures (
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
);

-- Standings of all leagues together.
-- Including teams and associated stats like wins, draws, loses...
-- Either team_name or team_id can uniquely determine a team, id is used for API calls
CREATE TABLE Standings (
  league_id INT NOT NULL,
  team_name VARCHAR(100),
  team_id INT NOT NULL NOT NULL PRIMARY KEY,
  played INT NOT NULL,
  win INT NOT NULL,
  draw INT NOT NULL,
  lose INT NOT NULL,
  goalsFor INT NOT NULL,
  goalsAgainst INT NOT NULL,
  goalsDiff INT NOT NULL,
  points INT NOT NULL,
  CONSTRAINT fk FOREIGN KEY (league_id) REFERENCES Leagues (league_id) ON DELETE CASCADE
);

-- Players, their team, and their shirt numbers
-- Coaches are included with the shirt number as 'Coach'
CREATE TABLE Players (
  team_id INT NOT NULL,
  pnumber varchar(100) NOT NULL,
  pname varchar(100) NOT NULL PRIMARY KEY,
  CONSTRAINT fk FOREIGN KEY (team_id) REFERENCES Standing (team_id) ON DELETE CASCADE,
);

-- The following table are not filled with data due to constraint on the number of API calls allowed

-- In match events, including goals, yellow cards, injuries..
CREATE TABLE MatchEvents (
  event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fixture_id INT NOT NULL,
  event_time INT NOT NULL,
  team_name varchar(100) NOT NULL,
  player varchar(100) NOT NULL,
  type varchar(100) NOT NULL,
  detail varchar(200) NOT NULL,
  CONSTRAINT fk FOREIGN KEY (fixture_id) REFERENCES Fixtures (fixture_id)
    ON DELETE CASCADE
);

-- Squad lineup before kick-off of each match
CREATE TABLE LineUps (
  fixture_id INT NOT NULL,
  team_name varchar(100) NOT NULL,
  formation varchar(100) NOT NULL,
  coach varchar(200) NOT NULL,
  captain varchar(100) NOT NULL,
  starting11 varchar(1000) NOT NULL,
  substitutes varchar(1000) NOT NULL,
  CONSTRAINT fk FOREIGN KEY (fixture_id) REFERENCES Fixtures (fixture_id)
    ON DELETE CASCADE
);

-- Detailed stats for each team
CREATE TABLE TeamStats (
  team_id INT NOT NULL PRIMARY KEY,
  homeWins INT NOT NULL,
  awayWins INT NOT NULL,
  homeLoses INT NOT NULL,
  awayLoses INT NOT NULL,
  homeDraws INT NOT NULL,
  awayDraws INT NOT NULL,
  homeGoalsFor INT NOT NULL,
  awayGoalsFor INT NOT NULL,
  homeGoalsAgainst INT NOT NULL,
  awayGoalsAgainst INT NOT NULL,
  CONSTRAINT fk FOREIGN KEY (team_id) REFERENCES Standings (team_id)
    ON DELETE CASCADE
);

-- Pre-match betting odds for different outcomes
CREATE TABLE Odds (
  fixture_id INT NOT NULL PRIMARY KEY,
  homeWin FLOAT NOT NULL,
  awayWin FLOAT NOT NULL,
  draw FLOAT NOT NULL,
  both_teams_score FLOAT NOT NULL,
  over_1_goal FLOAT NOT NULL,
  over_2_goals FLOAT NOT NULL,
  over_3_goals FLOAT NOT NULL,
  CONSTRAINT fk FOREIGN KEY (fixture_id) REFERENCES Fixtures (fixture_id)
    ON DELETE CASCADE
);

-- Defined triggers which we were planning to implement
-- Delete expired bet requests
DELIMITER //
CREATE TRIGGER bet_expired
BEFORE UPDATE ON Bet_Requests FOR EACH ROW
BEGIN
  DELETE FROM Bet_Requests
  WHERE fixture_time <= UNIX_TIMESTAMP();
END; //

-- Delete users with a balance of lower than 5 credits
CREATE TRIGGER low_balance
BEFORE UPDATE ON users FOR EACH ROW
BEGIN
  IF NEW.credit < 5 THEN
    DELETE FROM users as u, Likes_Teams as l, Bet_Requests as br, Bets as b, Bet_Results as bb
    WHERE NEW.credit < 5 AND u.username = l.user
      AND (u.username = br.requester or u.username = b.requested)
      AND (u.username = b.user1 or u.username = b.user2)
      AND (u.username = bb.fromUser or u.username = bb.toUser);
  END IF;
END; //

DELIMITER ;
