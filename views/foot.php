<?php
$generate_url = implode("/", $uu->urls);
$g = $host.$generate_url;
			?><div id="footer-container" class="flex-min">
				<footer class="centre">
						<a class="button" href="<?= $admin_path ?>info">INFO</a>
						<a class="button" href="<?= $g ?>" target="_blank">GENERATE</a>
						<a class="button" href="<?= $admin_path ?>settings">SETTINGS</a>
				</footer>
			</div>
		</div>
	</body>
</html><?php
$db-> close();
?>