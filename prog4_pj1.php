<?php 
	//OPEN PATH TO SQLite DATABASE
	
	define("PATH_TO_DB","thinkingSpaces.sqlite"); // database filepath 

	function open_db($path){
		try{
			if(file_exists($path)){
				// open DB
				$db = new PDO('sqlite:' . $path);
				$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			} else {
				echo "<strong>file not found at: \"" . $path . "\"</strong>";
				die;
			} // end if
			
		}catch(PDOException $e){
			echo '<strong>PDOException : ' . $e->getMessage() . " for file name: \"" . $path . "</strong>";
			die;
		} // end try
		
		return $db;
	}
	
	//
	// Gets the next set of 10 clouds
	//
	function getRows($num) { 
		//$num is the ID to compare to
		//$arrow is the greater than or less than symbol
		
		//open path to db
		$db = open_db(PATH_TO_DB);
				
		//Prepare query
		$sql = "SELECT * FROM Thoughts WHERE ID <= $num ORDER BY ID DESC LIMIT 10";
		$statement = $db ->prepare($sql);
		$statement -> setFetchMode(PDO::FETCH_ASSOC);
		
		// #3 execute query
		$statement ->execute();
				
		$allRows = $statement -> fetchAll();
		$numResults = count ($allRows);
		if ($numResults > 0) {
			//Generate Clouds Based on how many records there are
			return $allRows;
		}
		else {
			$content = "<h3>An error occured. Please try again.</h3>";
		}
		return $content;
	}//end getRows()
	
	//
	// Will create a cloud based off the array passed in
	//
	function createCloud ($thought, $name, $date, $id, $cloudNum) {			
			$cloud = "<a class='thoughtCloud fancybox' id='thought$cloudNum' href='#infobox$id'></a>\n";
			$cloud .= "<div style=\"display: none\"><div class=\"infobox\" id='infobox$id'>\n
				\t<p style=\"max-width: 40em; max-height: 450px; overflow-y: auto\">\"$thought\"</p>\n
				\t<span style='font-size:10pt; font-style:italic; padding-right:4em;  margin-top:.25em;'>$date</span>\n
				\t<span style='float:right; font-style:bold'>- $name</span>\n
				\t</div>\n</div>";
			return $cloud;
	}//end createCloud ()
		
		
	//
	//	Gets the ID of the newest entry
	//
	function getLastID () {
		//open path to db
		$db = open_db(PATH_TO_DB);
		
		//Prepare query
		$sql = "SELECT ID FROM Thoughts ORDER BY ID DESC LIMIT 1";
		$statement = $db ->prepare($sql);
		
		//Execute query
		$statement ->execute();
		
		$lastID = $statement -> fetchAll();
		return $lastID;
	}//end getLastID
	
	/*
	//Function will generate clouds but choose random entries
	// *** NOT IN USE *** - Nate, Feb 1st
	*/
	/*
	function getRandomClouds(){
		//open path to db
		$db = open_db(PATH_TO_DB);
	
		//Prepare query
		$sql = "SELECT * FROM Thoughts";
		$statement = $db ->prepare($sql);
		$statement -> setFetchMode(PDO::FETCH_ASSOC);
		
		// #3 execute query
		$statement ->execute();
		
	
	//Populate and create an array of existing IDs, as well as recording how many ID's there are
		$records = array();
		
		$allRows = $statement -> fetchAll();
		
		foreach ($allRows as $row){
			array_push($records,$row);
		}	

		//Generate Clouds Based on how many records there are
		$content = "";
		$numClouds = 0;
		
		while ($numClouds < 10) {
			$max = count($records) - 1;
			$rand = rand (0, $max);
			$currentRow = $records[$rand];

			$id = $currentRow['ID'];
			
			$content .= "<a class='thoughtCloud fancybox' id='thought$numClouds' href='#infobox$id'></a>\n";
			$content .= "<div style=\"display: none\"><div id='infobox$id'>\n
				<p style=\"max-width: 55em;\">".$currentRow['Thought']."</p>\n
				<span style='font-size:10pt; font-style:italic; padding-right:4em;  margin-top:.25em;'>".$currentRow['Date'] ."</span>\n
				<span style='float:right; font-style:bold'>- ".$currentRow['Name'] ."</span>\n
				</div></div>";
			array_splice($records, $rand, 1);
			$numClouds++;
		}//end while
		
		return $content;
	}//end getRandomClouds();
	*/
	
	//Filters string so only text and no disrupting tags are in it 
	function filter_string($string){
		$string = trim($string);
		$string = htmlentities($string);
		$string = strip_tags($string);
		
		return $string;
	}
	
?>