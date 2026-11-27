	function fPruefen(index)
	{
		var fehlen = "";
			
		if (index == 1) //passwort vergessen
		{	
			if (document.forms["form_pwd"].elements["pwd1"].value == "" ){ 
				fehlen = "Passwort 1"; }
			if (document.forms["form_pwd"].elements["pwd2"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Passwort 2";  }
			if (document.forms["form_pwd"].elements["pwd1"].value != document.forms["form_pwd"].elements["pwd2"].value ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Die beiden Passwörter stimmen nicht überein";  }
		}	
		
		if (index == 2) //register
		{
			if (document.forms["form_reg"].elements["tipprunde"].value == 0 ){ 
				fehlen = "Tipprunde"; }
			if (document.forms["form_reg"].elements["name"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Benutzername";  }
			if (document.forms["form_reg"].elements["email"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Email";  }
			if (document.forms["form_reg"].elements["passwort"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Passwort";  }
			if (document.forms["form_reg"].elements["passwort2"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Passwort (Wdh)";  }
			if (document.forms["form_reg"].elements["passwort"].value != document.forms["form_reg"].elements["passwort2"].value ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Die beiden Passwörter stimmen nicht überein";  }
				
		}	
		
		if (fehlen.length == 0)
		{
			return true;
		}
		else
		{
			fehlen = "Bitte vervollständigen Sie die Angaben folgender Felder:\n" + fehlen;
			alert(fehlen);
			return false;
		}	
			
	}
