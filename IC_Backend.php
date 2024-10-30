<?php 

header('Content-Type: application/json');

$DIRECTORY = 'Data';
$FILE_PATH_CLIENT = "$DIRECTORY/data.json";
$FILE_PATH_API = "$DIRECTORY/data_api.json";

$DIRECTORY_INIT = initializeDirectory($DIRECTORY);

if ($DIRECTORY_INIT["Error"]) {
    echo json_encode($DIRECTORY_INIT);
}

//Select function based on Method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    postMethod($FILE_PATH_CLIENT); //POST client data to JSON
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $key = $_GET['apikey'];
    $method = $_GET['method'];
    
    if(!empty($key)){
        getDataFromApi($key, $FILE_PATH_API);
    }   
    if($method == 1){
        getMethod($FILE_PATH_CLIENT, "Client data fetch");   //GET client data from JSON   
    }
    else if($method == 2){
        getMethod($FILE_PATH_API, "API data fetch");   //GET api data from JSON   
    }
   // getMethod($FILE_PATH_CLIENT, "Client data fetch");   //GET client data from JSON   
    //getMethod($FILE_PATH_API, "API data fetch");   //GET api data from JSON   

}
else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    deleteMethod($FILE_PATH_CLIENT); //DELETE client data from JSON
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
        echo json_encode(["Operation" => "Save data","Status " => "Data saved"]);
    } else {
        echo json_encode(["Operation" => "Save data","Status" => "Failed to save data"]);
    }    
}


function getMethod($FILE_PATH, $OPERATION){
    if(file_exists($FILE_PATH) ){
        $DATA = file_get_contents($FILE_PATH); //Get contents of file if it exists

        //Checks if file is empty, if not it will send data
        if(!empty($DATA)){
            
            //$DECODED_DATA = json_decode($DATA, true);
            echo json_encode(["Operation" => $OPERATION, "Status" => "Success: Data fetched!", "Data" => json_decode($DATA,true)]);
            //echo $DATA;
            //echo json_encode(["DATA: " => $DECODED_DATA]);
        }
        else{
            echo json_encode(["Operation" => $OPERATION, "Status" => "Failed: File is empty!"]);  
        }
    }
    else {
        //echo "Data not found!";
        echo json_encode(["Operation" => $OPERATION, "Status" => "Failed: File does not exist!"]);
    }
    
}

function deleteMethod($FILE_PATH){

    //Get id that is sent from client
    $id = $_GET['id'];

    if(file_exists($FILE_PATH)){

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
            echo json_encode(["Operation" => "Data deletion", "Status" => "Success: New data saved"]);
            }
            else{
            echo json_encode(["Operation" => "Data deletion","Status" => "Failed: New data not saved"]);
            }

        } 
        else{
            echo json_encode(["Operation" => "Data deletion", "Status:" => "Failed: ID not found!"]);
        }
    }
    else{
        echo json_encode(["Operation" => "Data deletion", "Status:" => "Failed: File does not exist!"]);

    }
}

function getDataFromApi($key, $FILE_PATH){

    //Symbols for individual stocks and ETF
    $SYMBOLS = ["TSLA", "AMZN", "MSFT", "AMD", "INTC", "QCOM", "NVDA","QDVE.DEX"];
    $DATA_ARRAY = []; //init array for api objects

    //loop symbol array, fetch objects, place to data array
    foreach($SYMBOLS as $symbol){
        $url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$key";
        $response = file_get_contents($url);
        $DATA_ARRAY[$symbol] = json_decode($response, true);
    }

    $status = file_get_contents("https://www.alphavantage.co/query?function=MARKET_STATUS&apikey=$key");
    $DATA_ARRAY["Status"] = json_decode($status, true);
    
    //Encode array to JSON
    $ENCODED_DATA = json_encode($DATA_ARRAY, JSON_PRETTY_PRINT);

    //Place encoded data to file, if successful will call function to fetch JSON to HTML
    if(file_put_contents($FILE_PATH, $ENCODED_DATA)){
        echo json_encode(["Message" => "Fetch new data","Status" => "Success"]);
    }
    else{
        echo json_encode(["Message" => "Fetch new data","Status" => "Failed"]);
    }

}
 
?>
