<?php

include_once("twidoooutput.sourceview.inc.php");

class pages extends sourceview {
	
	function className() {
		return "pages";
	}
	
	//gibt zurück, welche Parameter erlabut sind
	function allowedParameters() {
		
		return array_merge(parent::allowedParameters(), array(
			"ajaxLinkSelect"
		));
	}
	
// =============== 
// ! DETAIL VIEW   
// =============== 


//EDITOR AJAX FUNCTIONS
	
	function generateUnixNames($item, $key) {
		
		global $TWIDOO;
		
		if($key == $this->idName())
			$item = $TWIDOO["helpers"]->unixstring($TWIDOO["helpers"]->getUrlPartsById($item), array("/" => true));
	}
	
	
	function sub_ajaxLinkSelect() {
		
		
		global $TWIDOO;
		
		$mediaListSmarty = new Smarty;
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		
		$DATA = $this->leftData();		
		array_walk_recursive($DATA, array($this, 'generateUnixNames'));
		
		$mediaListSmarty->assign("titleName", $this->titleName());
		$mediaListSmarty->assign("idName", $this->idName());
		$mediaListSmarty->assign("array", $DATA);
		$mediaListSmarty->assign("baseurl", $getUrl->getPage()."/".$getUrl->getPageParameters(0));
		$mediaListSmarty->assign("tree", $this->isTree());	
		$mediaListSmarty->assign("includepath", $TWIDOO["includepath"]."/templates/outputs/uielements");
		$mediaListSmarty->assign("ajaxSelector", true);
		
		$ALLDATA = $mediaListSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/helpers/recursiv_list.tpl');
		
		$this->smarty->assign("rightContent", "
			<div id='ajaxContent' class='sourceview'>
				<form>
					<fieldset>
						<legend>Link</legend>
							<div id='linkSelector' class='left'>
								".$ALLDATA."
							</div>
							
							<div class='right'>
               					<input type='text' class='wym_href' value='' size='40' />
               					<input type='text' class='wym_title' value='' size='40' />
			               </div>
			               
			              	<input type='hidden' class='wym_dialog_type' value='Link'/>
			           		<button class='wym_submit bigButton' type='button' value='Submit'><span>Einfügen</span></button>
							<button class='wym_cancel smallButtonSmall' type='button' value='Cancel'><span>Oder doch lieber nicht...</span></button>
			               
               		</fieldset>
               	</form>
			</div>
               	");
	}






	function sub_detail() {
		$this->smarty->assign("rightContent", $this->editCategory());
	}
	
}

return new pages();

?>