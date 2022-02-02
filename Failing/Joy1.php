<?php

global $fann, $epochs, $photo, $timeStart, $frameCurrent, $plot;

$dir = 'JoyTest';
$epochs = 5;
$layers = [2, 200, 3];

$fann = fann_create_standard_array(count($layers), $layers);
fann_set_training_algorithm ($fann, FANN_TRAIN_RPROP);

$photo = imagecreatefrompng ('fon2.png');
$image_width = imagesx ($photo);
$image_height = imagesy ($photo);

$plot = imagecreatetruecolor ($image_width, $image_height);

// Plot before training
for ($x = 0; $x < $image_width; ++$x)
{

    for ($y = 0; $y < $image_height; ++$y)
    {
        $answer = fann_run ($fann, [$x / $image_width, $y / $image_height]);
				
        $expectedR = min(max (round ($answer[0] * 255), 0), 255);
        $expectedG = min(max (round ($answer[1] * 255), 0), 255);
        $expectedB = min(max (round ($answer[2] * 255), 0), 255);

        imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));
    }
}
imagepng ($plot, "$dir/START.png");
imagedestroy ($plot);


// Plot training epochs
for ($i = 0; $i < $epochs; ++$i)
{

    $plot = imagecreatetruecolor ($image_width, $image_height);
	
    for ($x = 0; $x < $image_width; ++$x)
    {

        for ($y = 0; $y < $image_height; ++$y)
        {

            $rgb = imagecolorat ($photo, $x, $y);

            $rgbChannels = imagecolorsforindex ($photo, $rgb);

            $r = $rgbChannels['red'] / 255;
            $g = $rgbChannels['green'] / 255;
            $b = $rgbChannels['blue'] / 255;

            fann_train ($fann, [$x / $image_width, $y / $image_height], [$r, $g, $b]);
            $answer = fann_run ($fann, [$x / $image_width, $y / $image_height]);

            $expectedR = min(max (round ($answer[0] * 255), 0), 255);
            $expectedG = min(max (round ($answer[1] * 255), 0), 255);
            $expectedB = min(max (round ($answer[2] * 255), 0), 255);

            imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));
        }
    }

	imagepng ($plot, "$dir/$i.png");
    imagedestroy ($plot);
}


// ANN Not saved or Loaded - Nothing changed should work as above
// Plot post training
$plot = imagecreatetruecolor ($image_width, $image_height);

for ($x = 0; $x < $image_width; ++$x)
{

    for ($y = 0; $y < $image_height; ++$y)
    {
        $answer = fann_run ($fann, [$x / $image_width, $y / $image_height]);
				
        $expectedR = min(max (round ($answer[0] * 255), 0), 255);
        $expectedG = min(max (round ($answer[1] * 255), 0), 255);
        $expectedB = min(max (round ($answer[2] * 255), 0), 255);

        imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));
    }
}
imagepng ($plot, "$dir/STOP.png");
imagedestroy ($plot);


// Unload ANN from memory
fann_save ($fann, "$dir.net");
fann_destroy ($fann);

// Reload ANN
$fann = fann_create_from_file("$dir.net");

// Plot post reload
for ($x = 0; $x < $image_width; ++$x)
{

    for ($y = 0; $y < $image_height; ++$y)
    {
        $answer = fann_run ($fann, [$x / $image_width, $y / $image_height]);
				
        $expectedR = min(max (round ($answer[0] * 255), 0), 255);
        $expectedG = min(max (round ($answer[1] * 255), 0), 255);
        $expectedB = min(max (round ($answer[2] * 255), 0), 255);

        imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));
    }
}
imagepng ($plot, "$dir/AFTERCREATEFROMFILE.png");
imagedestroy ($plot);