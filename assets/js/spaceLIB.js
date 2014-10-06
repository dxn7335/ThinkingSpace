	var icons= new Array();
			icons[0]= "#thought1";
			icons[1]= "#thought2";
			icons[2]= "#thought3";
			icons[3]= "#thought4";
			icons[4]= "#thought5";
			icons[5]= "#thought6";
			icons[6]= "#thought7";
			icons[7]= "#thought8";
			icons[8]= "#thought9";
			icons[9]= "#thought10";
			icons[10]= "#newEntryCloud";
	
	$(document).ready(function(){	
		//moveClouds ();
		triggerFancybox();
		animateClouds ();
		
		$('#search_form').submit ( function () {
				validateSearch();
				return false;
			}
		);
		
		$('#new_entry_form').submit ( function () {
				validateForm();
				return false;
			}
		);

	});//end document ready
	
	
	function animateClouds () {
		$(icons[0]).animate({left: '8%', top: '15%'}, 3000);
		$(icons[1]).animate({left: '35%', top: '13%'}, 3000);
		$(icons[2]).animate({left: '60%', top: '7%'}, 3000);
		//end top row
		$(icons[3]).animate({left: '1%', top: '43%'}, 3000);
		$(icons[4]).animate({left: '26%', top: '32%'}, 3000);
		$(icons[5]).animate({left: '50%', top: '40%'}, 3000);
		$(icons[6]).animate({left: '73%', top: '28%'}, 3000);
		//end middle row
		$(icons[7]).animate({left: '6%', top: '67%'}, 3000);
		$(icons[8]).animate({left: '36%', top: '58%'}, 3000);
		$(icons[9]).animate({left: '66%', top: '64%'}, 3000);
		
	}
	
	//is called when a new cloud is added
	function animateNewCloud () {
		$(icons[7]).animate({left: '6%'}, 5000);
		$(icons[8]).animate({left: '29%'}, 5000);
		$(icons[9]).animate({left: '51%'}, 5000);
		$(icons[10]).animate({left: '74%', top: '64%'}, 5000);
	}
	
	function triggerFancybox () {
		/* Fancybox */
		$(".fancybox").fancybox();
	}
	
	function moveClouds () {
			var horiPos = "5";
			var vertPos = "0";
			var unit = "em";
			
			var i=0;
			while( i < icons.length) {	
				var domWidth = document.body.clientWidth;
				var domHeight = document.body.clientHeight;
				
				var direction = Math.floor((Math.random()*2));
				
				if(direction == 0){
					var num = Math.ceil((Math.random()*domWidth)-400)+1;
					var unit = "px";
					var horiMove= num+unit;
					num = Math.ceil((Math.random()*domHeight)-30);
					unit = "px";
					var vertMove= num+unit;
				$(icons[i]).animate({left:"+"+ horiMove, top:"+"+ vertMove}, 3000);
				}
				if(direction == 1){
					var num = Math.ceil((Math.random()*domWidth)-400)+1;
					var unit = "px";
					var horiMove= num+unit;
					num = Math.ceil((Math.random()*domHeight)-30);
					unit = "px";
					var vertMove= num+unit;
				$(icons[i]).animate({right:"+"+ horiMove, top:"+"+ vertMove}, 3000);
				}
				if (checkForOverlap (icons[i]) == true) {
					i = i;
				} else {
					i++;
				}
			}//end while loop
	}//end moveClouds ()
	
	
	function hitTestObject(testobj, objCompare){
	
		var boundary = $(testobj).offset();
		//since offset only gives values of position relative to document for top and left, 
		//->right and bottom need to be defined
		boundary.right = boundary.left + $(testobj).outerWidth();
		boundary.bottom = boundary.top + $(testobj).outerHeight();
		
		//this is the obj the function uses to compare positions with the element of interest
		var compare = $(objCompare).offset();
		compare.right = compare.left + $(objCompare).outerWidth();
		compare.bottom = compare.top + $(objCompare).outerHeight();
		
		if(!(compare.right < boundary.left)){
			//$(testobj).animate({left: + $(objCompare).outerWidth()/2}, 10);
			alert (objCompare + ' right side overlaps');
			return true;
		}
		
		else if(!(compare.left > boundary.right)){
			//$(testobj).animate({right: + $(objCompare).outerWidth()/2}, 10);
			alert (objCompare + ' left side overlaps');
			return true;
		}
		
		else if(!(compare.top < boundary.bottom)){
			//$(testobj).animate({bottom: + $(objCompare).outerHeight()/2}, 10);
			alert (objCompare + ' bottom sideoverlaps');
			return true;
		}
		
		else if(!(compare.bottom > boundary.top)){
			//$(testobj).animate({top: + $(objCompare).outerHeight()/2}, 10);
			alert (objCompare + ' top sideoverlaps');
			return true;
		}
		else {
			//there is no overlap
			return false;
		}
		
	};
	
	function checkOverlap(){
		var count = 0;
		while(count< icons.length){
			//check a single cloud against all other clouds, until all clouds have been checked
			for(var i=0; i<icons.length ; i++){
				if(count != i){
					hitTestObject(icons[count],icons[i]);
				}
			}
			count ++;
		}
	};
	
	function checkForOverlap (currObject) {
		//returns true if there is an overlap
		for (var i = 0; i < icons.length; i++) {
			if (currObject != icons[i]) {
				hitTestObject(currObject, icons[i])
			}
		}
	}//end checkForOverlap