function highLightTR(event, trElm, normBackColor, normTextColor, hiLightBackColor, hiLightTextColor){
	var tbdyElm = trElm.parentNode;

	event = (event) ? event : ((window.event) ? window.event : null);

	for (var i = 0; i < tbdyElm.childNodes.length; i++) {
		var node = tbdyElm.childNodes[i]; // a list of tr
		
		//check to see if we are dealign with a table row
		if (typeof(node) != "undefined" && node.tagName == "TR") {

			//if we are dealing with the one that is not clicked 
			if ( node.id != trElm.id) {
				
				//If it is selected and if they don't have crtl or alt help down the un-select it. Otherwise leave it
				if ( ( node.getAttribute("selected") == "true")  && (!(event.ctrlKey || event.metaKey)) ) {
					deselect(node, normBackColor, normTextColor);
				}

			// else this is the node that was clicked
			} else {
				//only for the case that it is selected and they have ctrl and alt pressed then un-select it. 
				if ( (node.getAttribute("selected") == "true") && (event.ctrlKey || event.metaKey)  ) {
					deselect(node, normBackColor, normTextColor);
				} else { 
					select(node, hiLightBackColor, hiLightTextColor);
				}
			}
		}
	}
}

function select(node, hiLightBackColor, hiLightTextColor) {
	node.setAttribute("selected", "true");
	node.bgColor = hiLightBackColor;
	try {
		changeTextColor(node, htLightTextColor);
	} catch(e) {;}
}

function deselect(node, normBackColor, normTextColor) {
	node.setAttribute("selected", "");
	node.bgColor = normBackColor;
	try {
		changeTextColor(node, normTextColor);
	} catch(e) {;}
}

function changeTextColor(elm, color) {
	for (var i = 0; i < elm.cells.length; i++) {
		elm.cells[i].style.color = color;
	}
}

function transfer(fromTblId, toTblId) {
	var fromTblElm = document.getElementById(fromTblId);
	var toTblElm = document.getElementById(toTblId);
	if (fromTblElm && toTblElm) {
		var fromRows = fromTblElm.rows;
		var selectedFromRows = new Array();
		var selectedFromRowIndexes = new Array();
		var j = 0;
		for (var i = 0; i < fromRows.length; i++) {
			var row = fromRows[i];
			if (typeof(row) != "undefined" && row.getAttribute("selected") == "true") {
				selectedFromRows[j] = row;
				selectedFromRowIndexes[j] = row.rowIndex;
				j++;
			}
		}
		if (selectedFromRows.length > 0) {
			for (var i = 0; i < selectedFromRows.length; i++) {
				var selectedFromRow = selectedFromRows[i];
				var selectedFromRowIndex = selectedFromRowIndexes[i] - i;
				// append the row to the to table
				newToRow = toTblElm.insertRow(-1);
				newToRow.id = selectedFromRow.id;
				newToRow.onclick = selectedFromRow.onclick;
				for (var j = 0; j < selectedFromRow.cells.length; j++) {
					newToCell = newToRow.insertCell(-1);
					newToCell.className = selectedFromRow.cells[j].className;
					newToCell.innerHTML = selectedFromRow.cells[j].innerHTML;
				}
				// remove the row from the from table
				fromTblElm.deleteRow(selectedFromRowIndex);
				
			}
		} else {
			//alert("Please select one.");
		}
	}
}

function transferAll(fromTblId, toTblId) {
	var fromTblElm = document.getElementById(fromTblId);
	if ( fromTblElm ) {
	var rows = fromTblElm.rows;
		for (var i = 0; i < rows.length; i++) {
			var row = rows[i];
			if (typeof(row) != "undefined" && row.tagName == "TR") {
				select(row);
			}
		}
	}
	transfer(fromTblId, toTblId);
}

function _submit(min) {
	var selectedContactsTblElm = document.getElementById("selected_contacts");
	if (selectedContactsTblElm) {
		var aRows = selectedContactsTblElm.rows;
		var szRows = aRows.length;
		var contactIds = "";
		if (szRows >= min) {
			for (var i = 0; i < szRows; i++) {
				var row = aRows[i];
				if (typeof(row) != "undefined" && row.id) {
					contactIds += row.id + ",";
				}
			}
			contactIds = contactIds.substr(0, contactIds.length - 1);
			contactIds = "action:'scriptform',<|" + contactIds + "|>";
			//alert(contactIds);
			postwith(contactIds);
		} else {
			alert("Please choose at least " + min + " script(s).");
		}
	}
}

function _submitEdit(min,orderid) {
	var selectedContactsTblElm = document.getElementById("selected_contacts");
	if (selectedContactsTblElm) {
		var aRows = selectedContactsTblElm.rows;
		var szRows = aRows.length;
		var contactIds = "";
		if (szRows >= min) {
			for (var i = 0; i < szRows; i++) {
				var row = aRows[i];
				if (typeof(row) != "undefined" && row.id) {
					contactIds += row.id + ",";
				}
			}
			contactIds = contactIds.substr(0, contactIds.length - 1);
			contactIds = "action:'scriptedit',orderid:" + orderid + ",<|" + contactIds + "|>";
			//alert(contactIds);
			postwith(contactIds);
		} else {
			alert("Please choose at least " + min + " script(s).");
		}
	}
}

function postwith (p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.action = "/admin/" ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}

// only IE support
function changeHighLightTR(event, tblElm) {
	event = (event) ? event : ((window.event) ? window.event : null);
	if (event) {
		var aRows = tblElm.rows;
		var szRows = aRows.length;
		var selectedRowIndex = -1;
		var selectedRow = null;
		for (var i = 0; i < szRows; i++) {
			var row = aRows[i];
			if (typeof(row) != "undefined" && row.getAttribute("selected") == "true") {
				selectedRow = row;
				selectedRowIndex = row.rowIndex;
				break;
			}
		}

		switch (event.keyCode) {
		case 38:
			// UP
			if (selectedRowIndex > 0) {				
				highLightTR(event, aRows[selectedRowIndex - 1], '#ffffff', '#000000', '#316AC5','#ffffff');
			}
			break;
		case 40:
			// DOWN
			if ((selectedRowIndex >= 0) && (selectedRowIndex < szRows - 1)) {				
				highLightTR(event, aRows[selectedRowIndex + 1], '#ffffff', '#000000', '#316AC5','#ffffff');
			}
			break;
		default:
			break;
		}
	}
}

