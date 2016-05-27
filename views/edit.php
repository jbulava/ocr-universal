<?php
$browse_url = $admin_path.'browse/'.$uu->urls();

$vars = array("name1", "deck", "body", "notes",  "url", "rank", "begin", "end");

$var_info = array();

$var_info["input-type"] = array();
$var_info["input-type"]["name1"] = "text";
$var_info["input-type"]["deck"] = "textarea";
$var_info["input-type"]["body"] = "textarea";
$var_info["input-type"]["notes"] = "textarea";
$var_info["input-type"]["begin"] = "text";
$var_info["input-type"]["end"] = "text";
$var_info["input-type"]["url"] = "text";
$var_info["input-type"]["rank"] = "text";

$var_info["label"] = array();
$var_info["label"]["name1"] = "Name";
$var_info["label"]["deck"] = "Synopsis";
$var_info["label"]["body"] = "Detail";
$var_info["label"]["notes"] = "Notes";
$var_info["label"]["begin"] = "Begin";
$var_info["label"]["end"] = "End";
$var_info["label"]["url"] = "URL Slug";
$var_info["label"]["rank"] = "Rank";

// return false if object not updated,
// else, return true
function update_object(&$old, &$new, $siblings, $vars)
{
	global $oo;
	
	// set default name if no name given
	if(!$new['name1'])
		$new['name1'] = "untitled";
	
	// add a sort of url break statement for urls that are already in existence
	// (and potentially violate our new rules?)
	$url_updated = urldecode($old['url']) != $new['url'];
	
	if($url_updated)
	{
		// slug-ify url
		if($new['url'])
			$new['url'] = slug($new['url']);
	
		// if the slugified url is empty, 
		// or the original url field is empty,
		// slugify the name of the object
		if(empty($new['url']))
			$new['url'] = slug($new['name1']);
	
		// make sure url doesn't clash with urls of siblings
	
		$s_urls = array();
		foreach($siblings as $s_id)
			$s_urls[] = $oo->get($s_id)['url'];
	
		$new['url'] = valid_url($new['url'], strval($old['id']), $s_urls);
	}	
	// deal with dates
	if(!empty($new['begin']))
	{
		$dt = strtotime($new['begin']);
		$new['begin'] = date($oo::MYSQL_DATE_FMT, $dt);
	}
	
	if(!empty($new['end']))
	{
		$dt = strtotime($new['end']);
		$new['end'] = date($oo::MYSQL_DATE_FMT, $dt);
	}
	
	// check for differences
	$arr = array();
	foreach($vars as $v)
		if($old[$v] != $new[$v])
			$arr[$v] = $new[$v] ?  "'".$new[$v]."'" : "null";
	
	$updated = false;
	if(!empty($arr))
	{
		$updated = $oo->update($old['id'], $arr);
	}
	
	return $updated;
}

?><div id="body-container">
	<div id="body"><?php
	// TODO: this code is duplicated in 
	// + add.php 
	// + browse.php
	// + edit.php
	// + link.php
	// ancestors
	$a_url = $admin_path."browse";
	for($i = 0; $i < count($uu->ids)-1; $i++)
	{
		$a = $uu->ids[$i];
		$ancestor = $oo->get($a);
		$a_url.= "/".$ancestor["url"];
		?><div class="ancestor">
			<a href="<?= $a_url ?>"><?= $ancestor["name1"] ?></a>
		</div><?php
	}
if ($rr->action != "update" && $uu->id)
{
	// get existing image data
	$medias = $oo->media($uu->id);
	$num_medias = count($medias);
	
	// add associations to media arrays:
	// $medias[$i]["file"] is url of media file
	// $medias[$i]["display"] is url of display file (diff for pdfs)
	// $medias[$i]["type"] is type of media (jpg, 
	for($i = 0; $i < $num_medias; $i++)
	{
		$m_padded = "".m_pad($medias[$i]['id']);
		$medias[$i]["file"] = $media_path.$m_padded.".".$medias[$i]["type"];
		if ($medias[$i]["type"] == "pdf")
			$medias[$i]["display"] = $admin_path."media/pdf.png";
		else
			$medias[$i]["display"] = $medias[$i]["file"];
	}
	
	$form_url = $admin_path."edit/".$uu->urls();
// object contents
?><div id="form-container">
		<div class="self">
			<a href="<?= $browse_url ?>"><?= $name ?></a>
		</div>
		<form
			method="post"
			enctype="multipart/form-data" 
			action="<?= $form_url ?>" 
		>
			<div class="form"><?php
				// show object data
				foreach($vars as $var)
				{
				?><div class="field">
					<div class="field-name"><?= $var_info["label"][$var] ?></div>
					<div><?php
						if($var_info["input-type"][$var] == "textarea")
						{
						?><textarea name='<?= $var ?>' class='large'><?php
							if($item[$var])
								echo $item[$var];
						?></textarea><?php
						}
						elseif($var == "url")
						{
						?><input name='<?= $var ?>' 
								type='<?= $var_info["input-type"][$var] ?>'
								value='<?= urldecode($item[$var]) ?>'
						><?php
						}
						else
						{
						?><input name='<?= $var ?>' 
								type='<?= $var_info["input-type"][$var] ?>'
								value='<?= htmlspecialchars($item[$var], ENT_QUOTES) ?>'
						><?php
						}
					?></div>
				</div><?php
				}
				// show existing images
				for($i = 0; $i < $num_medias; $i++)
				{
					$im = str_pad($i+1, 2, "0", STR_PAD_LEFT);
				?><div class="existing-image">
					<div class="field-name">Image <?= $im ?></div>
					<div class='preview'>
						<a href="<?= $medias[$i]['file'] ?>" target="_blank">
							<img src="<?= $medias[$i]['display'] ?>">
						</a>
					</div>
					<textarea name="captions[]"><?= $medias[$i]["caption"]; ?></textarea>
					<span>rank</span>
					<select name="ranks[<?= $i ?>]"><?php
						for($j = 1; $j <= $num_medias; $j++)
						{
							if($j == $medias[$i]["rank"])
							{
							?><option selected value="<?= $j ?>"><?php
								echo $j; 
							?></option><?php
							}
							else
							{
							?><option value="<?= $j ?>"><?php 
								echo $j; 
							?></option><?php
							}
						}
					?></select>
					<label>
						<input
							type="checkbox"
							name="deletes[<?= $i ?>]"
						>
					delete image</label>
					<input 
						type="hidden"
						name="medias[<?= $i ?>]"
						value="<?= $medias[$i]['id'] ?>"
					>
					<input 
						type="hidden"
						name="types[<?= $i ?>]"
						value="<?= $medias[$i]['type'] ?>"
					>
				</div><?php
				}
				// upload new images
				for($j = 0; $j < $max_uploads; $j++)
				{
					$im = str_pad(++$i, 2, "0", STR_PAD_LEFT);
				?><div class="image-upload">
					<span class="field-name">Image <?= $im ?></span>
					<span>
						<input type="file" name="uploads[]">
					</span>
					<!--textarea name="captions[]"><?php
							echo $medias[$i]["caption"];
					?></textarea-->
				</div><?php
				} ?>
				<div class="button-container">
					<input
						type='hidden'
						name='action'
						value='update'
					>	
					<input 
						type='button' 
						name='cancel' 
						value='Cancel' 
						onClick="<?= $js_back ?>" 
					>
					<input 
						type='submit'
						name='submit'  
						value='Update Object'
					>
				</div>
			</div>
		</form>
	</div>
<?php
}
// THIS CODE NEEDS TO BE FACTORED OUT SO HARD
// basically the same as what is happening in add.php
else 
{
	$new = array();
	// objects
	foreach($vars as $var)
	{
		$new[$var] = addslashes($rr->$var);
		$item[$var] = addslashes($item[$var]);
	}
	$siblings = $oo->siblings($uu->id);
	$updated = update_object($item, $new, $siblings, $vars);
	
	// process new media
	$updated = (process_media($uu->id) || $updated);
	
	// delete media
	// check to see if $rr->deletes exists (isset) 
	// because if checkbox is unchecked that variable "doesn't exist" 
	// although the expected behaviour is for it to exist but be null.
	if(isset($rr->deletes))
	{
		foreach($rr->deletes as $key => $value)
		{
			$m = $rr->medias[$key];
			$mm->deactivate($m);
			$updated = true;
		}
	}

	// update caption, weight, rank  
	$num_captions = sizeof($rr->captions);
	if (sizeof($rr->medias) < $num_captions)
		$num_captions = sizeof($rr->medias);

	for ($i = 0; $i < $num_captions; $i++) 
	{
		unset($m_arr);
		$m_id = $rr->medias[$i];
		$caption = addslashes($rr->captions[$i]);
		$rank = addslashes($rr->ranks[$i]);

		$m = $mm->get($m_id);
		if($m["caption"] != $caption)
			$m_arr["caption"] = "'".$caption."'";
		if($m["rank"] != $rank)
			$m_arr["rank"] = "'".$rank."'";

		if($m_arr)
		{
			$arr["modified"] = "'".date("Y-m-d H:i:s")."'";
			$updated = $mm->update($m_id, $m_arr);
		}
	}
	?><div class="self-container"><?php
		// should change this url to reflect updated url
		$urls = array_slice($uu->urls, 0, count($uu->urls)-1);
		$u = implode("/", $urls);
		$url = $admin_path."browse/";
		if(!empty($u))
			$url.= $u."/";
		$url.= $new['url'];
		?><p><a href="<?= $url ?>"><?php echo $new['name1'] ?></a></p><?php
	// Job well done?
	if($updated)
	{
	?><p>Record successfully updated.</p><?php
	}
	else
	{
	?><p>Nothing was edited, therefore update not required.</p><?php
	}
	?></div><?php
} 
?></div>
</div>