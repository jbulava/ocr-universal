<div id="body-container">
	<div id="body">
		<div id="self-container"><?
		if($rr->action != "update")
		{
		?>
			<form action="<?= $admin_path ?>settings" method="post">
				<span>maximum # of uploads: </span>
				<select name="uploads"><?
					for($i = 5; $i <= 50; $i+= 5)
					{
						if($i == $max_uploads)
						{
						?><option value="<?= $i ?>" selected><?= $i ?></option><?
						}
						else
						{
						?><option value="<?= $i ?>"><?= $i ?></option><?
						}
					}
				?></select>
				<input name='action' type='hidden' value='update'>
				<input name='submit' type='submit' value='update settings'>
			</form><?
		}
		else
		{
			$uploads = $rr->uploads;
			if(!$settings)
				$settings = new ORG_Settings();
			$settings->num_uploads = $uploads;
			$f = serialize($settings);
			file_put_contents($settings_file, $f);
			?><span>maximum number of uploads: <?= $uploads ?></span><?
		}
		?></div>
	</div>
</div>