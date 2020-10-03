<?php
require "inc/connect.php";
require "inc/iconstats.php";
$currentTime = time();
$stmt = $conn->prepare("SELECT timer FROM iconScores");
$stmt->execute();
$result = $stmt->get_result();
$doThing = true;
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	if ($row["timer"] > $currentTime) {
		$doThing = false;
	}
}
if ($doThing) {
	$stmt = $conn->prepare("UPDATE iconScores SET timer = ?");
	$stmt->bind_param("i", $timerAdd);
	$timerAdd = $currentTime + $timeOffset;
	$stmt->execute();
	for ($gmType = 1; $gmType <= $gamemodeAmount; $gmType++) {
		switch ($gmType) {
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
		}
		for ($icon = 1; $icon <= $iconMax; $icon++) {
			$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM matchups WHERE isDecided = 1 AND gamemode = ? AND (firstChoice = ? OR secondChoice = ?)");
			$stmt->bind_param("sii", $getGamemode, $icon, $icon);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$matchupAmount = $row["count"];
			if ($matchupAmount >= $matchupMin) {
				$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM matchups WHERE isDecided = 1 AND gamemode = ? AND ((firstChoice = ? AND whoWon = 0) OR (secondChoice = ? AND whoWon = 1))");
				$stmt->bind_param("sii", $getGamemode, $icon, $icon);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				$percentageWon = 100 * $row["count"] / $matchupAmount;
				$stmt = $conn->prepare("SELECT * FROM iconScores WHERE gamemode = ? AND iconNumber = ?");
				$stmt->bind_param("si", $getGamemode, $icon);
				$stmt->execute();
				$result = $stmt->get_result();
				if ($result->num_rows > 0) {
					$stmt = $conn->prepare("UPDATE iconScores SET percentageWon = ? WHERE gamemode = ? AND iconNumber = ?");
					$stmt->bind_param("dsi", $percentageWon, $getGamemode, $icon);
					$stmt->execute();
				} else {
					$stmt = $conn->prepare("INSERT INTO iconScores (percentageWon, gamemode, iconNumber, timer) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("dsii", $percentageWon, $getGamemode, $icon, $timerAdd);
					$stmt->execute();
				}
			}
		}
	}
}
$stmt = $conn->prepare("SELECT * FROM iconScores");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
	$leaderboardEcho = "There is not enough data to rank icons";
} else {
	$leaderboardEcho = "<h3>BEST ICONS</h3><table>";
	$tableRow = [
		"<th></th>",
		"<td>#1</td>",
		"<td>#2</td>",
		"<td>#3</td>",
		"<td>#4</td>",
		"<td>#5</td>",
		"<td>#6</td>",
		"<td>#7</td>",
		"<td>#8</td>",
		"<td>#9</td>",
		"<td>#10</td>"
	];
	foreach ($iconArray as $iconStr) {
		$tableRow[0] .= "<th colspan='2'>".$iconStr."</th>";
		$stmt = $conn->prepare("SELECT percentageWon, iconNumber FROM iconScores WHERE gamemode = ? ORDER BY percentageWon DESC LIMIT 10");
		$stmt->bind_param("s", $iconStr);
		$stmt->execute();
		$result = $stmt->get_result();
		$temp = 1;
		while ($row = $result->fetch_assoc()) {
			$iconNumber = $row["iconNumber"];
			if ($iconNumber < 10) {
				$iconNumber = "0".$iconNumber;
			}
			$tableRow[$temp] .= "<td><img src=https://gdbrowser.com/gdicon/".$iconStr."-".$iconNumber.".png></td>";
			$tableRow[$temp] .= "<td>".$row["percentageWon"]."%</td>";
			$temp++;
		}
		if ($temp <= 10) {
			for ($r = $temp; $r <=10; $r++) {
				$tableRow[$r] .= "<td></td><td></td>";
			}
		}
		
	}
	foreach ($tableRow as $temprow) {
		$leaderboardEcho.= "<tr>".$temprow."</tr>";
	}
	$leaderboardEcho.= "</table>";
	
	$leaderboardEcho .= "<h3>WORST ICONS</h3><table>";
	$tableRow = [
		"<th></th>",
		"<td>#1</td>",
		"<td>#2</td>",
		"<td>#3</td>",
		"<td>#4</td>",
		"<td>#5</td>",
		"<td>#6</td>",
		"<td>#7</td>",
		"<td>#8</td>",
		"<td>#9</td>",
		"<td>#10</td>"
	];
	foreach ($iconArray as $iconStr) {
		$tableRow[0] .= "<th colspan='2'>".$iconStr."</th>";
		$stmt = $conn->prepare("SELECT percentageWon, iconNumber FROM iconScores WHERE gamemode = ? ORDER BY percentageWon LIMIT 10");
		$stmt->bind_param("s", $iconStr);
		$stmt->execute();
		$result = $stmt->get_result();
		$temp = 1;
		while ($row = $result->fetch_assoc()) {
			$iconNumber = $row["iconNumber"];
			if ($iconNumber < 10) {
				$iconNumber = "0".$iconNumber;
			}
			$tableRow[$temp] .= "<td><img src=https://gdbrowser.com/gdicon/".$iconStr."-".$iconNumber.".png></td>";
			$tableRow[$temp] .= "<td>".$row["percentageWon"]."%</td>";
			$temp++;
		}
		if ($temp <= 10) {
			for ($r = $temp; $r <=10; $r++) {
				$tableRow[$r] .= "<td></td><td></td>";
			}
		}
		
	}
	foreach ($tableRow as $temprow) {
		$leaderboardEcho.= "<tr>".$temprow."</tr>";
	}
	$leaderboardEcho.= "</table>";
}
?>
<html>
<head>
<title>rankig</title>
<style>
body {
	font-family: "Comic Sans MS", sans-serif;
	background-color: #fff;
}
table, th, td {
	border: 1px solid black;
	text-align: center;
	vertical-align: middle;
}
td {
	width: 100px;
	height: 100px;
}
td:first-child {
	width: 50px;
}
table {
	border-collapse: collapse;
}
</style>
<meta name="twitter:card" content="summary">
<meta name="twitter:creator" content="@summersolsta7">
<meta name="twitter:title" content="iconjudge - ranking geometry dash icons through your answers to icon matchups">
<meta name="twitter:image" content="https://summersolsta.7m.pl/presidentpulseicon.png">
</head>
<body>
<h2>what do people think?</h2>
<p><i>Icons are ranked based upon the percentage of matchups they win, not including draws. Note that an icon must be involved in at least <?php echo $matchupMin;?> matchups to be ranked.</i></p>
<?php echo $leaderboardEcho;?>
<p>images from gdcolon's <a href="https://gdbrowser.com">gdbrowser project</a> - <a href="iconjudge.php">judge icons yourself</a> - <a href="iconleaderboardfull.php">full leaderboard</a> - if you see ads i didn't put them there, use an adblocker if you can</p>
pester <a href='https://twitter.com/SummerSolsta7'>@summersolsta7</a> on twitter if you have any issues
</body>
</html>