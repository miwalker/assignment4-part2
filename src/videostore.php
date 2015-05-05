<?php 

function addVideo($mysqli, $vidName, $vidCategory, $vidLength) {
	// category and length empty
	if ($vidCategory == "" && $vidLength == 0) {
		if (!($stmt = $mysqli->prepare("INSERT INTO VideoStore (name) VALUES (?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		if (!$stmt->bind_param("s", $vidName)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return;
		}
	}
	
	// category empty
	elseif ($vidCategory == "") {
		if (!($stmt = $mysqli->prepare("INSERT INTO VideoStore(name, length) VALUES(?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		if (!$stmt->bind_param("si", $vidName, $vidLength)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return;
		}
	}

	// length empty
	elseif ($vidLength == 0) {
		if (!($stmt = $mysqli->prepare("INSERT INTO VideoStore(name, category) VALUES(?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		if (!$stmt->bind_param("ss", $vidName, $vidCategory)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return;
		}
	}
	
	// no fields empty
	else {
		if (!($stmt = $mysqli->prepare("INSERT INTO VideoStore(name, length, category) VALUES(?, ?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		if (!$stmt->bind_param("sis", $vidName, $vidLength, $vidCategory)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return;
		}
	}

	// executes statement if no errors
	if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	    	return;
		}
	$stmt->close();
	echo 'Video Added Successfully<br>';
	return;
}

function displayVideos($mysqli) {
	if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM VideoStore"))) {
    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    	return;
	}

	if (!$stmt->execute()) {
    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    	return;
	}

	$out_name = NULL;
	$out_category = NULL;
	$out_length = NULL;
	$out_rented = NULL;
	if (!$stmt->bind_result($out_name, $out_category, $out_length, $out_rented)) {
    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    	return;
	}
	$rentedYesNo;
	while ($stmt->fetch()) {
		if ($out_rented == 1) {
			$rentedYesNo = "Yes";
		}
		else {
			$rentedYesNo = "No";
		}
		echo  " <tr>
					<td>$out_name</td>
					<td>$out_category</td>
					<td>$out_length</td>
					<td>$rentedYesNo</td>
					<td>$rentedYesNo</td>
				</tr>";
		$tracker++;
	}
	$stmt->close();
	return;
}

function toggleCheckOut($mysqli) {

}

function deleteVideo($mysqli) {

}

function deleteALL($mysqli) {
	if (!($stmt = $mysqli->prepare("DELETE FROM VideoStore WHERE id>0"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}

	// executes statement if no errors
	if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	    	return;
		}
	$stmt->close();
	echo '*All Videos Deleted*<br>';
	return;
}



echo '<html>
		<head>
  			<title>Assignment 4-2 Videostore</title>
 		</head>
	
 		<body>
 			<h3>Add Video</h3>
 			<form action="videostore.php" method="post">
 				<p>Name <input type="text" name="name"></p>
 				<p>Category <input type="text" name="category"></p>
 				<p>Length <input type="text" name="length"></p>
 				<p><input type="submit" value="Add"></p>
			</form>';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "walkermi-db", "f87vujfeRS7iLV7o", "walkermi-db");
if (!$mysqli || $mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if (count($_POST) == 3) {
	$tracker = 0;
	if ($_POST["name"] == "") {
		echo '"Name" is a required field<br>';
		$tracker++;
	}
	if (!is_numeric($_POST["length"]) && ($_POST["length"] != "")) {
		echo '"Length" must be an integer<br>';
		$tracker++;
	}
	$lengthInt = (int)$_POST["length"];
	if ($lengthInt < 1 && ($_POST["length"] != "")) {
		echo '"Length" must be a positive integer';
		$tracker++;
	}
	if ($tracker == 0) {
		addVideo($mysqli, $_POST["name"], $_POST["category"], $lengthInt);
	}
}

echo '		<br>
			<h3>Current Inventory</h3>
			<table border="2">
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Length</th>
					<th>Rented</th>
				</tr>';


if ($_POST["deleteAll"] == "delete") {
	deleteALL($mysqli);
}
displayVideos($mysqli);

echo '		</table>
			
			<br>
			<h3>Delete All Videos</h3>
			<form action="videostore.php" method="post">
 				<input type="hidden" name="deleteAll" value="delete">
 				<p><input type="submit" value="Delete"></p>
			</form>
 		</body>
 	</html>';










?>