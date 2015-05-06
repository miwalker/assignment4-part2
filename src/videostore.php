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

// displays all videos (no filter)
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
	$tracker = 1;
	$tracker2 = 0;
	// array containing video categories for dropdown menu and removing duplicates
	$catArray[0] = "All";
	// populates unfiltered table of video inventory
	while ($stmt->fetch()) {
		if ($out_rented == 1) {
			$rentedYesNo = "Checked Out";
		}
		else {
			$rentedYesNo = "Available";
		}
		echo  " <tr>
					<td>$out_name</td>
					<td>$out_category</td>
					<td>$out_length</td>
					<td>$rentedYesNo</td>
					<td>
						<form action=\"videostore.php\" method=\"post\">
 							<input type=\"hidden\" name=\"deleteSingle\" value=\"delete\">
 							<input type=\"hidden\" name=\"deleteSingleID\" value=\"$out_name\">
 							<input type=\"submit\" value=\"Delete\">
						</form>
					</td>
					<td>
						<form action=\"videostore.php\" method=\"post\">
 							<input type=\"hidden\" name=\"checkoutVid\" value=\"checkout\">
 							<input type=\"hidden\" name=\"checkoutVidID\" value=\"$out_name\">
 							<input type=\"submit\" value=\"Check-In/Check-Out\">
						</form>
					</td>
				</tr>";
		for ($i = 0; $i < count($catArray); $i++) {
			// verifies no duplicate categories are added to dropdown
			if ($catArray[$i] == $out_category || $out_category == "") {
				$tracker2++;
			}
		}
		// if no duplicates category is added to dropdown array
		if ($tracker2 == 0) {
			$catArray[count($catArray)] = $out_category;
			$tracker++;
		}
		$tracker2 = 0;
	}
	// creates and populates category filter drop down menu
	echo '<form action="videostore.php" method="post">
			<select name="displayCategoryName">';
	for ($ii = 0; $ii < $tracker; $ii++) {
		echo "<option value=\"$catArray[$ii]\">$catArray[$ii]</option>";
	}
	echo '	</select>
			<input type="hidden" name="displayCategory" value="display">
			<input type="submit" value="Filter">
		</form>';	
	$stmt->close();
	return;
}

// displays only the videos matching the category filter
function displayCategory($mysqli, $displayCategoryName) {
	if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM VideoStore WHERE category=\"$displayCategoryName\""))) {
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
	// populates filtered table
	while ($stmt->fetch()) {
		if ($out_rented == 1) {
			$rentedYesNo = "Checked Out";
		}
		else {
			$rentedYesNo = "Available";
		}
		echo  " <tr>
					<td>$out_name</td>
					<td>$out_category</td>
					<td>$out_length</td>
					<td>$rentedYesNo</td>
					<td>
						<form action=\"videostore.php\" method=\"post\">
 							<input type=\"hidden\" name=\"deleteSingle\" value=\"delete\">
 							<input type=\"hidden\" name=\"deleteSingleID\" value=\"$out_name\">
 							<input type=\"submit\" value=\"Delete\">
						</form>
					</td>
					<td>
						<form action=\"videostore.php\" method=\"post\">
 							<input type=\"hidden\" name=\"checkoutVid\" value=\"checkout\">
 							<input type=\"hidden\" name=\"checkoutVidID\" value=\"$out_name\">
 							<input type=\"submit\" value=\"Check-In/Check-Out\">
						</form>
					</td>
				</tr>";
	}
	// displays category form to unapply filter
	echo '<form action="videostore.php" method="post">
			<select name="displayCategoryName">
				<option value="All">All</option>
			</select>
			<input type="hidden" name="displayCategory" value="display">
			<input type="submit" value="Filter">
		</form>';
	$stmt->close();
	return;
}

// toggles status of video checked-in/checked-out
function toggleCheckOut($mysqli, $checkoutName) {
	if (!($stmt = $mysqli->prepare("UPDATE VideoStore SET rented = NOT(rented) WHERE name=\"$checkoutName\""))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}

	// executes statement if no errors
	if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	    	return;
		}
	$stmt->close();
	// notifies user operation was successful
	echo '*Video Status Updated*<br>';
	return;
}

function deleteVideo($mysqli, $deleteName) {
	if (!($stmt = $mysqli->prepare("DELETE FROM VideoStore WHERE name=\"$deleteName\""))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}

	// executes statement if no errors
	if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	    	return;
		}
	$stmt->close();
	// notifies user operation was successful
	echo '*Video Deleted*<br>';
	return;
}

// deletes all videos
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
	// notifies user operation was successful
	echo '*All Videos Deleted*<br>';
	return;
}

// *** END OF FUNCTIONS ***








// *** BEG OF MAIN ***

// displays Add Video Form
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

// initializes mysqli object
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "walkermi-db", "f87vujfeRS7iLV7o", "walkermi-db");
if (!$mysqli || $mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// If Add Video form submitted
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
	// if no invalid input entered add video functino is called
	if ($tracker == 0) {
		addVideo($mysqli, $_POST["name"], $_POST["category"], $lengthInt);
	}
}

// html to display table
echo '		<br>
			<h3>Current Inventory</h3>
			<table border="1">
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Length</th>
					<th>Status</th>
					<th>Delete Video</th>
					<th>Check-In/Check-Out Video</th>
				</tr>';

// if delete all button clicked
if ($_POST["deleteAll"] == "delete") {
	deleteALL($mysqli);
}
// if delete single video button clicked
if ($_POST["deleteSingle"] == "delete") {
	deleteVideo($mysqli, $_POST["deleteSingleID"]);
}
// if checkout button clicked
if ($_POST["checkoutVid"] == "checkout") {
	toggleCheckOut($mysqli, $_POST["checkoutVidID"]);
}
// if category filter is applied that does not equal all
if ($_POST["displayCategory"] == "display" && ($_POST["displayCategoryName"] != "All")) {
	displayCategory($mysqli, $_POST["displayCategoryName"]);
}
// displays all videos if no filter applied
else {
	displayVideos($mysqli);
}

// html for end of table and delete all form
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