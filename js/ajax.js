// JavaScript Document
function get_state(value1){
	var url = 'get_state_name.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_state
	});
}
function show_state(originalRequest)
{	
   document.getElementById('state').innerHTML = originalRequest.responseText;
}

function get_matching_items(){
	var url = 'get_matching_items.php';
	var pars = 'nm='+document.getElementById('itemName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_items
	});
}
function show_matching_items(originalRequest)
{	
   document.getElementById('spanITList').innerHTML = originalRequest.responseText;
}

function get_matching_itemgroups(){
	var url = 'get_matching_itemgroups.php';
	var pars = 'nm='+document.getElementById('groupName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_itemgroups
	});
}
function show_matching_itemgroups(originalRequest)
{	
   document.getElementById('spanITGList').innerHTML = originalRequest.responseText;
}

function get_matching_units(){
	var url = 'get_matching_units.php';
	var pars = 'nm='+document.getElementById('unitName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_units
	});
}
function show_matching_units(originalRequest)
{	
   document.getElementById('spanUnitList').innerHTML = originalRequest.responseText;
}

function get_matching_usability(){
	var url = 'get_matching_usability.php';
	var pars = 'nm='+document.getElementById('usabilityName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_usability
	});
}
function show_matching_usability(originalRequest)
{	
   document.getElementById('spanUseList').innerHTML = originalRequest.responseText;
}

function get_matching_party(){
	var url = 'get_matching_party.php';
	var pars = 'nm='+document.getElementById('partyName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_party
	});
}
function show_matching_party(originalRequest)
{	
   document.getElementById('spanPartyList').innerHTML = originalRequest.responseText;
}

function get_matching_city(){
	var url = 'get_matching_city.php';
	var pars = 'nm='+document.getElementById('cityName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_city
	});
}
function show_matching_city(originalRequest)
{	
   document.getElementById('spanCityList').innerHTML = originalRequest.responseText;
}

function get_matching_state(){
	var url = 'get_matching_state.php';
	var pars = 'nm='+document.getElementById('stateName').value;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_matching_state
	});
}
function show_matching_state(originalRequest)
{	
   document.getElementById('spanStateList').innerHTML = originalRequest.responseText;
}

function get_items_on_itemgroup(value1){
	var url = 'get_items.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_items_on_itemgroup
	});
}
function show_items_on_itemgroup(originalRequest)
{	
   document.getElementById('tdItemName').innerHTML = originalRequest.responseText;
}

function get_unit(value1){
	var url = 'get_unit_name.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_unit_name
	});
}
function show_unit_name(originalRequest)
{	
	var res=originalRequest.responseText;
	document.getElementById('rdUnit').innerHTML=res+" / Acre";
	document.getElementById('maxdUnit').innerHTML=res+" / Acre";
	document.getElementById('mindUnit').innerHTML=res+" / Acre";
//	document.getElementById('designation_td').innerHTML='<input type="text" name="reporting_designation" size="50" disabled="disabled" class="ctrl" value="'+res[0]+'">';
}

function get_partydetail_of_po(value1){
	var url = 'get_party_detail_of_po.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_partydetail
	});
}
function show_partydetail(originalRequest)
{
	var res=originalRequest.responseText.split('~~',7);
	document.getElementById('address1').value=res[0];
	document.getElementById('address2').value=res[1];
	document.getElementById('address3').value=res[2];
	document.getElementById('cityName').value=res[3];
	document.getElementById('stateName').value=res[4];
	document.getElementById('contactPerson').value=res[5];
	document.getElementById('tinNumber').value=res[6];
}

function get_partydetail_on_payment(value1){
	var url = 'get_party_detail_payment.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_partydetail_on_payment
	});
}
function show_partydetail_on_payment(originalRequest)
{
	var res=originalRequest.responseText.split('~~',6);
	document.getElementById('address1').value=res[0];
	document.getElementById('address2').value=res[1];
	document.getElementById('address3').value=res[2];
	document.getElementById('cityName').value=res[3];
	document.getElementById('stateName').value=res[4];
	document.getElementById('partyName').value=res[5];
}

function get_unit_ofitem(value1){
	var url = 'get_unit_of_item.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_unit_ofitem
	});
}
function show_unit_ofitem(originalRequest)
{
	document.getElementById('iqunitName').innerHTML=originalRequest.responseText;
	document.getElementById('csunitName').innerHTML=originalRequest.responseText;
}

function get_unit_from_opstock(value1){
	var url = 'get_unit_of_opstock.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_unit_from_opstock
	});
}
function show_unit_from_opstock(originalRequest)
{
	var res=originalRequest.responseText.split('~~',2);
	document.getElementById('unitName').value=res[0];
	document.getElementById('unitID').value=res[1];
}

function get_curent_stock_of_item(value1){
	var url = 'get_cstock_of_item.php';
	var value2 = "";
	if(document.forms[0].name == "issueitem"){
		value2 = document.getElementById('issueDate').value;
	} else if(document.forms[0].name == "iltditem"){
		value2 = document.getElementById('iltDate').value;
	} else if(document.forms[0].name == "xltditem"){
		value2 = document.getElementById('xltDate').value;
	} else if(document.forms[0].name == "xltritem"){
		value2 = document.getElementById('xltDate').value;
	} else if(document.forms[0].name == "cpitem"){
		value2 = document.getElementById('memoDate').value;
	} else if(document.forms[0].name == "psitem"){
		value2 = document.getElementById('pstockDate').value;
	}
	var pars = 'iid='+value1+'&lid='+document.getElementById('location').value+'&edt='+value2;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_curent_stock_of_item
	});
}
function show_curent_stock_of_item(originalRequest)
{
	var res=originalRequest.responseText.split('~~',7);
	document.getElementById('itemStock').value=res[1];
	if(res[2]=="N"){
		document.getElementById('spanUnit1').innerHTML=res[0];
		//document.getElementById('spanUnit2').innerHTML=res[0];
		//document.getElementById('tblcol1').style.visibility='hidden';
	} else if(res[2]=="A"){
		document.getElementById('spanUnit1').innerHTML=res[0]+'<br><span style="font-size: 10px;">('+res[5]+" "+res[6]+')</span>';
		//document.getElementById('spanUnit2').innerHTML='&nbsp;';
		//document.getElementById('tblcol1').innerHTML='Unit:';
	}
	if(res[2]=="N" || (res[2]=="A" && res[4]==0)){
		document.getElementById('tblcol1').innerHTML='<select name="unit" id="unit" style="width:115px"><option value="'+res[3]+'">'+res[0]+'</option></select>';
	} else if(res[2]=="A" && res[4]!=0){
		document.getElementById('tblcol1').innerHTML='<select name="unit" id="unit" style="width:115px"><option value="'+res[3]+'">'+res[0]+'</option><option value="'+res[4]+'">'+res[6]+'</option></select>';
	}
}

function get_max_date(value1,value2,value3,value4){
	var url = 'get_max_date.php';
	var pars = 'id='+value1+'&sdt='+value2+'&edt='+value3+'&pg='+value4;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_max_date
	});
}
function show_max_date(originalRequest)
{
	document.getElementById('maxDate').value = originalRequest.responseText;
}

function get_staffs(value1){
	var url = 'get_staffs.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_staffs
	});
}
function show_staffs(originalRequest)
{	
	document.getElementById('staffOption').innerHTML = originalRequest.responseText;
	get_max_date(document.getElementById('location').value, document.getElementById('startYear').value, document.getElementById('endYear').value, document.forms[0].name);
}

function get_indentdetail(value1){
	var url = 'get_indent_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indentdetail
	});
}
function show_indentdetail(originalRequest)
{
	var res=originalRequest.responseText.split('~~',5);
	document.getElementById('indentDate').value=res[0];
	document.getElementById('orderFrom').value=res[1];
	document.getElementById('supplyDate').value=res[2];
	document.getElementById('orderBy').value=res[3];
	document.getElementById('termsCondition').value=res[4];
}

function get_podetails_of_dc(value1){
	var url = 'get_po_details_of_dc.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_podetails_on_dc
	});
}
function show_podetails_on_dc(originalRequest)
{
	var res=originalRequest.responseText.split('~~',11);
	document.getElementById('poDate').value=res[0];
	document.getElementById('partyName').value=res[1];
	document.getElementById('address1').value=res[2];
	document.getElementById('address2').value=res[3];
	document.getElementById('address3').value=res[4];
	document.getElementById('cityName').value=res[5];
	document.getElementById('stateName').value=res[6];
	document.getElementById('deliveryDate').value=res[7];
	document.getElementById('location').value=res[8];
	document.getElementById('deliveryAt').value=res[9];
	document.getElementById('companyName').value=res[10];
	get_max_date(document.getElementById('location').value, document.getElementById('startYear').value, document.getElementById('endYear').value, document.forms[0].name);
}

function get_dcdetails_on_mat_rcpt(value1){
	var url = 'get_dc_details_for_mrcpt.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_dcdetails_on_mat_rcpt
	});
}
function show_dcdetails_on_mat_rcpt(originalRequest)
{
	var res=originalRequest.responseText.split('~~',11);
	document.getElementById('dcDate').value=res[0];
	document.getElementById('poNo').value=res[1];
	document.getElementById('poDate').value=res[2];
	document.getElementById('partyName').value=res[3];
	document.getElementById('address1').value=res[4];
	document.getElementById('address2').value=res[5];
	document.getElementById('address3').value=res[6];
	document.getElementById('cityName').value=res[7];
	document.getElementById('stateName').value=res[8];
	document.getElementById('deliveryDate').value=res[9];
	document.getElementById('deliveryAt').value=res[10];
}

function get_podetails_on_pbill(value1){
	var url = 'get_po_details_for_pbill.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_podetails_on_pbill
	});
}
function show_podetails_on_pbill(originalRequest)
{
	var res=originalRequest.responseText.split('~~',3);
	document.getElementById('poDate').value=res[1];
	document.getElementById('orderFor').value=res[2];
	get_mrnumber_for_po(res[0]);
}

function get_mrnumber_for_po(value1){
	var url = 'get_mrnumber_for_po.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_mrnumber_for_po
	});
}
function show_mrnumber_for_po(originalRequest)
{
	document.getElementById('tdMRN').innerHTML=originalRequest.responseText;
	document.getElementById('mrDate').value="";
	document.getElementById('recdAt').value="";
	document.getElementById('locationID').value="";
}

function get_mrdetails_on_pbill(value1){
	var url = 'get_mr_details_for_pbill.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_mrdetails_on_pbill
	});
}
function show_mrdetails_on_pbill(originalRequest)
{
	var res=originalRequest.responseText.split('~~',4);
	document.getElementById('mrDate').value=res[1];
	document.getElementById('recdAt').value=res[2];
	document.getElementById('locationID').value=res[3];
	get_items_on_pbill(res[0]);
}

function get_items_on_pbill(value1){
	var url = 'get_items_on_pbill.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_items_on_pbill
	});
}
function show_items_on_pbill(originalRequest)
{
	document.getElementById('tdITEM').innerHTML = originalRequest.responseText;
	document.getElementById('itemStock').value="";
	document.getElementById('orderQnty').value="";
	document.getElementById('receivedQnty').value="";
	document.getElementById('billQnty').value="";
}

function get_stockNqnty_of_item_on_pbill(value1,value2,value3,value4,value5){
	var url = 'get_stockNqnty_of_item.php';
	var pars = 'iid='+value1+'&lid='+value2+'&edt='+value3+'&pid='+value4+'&mid='+value5;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_stockNqnty_of_item_on_pbill
	});
}
function show_stockNqnty_of_item_on_pbill(originalRequest)
{
	var res=originalRequest.responseText.split('~~',11);
	document.getElementById('itemStock').value=res[0];
	document.getElementById('orderQnty').value=res[2];
	document.getElementById('receivedQnty').value=res[4];
	if(res[6]=="N"){
		document.getElementById('spanUnit1').innerHTML=res[9];
		document.getElementById('spanUnit2').innerHTML=res[9];
		document.getElementById('spanUnit3').innerHTML=res[9];
	} else if(res[6]=="A"){
		document.getElementById('spanUnit1').innerHTML=res[9]+'<br><span style="font-size: 10px;">('+res[1]+" "+res[10]+')</span>';
		document.getElementById('spanUnit2').innerHTML=res[9]+'<br><span style="font-size: 10px;">('+res[3]+" "+res[10]+')</span>';
		document.getElementById('spanUnit3').innerHTML=res[9]+'<br><span style="font-size: 10px;">('+res[5]+" "+res[10]+')</span>';
	}
	if(res[6]=="N" || (res[6]=="A" && res[8]==0)){
		document.getElementById('tblcol1').innerHTML='<select name="unit" id="unit" style="width:115px"><option value="'+res[7]+'">'+res[9]+'</option></select>';
	} else if(res[6]=="A" && res[8]!=0){
		document.getElementById('tblcol1').innerHTML='<select name="unit" id="unit" style="width:115px"><option value="'+res[7]+'">'+res[9]+'</option><option value="'+res[8]+'">'+res[10]+'</option></select>';
	}
}

function get_partydetails_on_pbill(value1){
	var url = 'get_party_details_for_pbill.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_partydetails_on_pbill
	});
}
function show_partydetails_on_pbill(originalRequest)
{
	var res=originalRequest.responseText.split('~~',6);
	document.getElementById('address1').value=res[0];
	document.getElementById('address2').value=res[1];
	document.getElementById('address3').value=res[2];
	document.getElementById('cityName').value=res[3];
	document.getElementById('stateName').value=res[4];
	document.getElementById('partyName').value=res[5];
}

function get_receipt_details(value1){
	var url = 'get_receipt_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_receipt_details
	});
}
function show_receipt_details(originalRequest)
{
	var res=originalRequest.responseText.split('~~',19);
	document.getElementById('receiptDate').value=res[0];
	document.getElementById('poNo').value=res[1];
	document.getElementById('poDate').value=res[2];
	document.getElementById('challanNo').value=res[3];
	document.getElementById('challanDate').value=res[4];
	document.getElementById('transitPoint').value=res[5];
	document.getElementById('deliveryDate').value=res[6];
	document.getElementById('deliveryAt').value=res[7];
	document.getElementById('partyName').value=res[8];
	document.getElementById('address1').value=res[9];
	document.getElementById('address2').value=res[10];
	document.getElementById('address3').value=res[11];
	document.getElementById('cityName').value=res[12];
	document.getElementById('stateName').value=res[13];
	document.getElementById('receivedAt').value=res[14];
	document.getElementById('recdBy').value=res[15];
	document.getElementById('recdLocation').value=res[16];
	document.getElementById('freightPaid').value=res[17];
	document.getElementById('freightAmount').value=res[18];
	get_staffs(res[16]);
}

function get_issue_detail(value1){
	var url = 'get_issue_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_issue_detail
	});
}
function show_issue_detail(originalRequest)
{
	var res=originalRequest.responseText.split('~~',5);
	document.getElementById('issueDate').value=res[0];
	document.getElementById('location').value=res[1];
	document.getElementById('locationName').value=res[2];
	document.getElementById('issueBy').value=res[3];
	document.getElementById('issueTo').value=res[4];
}

function getshipping_detail(value1,value2){
	var url = 'get_shipping_detail.php';
	var pars = 'id='+value1+'&sip2='+value2;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: showshipping_detail
	});
}
function showshipping_detail(originalRequest)
{	
	var res=originalRequest.responseText.split('~~',5);
	document.getElementById('shippingAddress1').value=res[0];
	document.getElementById('shippingAddress2').value=res[1];
	document.getElementById('shippingAddress3').value=res[2];
	document.getElementById('shippingcityName').value=res[3];
	document.getElementById('shippingstateName').value=res[4];
}

function get_ship_control(value1){
	var url = 'get_ship_name.php';
	var pars = 'sip2='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_ship_control
	});
}
function show_ship_control(originalRequest)
{	
   document.getElementById('ship2control').innerHTML = originalRequest.responseText;
}

function get_detail_on_paytype(value1){
	if(value1==0 || value1==1){
		document.getElementById('chqnum').innerHTML = '<input name="chequeNumber" id="chequeNumber" maxlength="10" size="20" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById('chqdt').innerHTML = '<input name="chequeDate" id="chequeDate" maxlength="10" size="10" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById('chequeDate').value = document.getElementById('paymentDate').value;
		document.getElementById('calndr').style.display="none";
	} else {
		document.getElementById('chqnum').innerHTML = '<input name="chequeNumber" id="chequeNumber" maxlength="10" size="20" value="" tabindex="4">';
		document.getElementById('chqdt').innerHTML = '<input name="chequeDate" id="chequeDate" maxlength="10" size="10" tabindex="5">';
		document.getElementById('chequeDate').value = document.getElementById('paymentDate').value;
		document.getElementById('calndr').style.display="";
	}
	
	var url = 'get_bank.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_detail_on_paytype
	});
}
function show_detail_on_paytype(originalRequest)
{	
   document.getElementById('bnkname').innerHTML = originalRequest.responseText;
}

function get_controls_on_paymode(value1,value2,value3,value4)
{
	if(value1==1 || value1==3){
		document.getElementById("bamt_span").innerHTML='<input name="billAmount" id="billAmount" maxlength="15" size="20" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById("bdate_span").innerHTML='<input name="billDate" id="billDate" maxlength="10" size="10" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById("deduct_span").innerHTML='<input name="deductAmount" id="deductAmount" maxlength="15" size="20" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
	} else if(value1==2){
		document.getElementById("bamt_span").innerHTML='<input name="billAmount" id="billAmount" maxlength="15" size="20" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById("bdate_span").innerHTML='<input name="billDate" id="billDate" maxlength="10" size="10" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById("deduct_span").innerHTML='<input name="deductAmount" id="deductAmount" maxlength="15" size="20" tabindex="4">';
	}
	
	var url = 'get_billno.php';
	var pars = 'id='+value1+'&ptid='+value2+'&pid='+value3+'&pdt='+value4;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_controls_on_paymode
	});
}
function show_controls_on_paymode(originalRequest)
{	
   document.getElementById('bnum_span').innerHTML = originalRequest.responseText;
}

function get_bill_details(value1)
{
	var w = document.getElementById('billNo').selectedIndex;
	var selected_text = document.getElementById('billNo').options[w].text;
	document.getElementById('bnum').value = selected_text;
	var url = 'get_bill_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_bill_details
	});
}
function show_bill_details(originalRequest)
{	
	var res=originalRequest.responseText.split('~~',2);
	document.getElementById('billDate').value=res[0];
	document.getElementById('billAmount').value=res[1];
}

function get_indent_submit(value1,value2,value3,value4,value5,value6)
{
	var url = 'get_indent_submit.php';
	var pars = 'id='+value1+'&idt='+value2+'&sdt='+value3+'&loc='+value4+'&oby='+value5+'&oid='+value6; //alert('ok');
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_submit
	});
}
function show_indent_submit(originalRequest)
{	
	var res=originalRequest.responseText.split('~~',3);
	if(res[0]=="new"){
		alert("New Indent No. = "+res[2]);
		window.location="newindentitem.php?oid="+res[1]+"&ino="+res[2];
	} else if(res[0]=="edit"){
		alert("First Part of the Indent is updated successfully...");
		window.location="newindentitem.php?oid="+res[1]+"&ino="+res[2];
	}
}

function get_indent_item_submit(value1,value2,valueino,value3,value4,value5,value6,value7,value8)
{
    $.post("get_newindentitem_save.php",{id:value7},function(){
       
    })
	var url = 'get_indent_item_submit.php';
	var pars = 'id='+value1+'&oid='+value2+'&ino='+valueino+'&rid='+value3+'&itm='+value4+'&qty='+value5+'&unt='+value6+'&rmk='+value7+'&AnyOther='+value8;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_item_submit
	});
}

function show_indent_item_submit(originalRequest)
{
   
	if(originalRequest.responseText!=""){alert(originalRequest.responseText)};
        location.reload();
//get_indent_item_new(document.getElementById("indid").value);
	//get_indent_items(document.getElementById("indid").value);
}

function get_indent_items(value1)
{
	var url = 'get_indent_items.php';
	var pars = 'oid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_items
	});
}

function show_indent_items(originalRequest)
{
	document.getElementById("spanIndentItemList").innerHTML=originalRequest.responseText;
	document.getElementById("submit").style.display='';
}

function get_indent_item_new(value1)
{
	document.getElementById("xn").value='new';
	document.getElementById("recid").value=0;
	var url = 'get_indent_item_new.php';
	var pars = 'oid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_item_new
	});
}

function show_indent_item_new(originalRequest)
{
	document.getElementById("spanIndentEditItem").innerHTML=originalRequest.responseText;
}

function get_indent_item_edit(value1)
{
	document.getElementById("xn").value='edit';
	document.getElementById("recid").value=value1;
	var url = 'get_indent_item_edit.php';
	var pars = 'rid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_item_edit
	});
}

function show_indent_item_edit(originalRequest)
{
	document.getElementById("spanIndentEditItem").innerHTML=originalRequest.responseText;
}

function get_indent_item_delete(value1)
{
	document.getElementById("xn").value='delete';
	document.getElementById("recid").value=value1;
	var url = 'get_indent_item_delete.php';
	var pars = 'rid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_item_delete
	});
}

function show_indent_item_delete(originalRequest)
{
	document.getElementById("spanIndentEditItem").innerHTML=originalRequest.responseText;
}

function get_indent_send(value1,value2)
{
	document.getElementById("send").style.display = 'none';
	var url = 'get_indent_send.php';
	var pars = 'oid='+value1+'&ino='+value2;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_indent_send
	});
}

function show_indent_send(originalRequest)
{
	var res=originalRequest.responseText.split('~~',3);
	if(res[0]==0){
		alert("Sorry! This order can not send, since having no item!");
		document.getElementById("send").style.display='';
	} else if(res[0]>0){
           
		window.location='msgsend.php?oid='+res[1]+' & ino='+res[2];
	}
}

function get_item_of_indent(value1)
{
	var url = 'get_item_of_indent.php';
	var pars = 'oid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_item_of_indent
	});
}

function show_item_of_indent(originalRequest)
{
	document.getElementById("tdItem").innerHTML = originalRequest.responseText;
      
	get_date_of_indent(document.getElementById("indentNo").value);
}

function get_date_of_indent(value1)
{
	var url = 'get_date_of_indent.php';
	var pars = 'oid='+value1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'post', 
		parameters: pars, 
		onComplete: show_date_of_indent
	});
}

function show_date_of_indent(originalRequest)
{
	document.getElementById("indentDate").value = originalRequest.responseText;
}
