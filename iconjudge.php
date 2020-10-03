<?php
require "inc/connect.php";
session_start();
$currentTime = time();
require "inc/iconstats.php";
if (!isset($_SESSION["matchupCount"])) {
	$_SESSION["matchupCount"] = 0;
}
if (!isset($_SESSION["userToken"])) {
	$_SESSION["userToken"] = bin2hex(openssl_random_pseudo_bytes(64));
} else if (isset($_SESSION["matchupToken"])) {
	$getMatchupToken = $_SESSION["matchupToken"];
	$stmt = $conn->prepare("SELECT firstChoice, secondChoice, gamemode FROM matchups WHERE matchupToken = ? AND isDecided = 0");
	$stmt->bind_param("s", $getMatchupToken);
	$stmt->execute();
	$result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
		$getFirstChoice = $row["firstChoice"];
		$getSecondChoice = $row["secondChoice"];
		$getGamemode = $row["gamemode"];
	}
}
$getUserToken = $_SESSION["userToken"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["choice"])) {
		$inputChoice = $_POST["choice"];
	} else {
		$inputChoice = 0;
	}
	switch ($inputChoice) {
		case 1:
			$inputBool = 0;
			break;
		case 2:
			$inputBool = 1;
			break;
		default:
			echo "skipped (:";
			break;
	}
	$inputFirst = intval($_POST["iconOne"]);
	$inputSecond = intval($_POST["iconTwo"]);
	$inputGamemode = $_POST["gamemode"];
	$inputMatchupToken = $_POST["matchupToken"];
	$stmt = $conn->prepare("SELECT matchupID FROM matchups WHERE matchupToken = ? AND isDecided = 0 AND firstChoice = ? AND secondChoice = ? AND gamemode = ?");
	$stmt->bind_param("siis", $inputMatchupToken, $inputFirst, $inputSecond, $inputGamemode);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows == 1) {
		if ($inputChoice > 0) {
			$row = $result->fetch_assoc();
			$getMatchupID = $row["matchupID"];
			$stmt = $conn->prepare("UPDATE matchups SET isDecided = 1, whoWon = ?, userToken = ?, timer = ? WHERE matchupID = ?");
			$stmt->bind_param("isii", $inputBool, $getUserToken, $currentTime, $getMatchupID);
			$stmt->execute();
			echo "did (:";
			$_SESSION["matchupCount"]++;
		}
	} else {
		if ($inputChoice > 0) {
			$inputChoiceStr = strval($inputChoice);
			$stmt = $conn->prepare("INSERT INTO triedAndFailed (matchupToken, userToken, firstChoice, secondChoice, gamemode, whoWon) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("ssiissi", $inputMatchupToken, $getUserToken, $inputFirst, $inputSecond, $inputGamemode, $inputChoiceStr);
			if(!$stmt->execute()) echo $stmt->error;
			echo "did :)";
			$_SESSION["matchupCount"]++;
		}
	}
	unset($getFirstChoice);
	unset($getSecondChoice);
	unset($getGamemode);
}
if (isset($getGamemode)) {
	if ($getFirstChoice < 10) {
		$getFirstChoice = "0".$getFirstChoice;
	}
	if ($getSecondChoice < 10) {
		$getSecondChoice = "0".$getSecondChoice;
	}
	$randomIconImageOne = "https://gdbrowser.com/gdicon/".$getGamemode."-".$getFirstChoice.".png";
	$randomIconImageTwo = "https://gdbrowser.com/gdicon/".$getGamemode."-".$getSecondChoice.".png";
} else {
	$iconMax = $cubeMax;
	$getGamemode = $cubeStr;
	/*
	switch (rand(1,9)) { // should be (1,7), cubes are boosted for now
		case 1:
			$iconMax = $cubeMax;
			$getGamemode = $cubeStr;
			break;
		case 2:
			$iconMax = $shipMax;
			$getGamemode = $shipStr;
			break;
		case 3:
			$iconMax = $ballMax;
			$getGamemode = $ballStr;
			break;
		case 4:
			$iconMax = $ufoMax;
			$getGamemode = $ufoStr;
			break;
		case 5:
			$iconMax = $waveMax;
			$getGamemode = $waveStr;
			break;
		case 6:
			$iconMax = $robotMax;
			$getGamemode = $robotStr;
			break;
		case 7:
			$iconMax = $spiderMax;
			$getGamemode = $spiderStr;
			break;
		default: // you shouldn't be able to get here
			$iconMax = $cubeMax;
			$getGamemode = $cubeStr;
			break; 
	}*/
	$random = rand(1, array_sum($maxArray));
	$temp = 0;
	foreach ($maxArray as $count) {
		if ($random <= $count) {
			$iconMax = $count;
			$getGamemode = $iconArray[$temp];
			break;
		}
		$random -= $count;
		$temp++;
	}
	$getFirstChoice = rand(1,$iconMax);
	if ($getFirstChoice < 10) {
		$getFirstChoice = "0".$getFirstChoice;
	}
	$randomIconImageOne = "https://gdbrowser.com/gdicon/".$getGamemode."-".$getFirstChoice.".png";
	$getSecondChoice = rand(1,($iconMax-1));
	if ($getSecondChoice >= $getFirstChoice) {
		$getSecondChoice++;
	}
	if ($getSecondChoice < 10) {
		$getSecondChoice = "0".$getSecondChoice;
	}
	$randomIconImageTwo = "https://gdbrowser.com/gdicon/".$getGamemode."-".$getSecondChoice.".png";
	
	$_SESSION["matchupToken"] = bin2hex(openssl_random_pseudo_bytes(64));
	$getMatchupToken = $_SESSION["matchupToken"];
	$stmt = $conn->prepare("INSERT INTO matchups (matchupToken, userToken, firstChoice, secondChoice, gamemode, timer) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssiisi", $getMatchupToken, $getUserToken, $getFirstChoice, $getSecondChoice, $getGamemode, $currentTime);
	if(!$stmt->execute()) echo $stmt->error;
}
?>
<html>
<head>
<title>judgig</title>
<style>
body {
	font-family: "Comic Sans MS", sans-serif;
	background-color: #fff;
}
</style>
<meta name="twitter:card" content="summary">
<meta name="twitter:creator" content="@summersolsta7">
<meta name="twitter:title" content="iconjudge - ranking geometry dash icons through your answers to icon matchups">
<meta name="twitter:image" content="https://summersolsta.7m.pl/presidentpulseicon.png">
</head>
<body>
<br><?php
if ($_SESSION["matchupCount"] == 1) {
	echo "you have completed 1 matchup";
} else if ($_SESSION["matchupCount"] > 1) {
	echo "you have completed ".$_SESSION["matchupCount"]." matchups";
}
?>
<h2>which is better</h2>
<p><i>matchups are randomly generated, and are not dependent on the previous matchup. there is no limit to the amount of matchups you can do.</i></p>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p><label><input type="radio" name="choice" value="1"> <img src="<?php echo $randomIconImageOne;?>"></p></label>
<p><label><input type="radio" name="choice" value="2"> <img src="<?php echo $randomIconImageTwo;?>"></p></label>
<p><label><input type="radio" name="choice" value="0"> Neither</p></label>
<input type="hidden" name="iconOne" value="<?php echo $getFirstChoice;?>">
<input type="hidden" name="iconTwo" value="<?php echo $getSecondChoice;?>">
<input type="hidden" name="gamemode" value="<?php echo $getGamemode;?>">
<input type="hidden" name="matchupToken" value="<?php echo $getMatchupToken;?>">
<input type="submit">
</form>
<p>images from gdcolon's <a href="https://gdbrowser.com">gdbrowser project</a> - <a href="iconleaderboard.php">view leaderboard</a> - if you see ads i didn't put them there, use an adblocker if you can - </p>
pester <a href='https://twitter.com/SummerSolsta7'>@summersolsta7</a> on twitter if you have any issues
</body>
</html>