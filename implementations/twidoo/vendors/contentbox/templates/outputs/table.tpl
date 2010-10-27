{* PAGE START *}
{if $page == "start"}

{include 
	file="$includepath/templates/outputs/uielements/elements/table.tpl" 
	fromDatamanagement=true 
	key=$key
	baseurl="$pagePath/$ca_key" 
	mediaPath= $mediaPath
	tableData=$data
	tableName=$name
	fields=$fields
	labels=$labels
	}


{* PAGE EDIT *}
{elseif $page == "edit"}

{include 
	file="$includepath/templates/outputs/uielements/elements/edit.tpl" 
	fromDatamanagement=true 
	key=$key 
	name=$edit_name 
	optionalParameter=$edit_tablename
	tablename=$edit_tablename
	baseurl="$pagePath/$ca_key" 
	tableData=$edit_fields
	edit_data=$edit_data
	mediaPath= $mediaPath
	id=$edit_id
	}
{/if}