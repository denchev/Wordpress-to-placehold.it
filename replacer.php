<?php

/**
 * Replace any image occurances in Wordpress exported xml file with a link to placehold.it url for the same size.
 */

// A little download helper
if( isset( $_GET['download'] ) ) {

	$file = $_GET['download'];

	if(!file_exists($file)) {
		die('You shall not pass!');
	}

	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
	readfile($file);
	die;
}

if( isset( $_POST['submit'] ) ) {

	// Big files might take a while
	set_time_limit(0);

	$file = $_FILES['xml'];

	// A bit of security check
	if($file['type'] != 'text/xml') {
		die('You shall not pass!');
	}

	$content = file_get_contents( $file['tmp_name'] );

	// Find blog url
	$matches = array();
	preg_match('/<wp:base_blog_url>(.*?)<\/wp:base_blog_url>/', $content, $matches );

	$link = $matches[1];

	$file = $file['name'];
	$file_parts = explode('.', $file);
	$file_ext = array_pop($file_parts);
	$file_name = implode('.', $file_parts);

	// Extract all image files
	$matches = array();
	preg_match_all('/http(s)*:\/\/(.*?)\.(jpg|png|gif)/', $content, $matches);

	$urls = $matches[0];
	$urls = array_unique($urls);

	// Prepare a folder where to download Placehold.it images later to be imported.
	$dir = 'wp-content/uploads/placehold.it';
	if( ! file_exists($dir) ) {
		mkdir($dir);	
	} 

	$counter = 0;

	foreach ($urls as $url) {

		$counter++;

		$image_info = @getimagesize($url);

		// It is not a valid image
		if($image_info === false) {
			echo '<span style="color: red">Image <strong>' . $url . '</strong> skipped. Probably not found.</span><br>';
			continue;
		}

		$width = $image_info[0];
		$height = $image_info[1];

		$placeholdit_new_file = $dir . '/placehold.it-' . $width . 'x' . $height . '.gif';

		if( ! file_exists( $placeholdit_new_file ) ) {
			$placeholdit = 'http://placehold.it/' . $width . 'x' . $height;
			$content = file_get_contents($placeholdit);

			file_put_contents($placeholdit_new_file, $content);
		}
		
		// Show some stats
		echo 'Processed: ' . $url . ' => ' . ($link . '/' . $placeholdit_new_file) . ' => ' . $counter . ' / ' . count($urls) . '<br>';

		$content = str_replace($url, $link . '/' . $placeholdit_new_file, $content);

		flush();
		ob_flush();
	}

	$handle = fopen($file_name . '-replaced.xml', 'a+');
	$write = fwrite($handle, $content);
	fclose($handle);

	if( $write ) {

		echo '<a href="replacer.php?download=' . $file_name . '-replaced.xml' . '">Download new file!</a>';
	} else {

		echo 'Unabled to save new file. Please check permissions.';
	}
}

?>
<!doctype html>
<html>
<head>
	<title>Simple placehold.it replacer for Wordpress</title>
</head>
<body>

<form method="post" enctype="multipart/form-data">
	<input type="file" name="xml">
	<input type="submit" name="submit">
</form>

</body>
</html>
