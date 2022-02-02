<?php


$desired_error = 0.00001;
$max_epochs = 500;
$current_epoch = 0;
$epochs_between_saves = 1; // Minimum number of epochs between saves
$epochs_since_last_save = 0;
$filename = dirname(__FILE__) . "/training.data";

// Initialize psudo mse (mean squared error) to a number greater than the desired_error
// this is what the network is trying to minimize.
$psudo_mse_result = $desired_error * 10000; // 1
$best_mse = $psudo_mse_result; // keep the last best seen MSE network score here

// Initialize ANN
$layers = [2, 300, 100, 3];
$ann = fann_create_standard_array(count($layers), $layers);

if ($ann) {
  echo 'Training ANN... ' . PHP_EOL;
 
  // Configure the ANN
  fann_set_training_algorithm ($ann , FANN_TRAIN_RPROP);
 
  // Read training data
  $train_data = fann_read_train_from_file($filename);
   
  // Check if psudo_mse_result is greater than our desired_error
  // if so keep training so long as we are also under max_epochs
  while(($psudo_mse_result > $desired_error) && ($current_epoch < $max_epochs)){
    $current_epoch++;
    $epochs_since_last_save++; 
 
    // Train one epoch with the training data stored in data.
    $psudo_mse_result = fann_train_epoch ($ann , $train_data );
    echo 'Epoch ' . $current_epoch . ' : ' . $psudo_mse_result . PHP_EOL; // report
   
    // If we haven't saved the ANN in a while...
    // and the current network is better then the previous best network
    // as defined by the current MSE being less than the last best MSE
    // Save it!
    if(($epochs_since_last_save >= $epochs_between_saves) && ($psudo_mse_result < $best_mse)){
     
      $best_mse = $psudo_mse_result; // we have a new best_mse
     
      // Save a Snapshot of the ANN
      fann_save($ann, dirname(__FILE__) . "/RememberMyImage.net");
      echo 'Saved ANN.' . PHP_EOL; // report the save
      $epochs_since_last_save = 0; // reset the count
    }
 
  } // While we're training

  echo 'Training Complete! Saving Final Network.'  . PHP_EOL;
 
  // Save the final network
  fann_save($ann, dirname(__FILE__) . "/RememberMyImage.net"); 
  fann_destroy($ann); // free memory
}
echo 'All Done!' . PHP_EOL;
?>