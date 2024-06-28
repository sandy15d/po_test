// JavaScript Document
function startTime()
{
var today=new Date();
var y=today.getFullYear();
var M=today.getMonth();
var d=(today.getDate()<10 ? "0" : "")+today.getDate();
var E=today.getDay();
var h=today.getHours();
var m=(today.getMinutes()<10 ? "0" : "")+today.getMinutes();
var s=(today.getSeconds()<10 ? "0" : "")+today.getSeconds();

var MonthName = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
var DayName = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
document.getElementById('footer').innerHTML=DayName[E]+", "+MonthName[M]+" "+d+", "+y+"  "+h+":"+m+":"+s;
t=setTimeout('startTime()',500);
}

function checkdate(input1)
{
	var validformat=/^\d{2}\-\d{2}\-\d{4}$/; //Basic check for format validity
	var returnval=false;
	if (!validformat.test(input1.value))
		alert("Error: \n Invalid Date Format. Please correct and submit again.");
	else{ //Detailed check for valid date ranges
		var dayfield=input1.value.split("-")[0];
		var monthfield=input1.value.split("-")[1];
		var yearfield=input1.value.split("-")[2];
		var dayobj = new Date(yearfield, monthfield-1, dayfield);
		if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield))
			alert("Error: \n Invalid Day, Month, or Year range detected. Please correct and submit again.");
		else
			returnval=true;
	}
	if (returnval==false) input1.select();
	return returnval;
}

function getDaysbetween2Dates(input1,input2)
{
	//Total time for one day
	var one_day=1000*60*60*24;
	var diff=0;
	//Here we need to split the inputed dates to convert them into standard format for further execution
    var day1=input1.value.split("-")[0];
    var month1=input1.value.split("-")[1];
    var year1=input1.value.split("-")[2];
    var day2=input2.value.split("-")[0];
    var month2=input2.value.split("-")[1];
    var year2=input2.value.split("-")[2];
  	//date format(Fullyear,month,date) 
    var date1=new Date(year1, month1-1, day1);
    var date2=new Date(year2, month2-1, day2);
    //Calculate difference between the two dates, and convert to days
    diff=Math.ceil((date2.getTime()-date1.getTime())/one_day);
	//diff gives the diffrence between the two dates.
	return diff;
}

function getDateObject(dateString,dateSeperator)
{
	//This function return a date object after accepting 
	//a date string ans dateseparator as arguments
	var curValue=dateString;
	var sepChar=dateSeperator;
	var curPos=0;
	var cDate,cMonth,cYear;

	//extract day portion
	curPos=dateString.indexOf(sepChar);
	cDate=dateString.substring(0,curPos);
	
	//extract month portion				
	endPos=dateString.indexOf(sepChar,curPos+1);
	cMonth=dateString.substring(curPos+1,endPos);

	//extract year portion				
	curPos=endPos;
	endPos=curPos+5;			
	cYear=curValue.substring(curPos+1,endPos);

	//Create Date Object
	dtObject=new Date(cYear,cMonth,cDate);	
	return dtObject;
}

function IsNumeric(sText)
{
	var ValidChars = "0123456789.";
	var Char;

	for (i = 0; i < sText.length; i++)
	{
		Char = sText.charAt(i);
		if (i == 0 && Char == "-") // check first character for minus sign
			continue;
        if ((Char < "0" || Char > "9") && Char != ".") return false;
	}
    return true;
}

function trim(s)
{
  return s.replace(/^\s+|\s+$/, '');
} 

function validateEmail(fld)
{
    var error="";
    var tfld = trim(fld.value);                        // value of field with whitespace trimmed off
    var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
    var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;
    
    if (fld.value == "") {
        error = "* You didn't enter an email address.\n";
    } else if (!emailFilter.test(tfld)) {              //test email for illegal characters
        error = "* Please enter a valid email address.\n";
    } else if (fld.value.match(illegalChars)) {
        error = "* The email address contains illegal characters.\n";
    }
    return error;
}

function validatePhone(fld,str) {
    var error = "";
    var stripped = fld.value.replace(/[\(\)\.\-\ ]/g, '');     

   if (fld.value == "") {
        error = "* You didn't enter a "+str+".\n";
    } else if (isNaN(parseInt(stripped))) {
        error = "* The "+str+" contains illegal characters.\n";
    } 
    return error;
}

function ClearFormElements(oForm)
{
	var frm_elements = oForm.elements;
	var i;
	for(i=0; i<frm_elements.length; i++)
	{
		field_type = frm_elements[i].type.toLowerCase();
		switch(field_type)
		{
			case "text":
			case "password":
			case "textarea":
			case "hidden":

			elements[i].value = "";
			break;

			case "radio":
			case "checkbox":

			if (elements[i].checked){
				elements[i].checked = false;}
			break;

			case "select-one":
			case "select-multi":

			elements[i].selectedIndex = -1;
			break;

			default:
			break;
		}
	} 
}