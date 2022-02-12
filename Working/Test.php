<?php

// Load ANN
$train_file = (dirname(__FILE__) . "/RememberMyImage.net");
$ann = fann_create_from_file($train_file);
if ($ann) {

    $image_x = 100;
    $image_y = 100;

    $image = imagecreatetruecolor($image_x, $image_y);
     
	// for the height of the image
	for($y = 0; $y < $image_y; $y++){
		
		// for the width of the image
		for($x = 0; $x < $image_x; $x++){

			$result = fann_run($ann, [$x/$image_x,$y/$image_y]);

			$expectedR = min(max (round ($result[0] * 255), 0), 255);
            $expectedG = min(max (round ($result[1] * 255), 0), 255);
            $expectedB = min(max (round ($result[2] * 255), 0), 255);
			
			imagesetpixel($image, $x,$y, imagecolorallocate($image, $expectedR, $expectedG, $expectedB));

		}
	}
		
	// destroy image resources
    imagepng($image, "test.png", 0);
    imagedestroy($image);

    // Destroy ANN
    fann_destroy($ann);
} else {
    die("Invalid file format" . PHP_EOL);
}
?>