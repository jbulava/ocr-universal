<div id="body-container">
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
		
	// self
	if($uu->id)
	{
		?><div class="self-container"><?php
		if($name)
		{
			?><span id="object-name"><?= $name ?></span>
			<span class="action">
				<a href="<?= $admin_path."edit/".$uu->urls() ?>">EDIT... </a>
			</span>
			<span class="action">
				<a href="<?= $admin_path."delete/".$uu->urls() ?>">DELETE... </a>
			</span><?php
		}
		?></div><?php
	}
		// children		
		$children = $oo->children($uu->id);
		$num_children = count($children);
		?><div id="children"><?php
		if($num_children)
		{
			$pad = floor(log10($num_children)) + 1;
			if($pad < 2)
				$pad = 2;
			for($i = 0; $i < $num_children; $i++)
			{
				$c = $children[$i];
				$j = $i + 1;
				$j_pad = str_pad($j, $pad, "0", STR_PAD_LEFT);
			
				// this is to avoid adding an extra slash
				// in child urls of the root object
				$url = $admin_path."browse/";
				if($uu->urls())
					$url.= $uu->urls()."/";
				$url.= $c["url"];
						
				?><div class="child">
					<span><?= $j_pad ?></span>
					<a href="<?= $url ?>"><?= $c["name1"] ?></a>
				</div><?php
			}
		}
			?><div id="object-actions">
				<span class="action">
					<a href="<?= $admin_path."add/".$uu->urls() ?>">ADD OBJECT... </a>
				</span>
				<span class="action">
					<a href="<?= $admin_path."link/".$uu->urls() ?>">LINK... </a>
				</span>
				<span class="action">
					<a href="<?= $admin_path."copy/".$uu->urls() ?>">COPY... </a>
				</span>
			</div>
		</div>
	</div>
</div>