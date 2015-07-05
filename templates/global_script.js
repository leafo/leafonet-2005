
	function setbgcolor(e,c) {
		var elm = document.getElementById(e);
		elm.style.backgroundColor = c;
	}
	
	last = null;
	
	function toggle(e) {
		var elm = document.getElementById(e);
		
		if (elm.style.display == 'block')
			elm.style.display = 'none';
		else {
			elm.style.display = 'block';
			
			if ((last != null) && (last != e)) {
				var elmm = document.getElementById(last);
				elmm.style.display = 'none';
			}
				
			last = e;
			
		}
	}
	
	tlast = null;
	
	function ttoggle(e) {
		var elm = document.getElementById(e);
		
		if (elm.style.display == 'block')
			elm.style.display = 'none';
		else {
			elm.style.display = 'block';
			
			if ((tlast != null) && (tlast != e)) {
				var elmm = document.getElementById(tlast);
				elmm.style.display = 'none';
			}
				
			tlast = e;
			
		}
	}