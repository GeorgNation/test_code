<?php

global $fann, $epochs, $photo, $timeStart, $frameCurrent, $plot;

$fann = fann_create_from_file ('1.txt');

# fann_randomize_weights ($fann, -1, 1);

# fann_set_training_algorithm ($fann, FANN_TRAIN_RPROP);

$epochs = 500;

$photo = imagecreatefrompng ('fon2.png');

$timeStart = time ();

$trainCSV = fopen ('TRAIN2_MSE.csv', 'a+');

echo 'Frame generation started.' . PHP_EOL;

$frameCurrent = 0;

$plot = imagecreatetruecolor (100, 100);

for ($x = 0; $x < imagesx ($photo); ++$x)
{

	for ($y = 0; $y < imagesy ($photo); ++$y)
	{

		$xNeuro = $x / 100;
		$yNeuro = $y / 100;

		$answer = fann_run ($fann, [$xNeuro, $yNeuro]);

		$expectedR = min(max (round ($answer[0] * 255), 0), 255);
		$expectedG = min(max (round ($answer[1] * 255), 0), 255);
		$expectedB = min(max (round ($answer[2] * 255), 0), 255);

		imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));

		echo 'Epoch: ' . 'START (NOT TRAINING)' . ' | X: ' . $x . ' | Y: ' . $y . ' | Mode: Draw pixel' . PHP_EOL;

	}
}

imagebmp ($plot, '2/START.bmp');

echo 'Epoch: START (NOT TRAINING) | Finished' . PHP_EOL;

fputcsv ($trainCSV, [-1, 1], ';');

fflush ($trainCSV);

imagedestroy ($plot);

for ($i = 0; $i < $epochs; ++$i)
{

	$plot = imagecreatetruecolor (100, 100);

	++$frameCurrent;

	for ($x = 0; $x < imagesx ($photo); ++$x)
	{

		for ($y = 0; $y < imagesy ($photo); ++$y)
		{

			$rgb = imagecolorat ($photo, $x, $y);

			$rgbChannels = imagecolorsforindex ($photo, $rgb);

			$r = $rgbChannels['red'] / 255;
			$g = $rgbChannels['green'] / 255;
			$b = $rgbChannels['blue'] / 255;

			$xNeuro = $x / 100;
			$yNeuro = $y / 100;

			fann_train ($fann, [$xNeuro, $yNeuro], [$r, $g, $b]);
			
			echo 'Epoch: ' . $i . ' | X: ' . $x . ' | Y: ' . $y . ' | MSE: ' . fann_get_MSE ($fann) . ' | Mode: Train' . PHP_EOL;

			$answer = fann_run ($fann, [$xNeuro, $yNeuro]);

			$expectedR = min(max (round ($answer[0] * 255), 0), 255);
			$expectedG = min(max (round ($answer[1] * 255), 0), 255);
			$expectedB = min(max (round ($answer[2] * 255), 0), 255);

			imagesetpixel ($plot, $x, $y, imagecolorallocate ($plot, $expectedR, $expectedG, $expectedB));

			echo 'Epoch: ' . $i . ' | X: ' . $x . ' | Y: ' . $y . ' | Mode: Draw pixel' . PHP_EOL;

		}

	}

	imagebmp ($plot, '2/' . $i . '.bmp');

	echo 'Epoch: ' . $i . ' | Finished' . PHP_EOL;

	imagedestroy ($plot);

	fputcsv ($trainCSV, [$i, fann_get_MSE ($fann)], ';');

	fflush ($trainCSV);

}

echo 'Train and frame generating succefully finished! Time took: ' . (time () - $timeStart);

fann_save ($fann, '2.txt');

fann_destroy ($fann);

fclose ($trainCSV);
