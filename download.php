<?php 
	header('Content-Disposition: attachment; filename="download.jpg"');

	$filename = $_GET['filename'];
	$existingFile = "uploads/" . $filename;

	if (file_exists($existingFile) == $filename) {

		$img = imagecreatefromjpeg($existingFile);
		imagejpeg($img); 
	}

	imagedestroy($img);

?>