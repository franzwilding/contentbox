
var curPopupObject = null;
var lastAjaxLoad = Array;

$(document).ready(function(){


// ====================== 
// ! MAIN CLICK HANDLER   
// ====================== 
	$("html").click(function(e){
		
		//wenn gerade ein Popup offen ist
		if(curPopupObject != null) {
			var boxX = curPopupObject.offset().left;
			var boxY = curPopupObject.offset().top;
			var boxX1 = curPopupObject.offset().left + curPopupObject.width()+10;
			var boxY1 = curPopupObject.offset().top + curPopupObject.height()+10;

			//wenn wir uns NICHT im popup befinden
			if(!(e.pageX >= boxX && e.pageX <= boxX1) || !(e.pageY >= boxY && e.pageY <= boxY1)) {
			
				curPopupObject.slideToggle("fast");
				curPopupObject = null;
			}
		}
	});
	

// MAINFANCYBOX
	$("a.fancy").fancybox({
		'titleShow'		: false
	});


// ========================= 
// ! NAVIGATION SCROLLWITH   
// ========================= 
	var navigationChildWidth = 0;
	$("#ulWrapper ul li").each(function(index){
		navigationChildWidth += $(this).outerWidth()+21;
	});
	
	$("#ulWrapper ul").css("width", navigationChildWidth);

	

// ========================= 
// ! TABLE DELETE FUNCTION   
// ========================= 
	$("form table td.delete input[type=checkbox]").change(function(){

		
		//wenn gerade kein Popup offen ist
		if($(this).parent().parent().parent().parent().next("p.table_delete").css("display") == "none") {
			popup(
				$(this).parent().parent().parent().children("tr:last-child").children("td.delete"), 
				$(this).parent().parent().parent().parent().next("p.table_delete"), 
				"below_right", 
				"", 
				true
			);
		}
				
		//wenn dann keine Checkbox mehr selektiert ist, dann schließen wir das ganze wieder
		if($(this).parent().parent().parent().find("tr td.delete input[type=checkbox]:checked").length == 0) {
			$(this).parent().parent().parent().parent().next("p.table_delete").slideToggle("fast", function(){
				$(this).parent().parent().parent().parent().next("p.table_delete").removeClass("popupstyle");
			});
		}
		
	});	
	

// ========================= 
// ! TABLE TOGGLE FUNCTION   
// ========================= 
	$("a.toggleTable").click(function(){
		$(this).parent().next().slideToggle("slow");
		$(this).toggleClass("closed");
	});
	
	
// ================================ 
// ! SOURCEVIEW MAIN ADD FUNCTION   
// ================================ 
	$(".sourceview .left h2 a").attr("href", "javascript:;");
	$(".sourceview .left h2 a").click(function(){
		$(this).parent().parent().children("form.addRootElement").slideToggle("fast");
		

		if($(this).html() == "+ Hinzufügen") {
			$(this).html("Doch nicht...");
		}
		
		else if($(this).html() == "Doch nicht...")
			$(this).html("+ Hinzufügen");
			
	});
	
	
	
	
	
	
	// ============= 
// ! WYMEDITOR   
// ============= 
    jQuery('.wymeditor').wymeditor({
    	lang: 'de', 
        updateSelector: "button", 
        updateEvent:    "click", 
        toolsItems: [
		    {'name': 'Bold', 'title': 'Strong', 'css': 'wym_tools_strong'}, 
		    {'name': 'Italic', 'title': 'Emphasis', 'css': 'wym_tools_emphasis'},
		    {'name': 'CreateLink', 'title': 'Link', 'css': 'wym_tools_link'},
		    {'name': 'Unlink', 'title': 'Unlink', 'css': 'wym_tools_unlink'},
		    {'name': 'InsertImage', 'title': 'Image', 'css': 'wym_tools_image'},
		    {'name': 'InsertOrderedList', 'title': 'Ordered_List',
		        'css': 'wym_tools_ordered_list'},
		    {'name': 'InsertUnorderedList', 'title': 'Unordered_List',
		        'css': 'wym_tools_unordered_list'},
		    {'name': 'Paste', 'title': 'Paste_From_Word',
		        'css': 'wym_tools_paste'},
			{'name': 'Superscript', 'title': 'Superscript', 'css': 'wym_tools_superscript'},		 
		    {'name': 'Subscript', 'title': 'Subscript', 'css': 'wym_tools_subscript'},		 
		    {'name': 'Undo', 'title': 'Undo', 'css': 'wym_tools_undo'},
		    {'name': 'Redo', 'title': 'Redo', 'css': 'wym_tools_redo'}
	    ], 
	    containersItems: [
		    {'name': 'P', 'title': 'Paragraph', 'css': 'wym_containers_p'},
		    {'name': 'H2', 'title': 'Heading_2', 'css': 'wym_containers_h2'},
		    {'name': 'H3', 'title': 'Heading_3', 'css': 'wym_containers_h3'},
		    {'name': 'H4', 'title': 'Heading_4', 'css': 'wym_containers_h4'},
		    {'name': 'H5', 'title': 'Heading_5', 'css': 'wym_containers_h5'},
		    {'name': 'BLOCKQUOTE', 'title': 'Blockquote', 'css': 'wym_containers_blockquote'}
	    ],
	    classesHtml:    "<div class='wym_containers wym_section''>"
                        + "<h2>{Classes}</h2><ul>"
                        + WYMeditor.CLASSES_ITEMS
                        + "</ul></div>",
	    /*classesItems: [
	    	{'name': 'Frage', 'title': 'Frage', 'css': 'wym_class'}
	    ],*/ 
	    boxHtml:   "<div class='wym_box'>"
              + "<div class='wym_area_top'>"
              + WYMeditor.TOOLS
              + WYMeditor.CONTAINERS
              /*+ WYMeditor.CLASSES*/
              + "</div>"
              + "<div class='wym_area_main'>"
              + WYMeditor.HTML
              + WYMeditor.IFRAME
              + "</div>"
              + "</div>", 
    	postInit: function(wym) {
    		wym.hovertools();
    	}, 
    	    	
    	dialogImageHtml:  "<body class='wym_dialog wym_dialog_image'>"
               + '<script type="text/javascript">$.ajax({url: "/contentbox/content/10/ajaxImageSelect/", type: "GET",  cache: false, success: function(html) { $(".wym_dialog").append($(html).find("#ajaxContent")); WYMeditor.INIT_DIALOG(' + WYMeditor.INDEX + '); }});</script>', 
        
        dialogLinkHtml:  "<body class='wym_dialog wym_dialog_link'>"
               + '<script type="text/javascript">$.ajax({url: "/contentbox/content/2/ajaxLinkSelect/", type: "GET",  cache: false, success: function(html) { $(".wym_dialog").append($(html).find("#ajaxContent")); WYMeditor.INIT_DIALOG(' + WYMeditor.INDEX + '); }});</script>'               
    });
    
    //adding the incredible hover effeckt
    $('.wym_tools ul').children().each(function(){
    	var text = $(this).find("a").text();
    	$(this).find("a").html("<span><span>"+text+"</span></span>");
    });





 
// =================
// ! FOTO SELECTION   
// =================

	$("#allMedia li ul.medialist li p.details a").live("click",function(){		
		$(this).parent().parent().parent().children().removeClass("selected");
		
		$(this).parent().parent().parent().children().each(function(){
			$(this).find("p.details a").html("Übernehmen");
		});
		
		$(this).parent().parent().addClass("selected");
		$(this).html("Ausgewählt!");
		
		var form = $(this).parent().parent().parent().parent().parent().parent().parent();
		
		form.find("input.wym_src").attr("value", $(this).parent().prev().attr("href"));
		form.find("input.wym_alt").attr("value", $(this).parent().prev().find("img").attr("alt"));
		form.find("input.wym_title").attr("value", $(this).parent().prev().find("img").attr("title"));
	});
 
 
 
// =================
// ! LINK SELECTION   
// =================

	$("#linkSelector ul li a").live("click",function(){	
		
		
		var form = $(this).parent().parent().parent().parent().parent().parent().parent();
		
		form.find("input.wym_href").attr("value", $(this).attr("rel"));
		form.find("input.wym_title").attr("value", $(this).html());
	});
	
	
	
	 
 
 
    
// ========================== 
// ! DATE & DATETIME PICKER   
// ========================== 
	$('form.edit li.date input').datepicker({
		dateFormat: 'dd.mm.yy'
	});
							
	$('form.edit li.datetime input').datepicker({
		dateFormat: 'dd.mm.yy'
	});

});






// ====================== 
// ! SETTINGS FUNCTIONS   
// ====================== 
function settings_addTable() {
	$("#contentboxdetails ul li.tables ul li:first-child").clone().insertBefore("#contentboxdetails ul li.tables ul li:last-child");
	$("#contentboxdetails ul li.tables ul li:last-child").prev().show("slow");
}


function popup(caller, elementSelector, position, noround, handleclickmyself, offsetLeft, offsetTop) {
		
		setTimeout(function(){
									
			//getting the position of the caller
			var left = caller.offset().left;
			var top = caller.offset().top;
			var height = caller.outerHeight();
			var width = caller.outerWidth();
			
			if(offsetLeft != null) left += offsetLeft;
			if(offsetTop != null) top += offsetTop;
			
			var poptop = 0;
			var popleft = 0;
			
			elementSelector.append('<span class="arrow"></span>');
			
			if(position == "below_right") {
				
				poptop = top+height+11;
				popleft = left-elementSelector.width()+20;
				elementSelector.addClass("top");
				
				elementSelector.children("span.arrow").css("right", (width/2)-4);
			}
			
			if(position == "right_above") {
				poptop = top-11;
				popleft = left+width+20;
				elementSelector.addClass("left");
				
				elementSelector.children("span.arrow").css("top", (height/2)+3);
			}
			
			elementSelector.css("left", popleft);
			elementSelector.css("top", poptop);
			elementSelector.css("width", elementSelector.width());
			
			elementSelector.addClass("popupstyle");
			
			//wenn wir die Roundness selber einstellen wollen
			if(noround == "top") { elementSelector.addClass("noroundtop"); }
			
	
			
			//wenn das "popup-schließen" selber in die Hand genommen wird
			if(handleclickmyself != true) curPopupObject = elementSelector;
			
			elementSelector.slideToggle("fast");
		
		}, 1);
	}

function myAjax(caller, loadUrl, loadingPosition, appendPosition, sendData, onlyOnce, deleteCaller) {
	
	if((onlyOnce && lastAjaxLoad[loadUrl] != sendData) || !onlyOnce) {
		
		lastAjaxLoad[loadUrl] = sendData;

		//getting the position of the caller
		var left = caller.offset().left;
		var top = caller.offset().top;
		var height = caller.outerHeight();
		var width = caller.outerWidth();
		
		if(loadingPosition == "inside") {

			if(!caller.next().hasClass("loading"))
				caller.after('<span class="loading">Laden...</span>');
				
			caller.next().css("top", top+6);
			caller.next().css("left", left+width-23);
			caller.next().css("position", "absolute");
			
			
		}

		else if(loadingPosition == "inline") {
			
			if(!caller.next().hasClass("loading"))
				caller.after('<span class="loading">Laden...</span>');
			
			var marginTop = caller.css("margin-top").split("px");
			
			caller.next().css("float", "left");
			caller.next().css("margin-left", 7);
			caller.next().css("margin-top", (parseInt(marginTop[0]) + (height/2) - 7));
			
		}
		
		if(sendData == "") myType = "GET"; 
		else myType = "POST";
		
		$.ajax({
			url: loadUrl,
			cache: false, 
			data: sendData, 
			type: "POST", 
			success: function(html) {
				
				if(deleteCaller != null)
					deleteCaller.fadeOut();						
				
				if(appendPosition.children(".ajaxParse")) {
					appendPosition.children(".ajaxParse").nextAll().remove();
					}
				else
					appendPosition.html('');
				
				
				appendPosition.children().eq(-1).addClass("ajaxParse");
				
				var result = jQuery.parseJSON(html);
				
				
				appendPosition.append(result.content);
				
				caller.next().removeClass("success fail");
				if(result.success) {
					
					
					caller.next().addClass("success");
				}
				else
					caller.next().addClass("fail");
			}
		});	
		
	}
}

	
//Return all names of Checkboxes, checked
function returnCheckedBoxes(searchArea) {

	var allVals = "";
	searchArea.find('input[type=checkbox]:checked').each(function() {
		allVals += ";"+$(this).attr("name");
	});
	return allVals;
}
	
	
	
	
	