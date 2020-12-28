//
// (c) 2003 iPulse.com
// written by Stefan Wendt
//

// J's function, removes the blisters from the checkout page
function removeBubbles() {
	$('.formError').each(function(i,e){document.body.removeChild(e);});
}


function closeMyExtraWin()
{
	try {
		myWindow.close();
	}
	catch(e)
	{
		//ignor
	}
}
function openMyExtraWin( myURL, myTitle, xsize, ysize )
{
	var myOptions= "width=" + xsize + ",height=" + ysize + ",resizable=yes,menubar=no,scrollbars=yes";
	myWindow=window.open(myURL,myTitle,myOptions);

	window.onunload= closeMyExtraWin;

	myWindow.focus();
}

function openMyExtraWin_NoSize( url, title )
{
	var options= "";
	myWindow=window.open(url, title, options);
	myWindow.focus();
}

function goToURL( url )
{
	window.location= url;
}

function pleaseWait( formid )
{
	if ( this.document.forms[formid].elements["formaction"].value != "" )
	{
		return;
	}
	scroll(0,0);
	document.getElementById("pleasewait").style.visibility = "visible";
}

function pleaseWaitAlert( url )
{
	alert("The following page takes time to process. Please wait until your browser finished loading the page!");
	window.location= url;
}

function formActionButton( formid, action, arg )
{
	this.document.forms[formid].elements["formaction"].value=action;
	this.document.forms[formid].elements["formaction_arg"].value=arg;
	this.document.forms[formid].submit();
}

function transferCheckinCheckout( ciyear, cimonth, ciday, coyear, comonth, coday )
{
	opener.document.forms["checkincheckout"].elements["ci_year"].value=ciyear;
	opener.document.forms["checkincheckout"].elements["ci_month"].value=cimonth;
	opener.document.forms["checkincheckout"].elements["ci_day"].value=ciday;
	opener.document.forms["checkincheckout"].elements["co_year"].value=coyear;
	opener.document.forms["checkincheckout"].elements["co_month"].value=comonth;
	opener.document.forms["checkincheckout"].elements["co_day"].value=coday;
	close();
	opener.document.forms["checkincheckout"].submit();
}

function formAction( formid, action )
{
	this.document.forms[formid].elements["formaction"].value=action;
	this.document.forms[formid].submit();
}

function readOnlyInputAlert( msg )
{
	alert( msg );
	this.blur();
}

function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}


/**
 * <p><b>Description:</b> <br>validateForm(fieldID, [fieldName], testType)</p>
 * <p>Function used to check (validate) input fields in a form.
 * Can check a single and multiple form input fields, if required.
 * </p>
 * <p>Check single field the information specified in the arguments list is required.<br>
 * Check multiple fields: for each field same information (fieldID, [fieldName], testType) is required.<br>
 * For each following field the input information is same as for the first field (fieldID, [fieldName], testType) separated with comma.<br><br>
 * <b>For example:</b> function call: validateForm(fieldID1, [fieldName1], testType1, fieldID2, [fieldName2], testType2,..etc)
 * </p>
 * <p>
 * <b>Input Arguments:</b>
 * <ul>
 * <li><b>fieldID</b> - the ID of the field to be checked.</li>
 * <li><b>fieldName</b> - "optional". If specified, this name will be used in the "error" message.</li>
 * <li>
 * <b>testType</b> - the test that needs to be done with the required field.
 * </li>
 * </ul>
 * </p>
 * <p>
*  <b>Available testTypes:</b>
*  <ol>
* 	<li><b>R</b>: used to check that a field "REQUIRED" input, select or other input field is entered,selected etc.<br>Can be used with any other testType listed bellow. </li>
* 	<li><b>isEmail</b> : used to check if entered email is in correct form: "username@domain.com".<br>
* if used in the form: "<b>RisEmail</b>" - will mark that the email field is REQUIRED and check if it is valid.</li>
* 	<li><b>isNum</b> : used to check if an input is a number.<br>
* if used in the form: "<b>RisNum</b>" - will mark the field as REQUIRED and check if it is a NUMBER</li>
* 	<li><b>inRange</b>min<b>:</b>max : used to check if the input is a NUMBER and in between the specified RANGE: min:max.<br>
* Extends the testType: "isNum".<br>
* if used in the form: "<b>RinRange</b>" - will mark the field as REQUIRED and do the checkings stated above.</li>
* 	<li><b>isChecked</b> : used to check if a <b>checkbox</b> or <b>radiobutton</b> is checked.<br>
* if used in the form: "<b>RisChecked</b>" - will mark the field (checkbox/radiobutton) as REQUIRED and check whethther it was selected.</li>
*  </ol>
*  </p> 
 * <p>
 * <b>Return</b><br>
 * <b>inputsOK</b> - "true" if the inputs are valid and "false" otherwise.
 * </p>
 *  @param fieldID 
 *  @param [fieldName] 
 *  @param testType
 * 
 * 	@return inputsOK 
 * 
 */
function validateForm() { //v4.0
    // Flag to be used - initially set to true (if an input is not valid the flag will be set to false)
	var inputsOK = true;
	
	if (document.getElementById) {
		
        // Variables to be used
        var i, p, q, checked, value, name,  testType, num, min, max, errors = '';
        // Get passed arguments
        var args = validateForm.arguments;
        
        // Loop through the passed arguments (fields) and test each one.
        for (i = 0; i < (args.length - 2); i += 3) {
			testType = args[i + 2];
            fieldID = document.getElementById(args[i]);
			fieldName = args[i + 1];
            
			// Field is present 
            if (fieldID) {
				// Take field value
				value = fieldID.value;
				// Take field name declared in html code
                name = fieldID.name;
				// Take field name if it is passed on the function call and overwrite the field name (from html code)
				if (fieldName != "") name = fieldName;
				
				/*
				 * The passed field value is not empty - START OF CHECKING INPUT
				 */
                if (value != "") {
					// 1. Check an email address
					if (testType.indexOf('isEmail') != -1) {
						if (!isValidEmail(value)) {
							// Set the flag to false
							inputsOK = false;
							errors += '- ' + name + ' must be in the correct format.\n';
						}
					}
					// 2. Check number and range
					else if (testType.indexOf('isNum') != -1 || testType.indexOf('inRange') != -1) {
						num = parseFloat(value);
						// Check if passed value is a number
						if (isNaN(value)) {
							inputsOK = false;
							errors += '- ' + name + ' must contain a number.\n';
						}
						// Check if the passed value is in specified range
						if (testType.indexOf('inRange') != -1) {
							p = testType.indexOf(':');
							min = testType.substring(8, p);
							max = testType.substring(p + 1);
							if (num < min || max < num) {
								inputsOK = false;
								errors += '- ' + name + ' must contain a number between ' + min + ' and ' + max + '.\n';
							}
						}
					}
					// 3. Check if checkBox is checked checkbox
					else if(testType.indexOf('isChecked') != -1){
						// Check if checkbox is checked
						if(fieldID.checked != 1){
							inputsOK = false;
							errors += '- Please check the field: ' + name + '.\n';
						}
					}
					
				}//Field is empty - check if required
				else if (testType.charAt(0) == 'R') {
					inputsOK = false;
					errors += '- ' + name + ' is required.\n';
				}
            }// End of if(present field)
        }//end of for
		
		// Send error message
        if (!inputsOK)
            alert('The following error(s) occurred:\n' + errors);
    }// End of testing inputs
	
	// Return flag
	return inputsOK;
} //END OF FUNCTION validateForm()


/**
 * Function used to check if entered email i svalid
 * @param {Object} str - email to be checked
 */
function isValidEmail(str){
	//alert ("I will check if email is valid");
    return (str.indexOf(".") > 2) && (str.indexOf("@") > 0);
}
