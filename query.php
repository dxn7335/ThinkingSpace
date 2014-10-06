<?php
	
	require_once('prog4_pj1.php'); 
	// #1 open DB
	$db =  open_db(PATH_TO_DB);
	
	if(array_key_exists('submit_new_post', $_POST)){
		echo addThought($db);
	}
	if(array_key_exists('search_submit', $_POST)){
		echo searchThoughts($db);
	}
	
	/*FUNCTIONS FOR FORM*/
	///////////////////
	
	//Adds a new thought to the database
	//Returns the cloud prefilled with the proper information
	function addThought($db){
			//need Name, Thought, and Date
			
			//used to check if the thought is not the default message in the form
			$defaultMessage= "Post your thoughts, ideas, dreams, or stories here...";
			
			if(array_key_exists('name',$_POST) && array_key_exists('thought',$_POST)&& strlen($_POST['thought'])>0 
			&& $_POST['thought'] != "" && $_POST['thought'] != " " && $_POST['thought'] != "   " && 
			filter_string($_POST['thought'])!= $defaultMessage) {
				
				
				//setting the variables with values from form
				$name= filter_string($_POST['name']);
				if($name == "" || $name == " " || $name == "  "){
					$name = "Anonymous";
				}
				
				$thought = filter_string($_POST['thought']);
				$date = date("m-d-Y  H:i:s",Time('EST'));
				
				
				//prepare and execute SQL
				//the ? ? are placeholders for the values
				$sql = "INSERT INTO Thoughts (Name, Thought, Date) VALUES(?,?,?)";
				$statement = $db -> prepare($sql);
				
				//execute and pass in values
				$statement -> execute (array($name, $thought, $date));
				
				//Checks if statement was successful or not and returns a message
					if($statement){
						$id= $db->lastInsertId(); //lastInsertId() returns the last ID that was incremented into database
						$status= "Thank you for your thoughts!";
					}
					else{
						$status = "Sorry, there was an error and your thought wasn't able to be posted. Try reloading this bitch and try again.";
					}
			
			
			}//END if statement
			
			else{
				$status = "Not all necessary areas have been filled out.";
			}
			
			///the returned status could be used to open another fancybox that will alert the user after data is sent
			return $status;
		
		}
		
		//Searches the database for related thoughts
		function searchThoughts ($db) {
			//open path to db
			$db = open_db(PATH_TO_DB);
			
			$input = filter_string($_POST['search']);
			
			if ($input == '' || $input == ' ') {
				return '<h3>An error occured somewhere along the way. Please try refreshing the page or doing a new search</h3>';
			}
			
			//Prepare query
			$sql = "SELECT * FROM Thoughts WHERE Thought LIKE '%$input%'";
			$statement = $db ->prepare($sql);
			$statement -> setFetchMode(PDO::FETCH_ASSOC);
			
			// #3 execute query
			$statement ->execute();
					
			$allRows = $statement -> fetchAll();
			$numResults = count ($allRows);
			if ($numResults > 0) {
				//Generate Clouds Based on how many records there are
				$content = "";
				$numClouds = 1;
				
				if ($numRows = 1) {
					$content = "<h3>Found $numResults result for \"$input\"</h3>";
				} else {
					$content = "<h3>Found $numResults results for \"$input\"</h3>";
				}
				
				foreach ($allRows as $row) {
				
					$id = $row['ID'];
					
					$content .= "<a class='thoughtCloud fancybox' id='thought$numClouds' href='#infobox$id'></a>\n";
					$content .= "<div style=\"display: none\"><div id='infobox$id'>\n
						<p style=\"max-width: 45em;\">".$row['Thought']."</p>\n
						<span style='font-size:10pt; font-style:italic; padding-right:4em;  margin-top:.25em;'>".$row['Date'] ."</span>\n
						<span style='float:right; font-style:bold'>- ".$row['Name'] ."</span>\n
						</div></div>";
					$numClouds++;
				}
			}
			else {
				$content = "<h3>No results were found for \"$input\"</h3>";
			}
			return $content;
		}//end searchThoughts()
		


?>