<?php

$dataset = array();

$image = imagecreatefrompng('fon2.png');

list($image_width, $image_height) = getimagesize('fon2.png');

// for the height of the image
for($y = 0; $y < $image_height; $y++){
	// for the width of the image
	for($x = 0; $x < $image_width; $x++){
		
		// obtain the pixel color information
		$pixel_color = imagecolorat($image, $x, $y);
		
		// get the color values
		$rgbChannels = imagecolorsforindex ($image, $pixel_color);
		
		$r = $rgbChannels['red'] / 255;
		$g = $rgbChannels['green'] / 255;
		$b = $rgbChannels['blue'] / 255;

		// store the image information in our dataset array
		$dataset[] = array($x/$image_width,$y/$image_height, $r, $g,$b);
	}
 }

// if our dataset array is not empty
if(!empty($dataset)){
	
	// create a file to store the unscaled tranining data
	$f = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'training.data', 'w');
	
	// count the number of training examples = image width * image height
	$number_of_training_examples = count($dataset);
	
	// write the FANN training file header 
	$number_of_inputs = 2;
	$number_of_outputs = 3;
	fwrite($f, "$number_of_training_examples $number_of_inputs $number_of_outputs" . PHP_EOL);

	// for all the pixel information
	foreach($dataset as $pixel_data){
		
		/////////////////////////////////////////////////////////////
		// Write the training data to file here   //
		/////////////////////////////////////////////////////////////
		
		// input: x_cord y_cord
		fwrite($f,  $pixel_data[0] . ' ' . $pixel_data[1] . PHP_EOL);
		
		// output: R G B
		fwrite($f,  $pixel_data[2] . ' ' . $pixel_data[3] . ' ' . $pixel_data[4] . PHP_EOL);
		
		/////////////////////////////////////////////////////////////
		// / Write the training data to file here //
		/////////////////////////////////////////////////////////////
	}
	
	fclose($f);
}