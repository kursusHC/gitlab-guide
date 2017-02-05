<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Apache error log</title>
	<style>
	body {font-family: arial, sans-serif; font-size: 14px;color: #515151; background: #fefefe;}
	h1 {padding: 12px ;font-size: 32px;}
	h1 .icon {color: #3293bc;}
	h1 .project {color: #3293bc;font-style: italic;font-weight: normal;}
    p {border-bottom:1px solid #ddd; margin:0;padding:5px 0;}
	#log {padding-left: 12px;}
	.date { color: #3293bc;}
	.time {font-weight: bold;padding: 0 6px;}
	.type {padding: 0 8px; font-style: italic;color: #B21A1A;}
	.message {padding-right: 8px; font-weight: bold;}
	</style>
</head>
<body>
	<h1>Apache error log <span class="icon">&#8673;</span> <span class="project">${PROJECTNAME}</span></h1>
	<div id="log">
		<?php
		error_reporting(0);
		$logfile = array_reverse(file('/var/log/apache2/error.log'));
		$regex = '/^(\[[\w:\s]+\]) (\[.+\]) ([\w\s\/.(")-]+[\-:])\s*>?\s*(.*)$/';
		$count = 0;

		foreach ($logfile as $line) {
			preg_match($regex, $line, $found);
			if (!empty($found)) {
				$date = date_create_from_format('\[D M d H:i:s Y\]', $found[1]);

				$html = '<p>';
				$html.= '<span class="date">'.date_format($date, 'D d/m/Y').'</span>';
				$html.= '<span class="date time">'.date_format($date, 'H:i:s').'</span>';
				// $html.= '<span class="type">'.$found[2].'</span>';
				$html.= '<span class="message">'.rtrim($found[3], ':').'</span>';
				$html.= '<span class="detail">'.$found[4].'</span>';
				$html.= '</p>';
				echo $html;
			}
			if (++$count == 1000) break;
		}
		?>
	</div>
</body>
</html>

