<!DOCTYPE html>
<?php
	require_once('prog4_pj1.php');
	// #1 open DB
	$db =  open_db(PATH_TO_DB);

?>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Thinking Space is a place where people can post some of their fun, strange, secret or philosophic thoughts anonymously.">
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css' />
	<!-- jQuery Library -->
	<script src="assets/jquery-1.8.3.min.js"></script>
	<!-- Fancybox -->
	<script type="text/javascript" src="assets/fancybox/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" href="assets/fancybox/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
	<!-- Thinking Space js-->
	<script src="assets/js/spaceLIB.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="spaceStyle.css" />
	<link rel="stylesheet" type="text/css" href="clouds.css" />
		<!-- FaviIcon -->
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>
	<link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
	<title>Thinking Space</title>
	<style>
	#links a, #links a:visited {text-decoration: none; color: #333}
	#links a:hover {text-decoration: underline; color: #a00;}
	</style>

	<script type="text/javascript">

	//
	// Controls the opening and closing of the instructions tab
	//
	var instructionsOpen = 0;
	function instructionsToggle () {
		if(instructionsOpen == 0  || open == null){
			$('#instructionWrapper').animate({marginRight:'0px', zIndex: 1350});
			$("#instructionTab").text(">");
			instructionsOpen = 1;
		}
		else {
			$('#instructionWrapper').animate({marginRight:'-=425px', zIndex: 1000});
			$("#instructionTab").text("<");
			instructionsOpen = 0;
		}
	}

	//Handles form validation
	//If form is valid, send data
	//Get back success/fail message
	function validateForm () {
		var thought = $('#new_thought').val();
		var name = $('#new_thought_name').val();

		if (thought == '') {
			alert ('Please fill out a thought before submitting.');
			return false;
		} else {
			submitForm (name, thought);
		}
	}//end validateForm()

	function submitForm (newName, newThought) {
		//Gets called upon proper form valdation

		//Close fancybox
		$('#status').load("query.php", {thought: newThought, name: newName, submit_new_post: 'submit'},
					function () {
						$('#formWrapper').css('display', 'none');
						$('#status').css('display', 'block');

						//Create new cloud for the new entry
						if (newName == '' || newName == ' ' || newName == '   ') {
							newName = 'Anonymous';
						}
						var d = new Date();
						var date = d.toUTCString();
						$('#info_newEntry').html ('<p style=\"max-width:55em\">"' + newThought + '"</p><span style="font-size:10pt; font-style:italic; padding-right:4em;  margin-top:.25em;">' + date + '</span><span style="float:right; font-style:bold">- ' + newName + '</span>');
						$('#info_newEntry').css ('display', 'block');
						$('#newEntryCloud').css ('display', 'block');
						animateNewCloud();
					});

	}//end submitForm()

	<?php

		//Acts as a fail safe if you press enter when doing your search
		if (array_key_exists('search_for', $_GET)) {
			echo "$(document).ready ( function () {
						submitSearch ();
					});
				";
		}
	?>

	//
	//Handles the submission for searches
	//
	function submitSearch () {
		<?php
			if (array_key_exists('search_for', $_GET)) {
				$word = $_GET['search_for'];
				$html = "
						if ($('#search_for').val() == '') {
							var searchFor = '$word';
						} else {
							var searchFor = $('#search_for').val();
						}
						";
				echo $html;
			} else {
				echo "var searchFor = $('#search_for').val();";
			}
		?>
		instructionsToggle();
		$('#content').load("query.php", {search: searchFor, search_submit: 'submit'},
					function () {
						triggerFancybox();
						animateClouds();
						$('.nextSetTab').hide();
						//****
					});
	}//end submitSearch()

	function validateSearch () {
		var input = $('#search_for').val();
		if (input == '' || input == ' ' || input == '  ' || input == '   ' ) {
			alert ("Please enter a word to search for.");
		} else {
			submitSearch();
		}
	}//end validateSearch ()

	</script>
</head>
<body>

	<div style="top: 0; height: auto; position: fixed; width: 100%;">
	<header>
	<img id="logo" src="images/logo.png" alt="logo"/>
	<h3>Thinking Space</h3>
	</header>
	<div id="links" style="margin-left: 40%; width: 60%;">
	<a  href="http://people.rit.edu/~ndp4570/334/thinking_space/">Newest Entries</a> |
	<a  href="http://people.rit.edu/~ndp4570/334/thinking_space/?id=10">Oldest Entries</a>
	</div>

	<div id="content">
	<?php
			$TheLastID = getLastId ();
			$TheLastID = $TheLastID[0][0];
			if (array_key_exists('id', $_GET) && $_GET['id'] < $TheLastID) {
				$maxID = $_GET['id'];
				if ($maxID == $TheLastID - 1) {
					$maxID = $TheLastID;
				}
			} else {
				$maxID = $TheLastID;
			}
			if ($maxID <= 10) {$maxID = 10;}
			$clouds = '';
			$cloudNum = 1;

			//Gets the 10 clouds before the maxID
			$allRows = getRows($maxID);
			foreach ($allRows as $row) {
				$thought = $row['Thought'];
				$name = $row['Name'];
				$date = $row['Date'];
				$id = $row['ID'];
				$clouds .= createCloud ($thought, $name, $date, $id, $cloudNum);
				$cloudNum ++;
			}

			echo $clouds;
	?>
		<a class="thoughtCloud fancybox" id="newEntryCloud" style="display:none" href="#info_newEntry" ></a>
		<div style="display:none;">
			<div id="info_newEntry">
				//is a placeholder until a new entry is made. Once a new entry is made
				//the submit form script will replace this text with the proper information
				//as well as make it display
			</div>
		</div>
	</div>

	<!-- Tabs that are clicked for next set of clouds -->
	<?php
		if ($maxID <= $TheLastID && $maxID > 10) {
			$number = $maxID - 10;
			echo '<a class="nextSetTab" style="float: left; left: 10px;" id="PrevSetLeft" href="?id=' . $number . '"><span class="nextSetArrow">&lt;</span></a>';
		}
		if ($maxID <= $TheLastID - 10 || ($maxID <= 10 && $TheLastID > 10)) {
			$number = $maxID + 10;
			echo '<a class="nextSetTab" style="float: right; right: 10px;" id="nextSetRight" href="?id=' . $number . '"><span class="nextSetArrow">&gt;</span></a>';
		}
	?>

	<!-- Instructions panel on the right side -->
	<div id="instructionWrapper">
		<div id="instructionsLeft">
			<div id="instructionTab" onClick="instructionsToggle();">
			&lt;
			</div>
		</div>
		<div id="instructionsRight">
			<div id="instructions">
				<div id="instructionText" style="font-size:10.5pt;"><h3>Welcome to Thinking Space!</h3> Here your thoughts, ideas, secrets, and stories can be easily shared
			with others and anonymously as well.  Get started by clicking the "Post a Thought" button below and post you words into the Thinking Space for everyone to see.
			You can also search for posted thoughts as well by typing in a topic, or word of interest in the search bar below to see related posts.
				</div>
				<br />
				<a id="newEntryBtn" class="button fancybox" href="#newEntry" onClick="instructionsToggle()">Post a Thought</a>
				<br>
				<form action="" id='search_form'>
				<input id='search_for' type="text" name="search_for" size="30" <?php if (array_key_exists('search_for', $_GET)) { echo 'value="' . $_GET['search_for'] . '"';} ?> placeholder="Search..." />
				<input class="formbutton" onClick="validateSearch()" type="button" name="search_submit" value="Search" />
				</form>
			</div>
		</div>
	</div>


	<footer id="footer">
	<span id="footerText" >Danny Nguyen and Nate Perry &copy; <?php echo date('Y'); ?></span>
	<a class="fancybox" id ="contactlink" href="#contactinfo">Contact Us</a>
	</footer>

	<!--
	* For items that will be displayed in Fancybox,
	* place them within this hidden div. Be sure to
	* use the appropriate ID's.
	-->
<div id="hiddenitems" style="display: none">
	<div id="contactinfo" style="width:400px;">
		Developed By<br/>
		<h3> Danny Nguyen</h3>
		<b>Email:</b> dxn7335@rit.edu<br/>
		<h3>Nathaniel Perry</h3>
		<b>Email:</b> ndp4570@rit.edu
	</div>

	<div id="newEntry">
		<div id="formWrapper">
			<form id="new_entry_form" action="">
					<table>
						<tr>
						<!--
						*
						* Should this be optional?
						*
						-->
						<td>Name:</td>
						<td><input id="new_thought_name" type="text" name="name" size="35" placeholder="Anonymous" /></td>
						</tr>

						<tr>
						<td>Thought:</td>
						<td>
						<textarea id="new_thought" name="thought" cols="50" rows="10" placeholder="Post your thoughts, ideas, dreams, or stories here..."></textarea>
						</td>
						</tr>

					</table>
					<br>
					<input class="formbutton" type="button" onClick="validateForm()" name="submit_new_post" value="Submit"/>
			</form>
		</div>

		<!-- Status message after thought is posted-->
		<div id="status" style="display: none; text-align: center">
			//placeholder for information. Will be changed to success or fail
		</div>
	</div><!-- End #newEntry -->

</div><!-- end hiddenitems -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45160660-2', 'rit.edu');
  ga('send', 'pageview');

</script>



</body>
</html>
<!--
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40258106-2', 'rit.edu');
  ga('send', 'pageview');

</script>-->