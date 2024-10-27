<?php 

header('Content-Type: application/json');

$DIRECTORY = 'Data';
$FILE_PATH = "$DIRECTORY/data.json";

$DIRECTORY_INIT = initializeDirectory($DIRECTORY);

if ($DIRECTORY_INIT["Error"]) {
    echo json_encode($DIRECTORY_INIT);
}

//Select function based on Method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    postMethod($FILE_PATH); //POST data to JSON
}else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getMethod($FILE_PATH);   //GET JSON data
}else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    deleteMethod($FILE_PATH); //DELETE data from JSON
}


function initializeDirectory($DIRECTORY) {
    //IF Directory doesnt exist -> make new, if not successful return error, else success
    if (!is_dir($DIRECTORY) && !mkdir($DIRECTORY, 0765, true) ) {
        return ["Error: " => "Failed to create directory!"];
    }
    return ["Success: " => "Directory created/exists!"];
}

function postMethod($FILE_PATH) {
    $DATA_FROM_CLIENT = file_get_contents("php://input");
    
    // Initialize an array to hold the existing data
    $DATA_ARRAY = [];

    // Check if file exists already
    if (file_exists($FILE_PATH)) {
        // Get existing data from File
        $EXISTING_DATA = file_get_contents($FILE_PATH);
        // Place decoded data to this variable
        $DATA_ARRAY = json_decode($EXISTING_DATA, true);
    }

    // Determine the next ID
    $nextID = 1; // Default to 1 if the array is empty
    if (!empty($DATA_ARRAY)) {
        // Extract IDs and find the maximum
        $ids = array_column($DATA_ARRAY, 'id');
        $nextID = max($ids) + 1; // Increment the maximum ID by 1
    }

    // Decode incoming data from client and add the new ID
    $newData = json_decode($DATA_FROM_CLIENT, true);
    $newData['id'] = $nextID; // Assign the new ID to the incoming data

    // Add the new data to the array
    $DATA_ARRAY[] = $newData;

    // Encode data
    $NEW_DATA = json_encode($DATA_ARRAY, JSON_PRETTY_PRINT);
    
    // Save new data to file
    if (file_put_contents($FILE_PATH, $NEW_DATA)) {
        echo json_encode(["Saved data:" => $NEW_DATA]);
    } else {
        echo json_encode(["Message:" => "Failed to save data"]);
    }    
}


function getMethod($FILE_PATH){
    if(file_exists($FILE_PATH) ){
        $DATA = file_get_contents($FILE_PATH); //Get contents of file if it exists

        //Checks if file is empty, if not it will send data
        if(!empty($DATA)){
            
            //$DECODED_DATA = json_decode($DATA, true);
            echo $DATA;
            //echo json_encode(["DATA: " => $DECODED_DATA]);
        }
        else{
            echo json_encode(["status" => "error", "message" => "File is empty!"]);  
        }
    }
    else {
        //echo "Data not found!";
        echo json_encode(["status" => "error", "message" => "File does not exist!"]);
    } 
}

function deleteMethod($FILE_PATH){

    //Get id that is sent from client
    $id = $_GET['id'];

    //Decode existing data
    $DECODED_DATA = json_decode(file_get_contents($FILE_PATH), true);

    //Look for ID from array and set to variable
    $index = array_search($id, array_column($DECODED_DATA, 'id'));

    //Checks if index IS NOT false
    if($index !== false){
        //Delete data from decoded data from this array index
        array_splice($DECODED_DATA, $index, 1);

        foreach ($DECODED_DATA as $newID => &$item) {
            $item['id'] = $newID + 1; // Update the ID directly
        }
        //Encode data
        $ENCODED_DATA = json_encode($DECODED_DATA, JSON_PRETTY_PRINT);

        //Write updated data back to file
        if(file_put_contents($FILE_PATH, $ENCODED_DATA)){
        echo json_encode(["Saved data:" => $NEW_DATA]);
        }
        else{
        echo json_encode(["Message:" => "Failed to delete data"]);
        }

    } 
    else{
        echo json_encode(["Message:" => "ID not found!"]);
    }

}


?>
