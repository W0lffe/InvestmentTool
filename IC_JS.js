let ClientData = []; //Initializing array for data that is made with Java
let StockData = []; //Initializing array for data that comes from API request
$(document).ready(function() {
    createClientDataList();
    getStockData(); 

});

$("#paina").click(function(){
        $("#table").slideToggle("slow");
})
$("#table").hide();


/*function: createClientDataList
description: This function fetches investment data that is made with Java from PHP, function will
loop through object properties and append to table*/
function createClientDataList(){
    const method = 1;
    $.get(`https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php?method=${method}`, function(data){
        console.log(data);
        ClientData = data;

        if(data != null){
            $("#paina").html("Data loaded. Press here");
            $("#table").html("");
            const row = $("<tr></tr>").html(`
                <td>#</td>
                <td>Deposit</td>
                <td>Percentage</td>
                <td>Time</td>
                <td>After Intrest</td>
                <td>Earnings</td>
                <td>Volatility</td>`);
                $("#table").append(row);
    
                ClientData.Data.forEach(item => {
                    let vol;
                    if(item.volatility == true && item.type > 0){
                        vol = "Yes";
                    }else{
                        vol = "No";
                    }
            
                    let id = item.id;
                    const row = $("<tr></tr>").html(`
                    <td>${item.id}</td>
                    <td>${item.deposit.toFixed(2)}€</td>
                    <td>${item.percentage.toFixed(2)}%</td>
                    <td>${item.time} ${item.period}</td>
                    <td>${item.afterIntrest.toFixed(2)}€</td>
                    <td>${item.earnings.toFixed(2)}€</td>
                    <td>${vol}</td>
                    <td> <button id="deleteButton" onclick="deleteData(${id})">Delete</button>
                    `);
                    $("#table").append(row);
                });
        };
    });
};

/*function: deleteData 
parameter: id number of selected row
description: this function deletes data from json file, it will send ID as parameter to PHP
wich will handle delete*/
function deleteData(id) {
    const choice = window.confirm("Are you sure you want to delete this data?");
    
    if (choice) {
        console.log("Deleting data for id:", id);

        const dataToDelete = ClientData.findIndex(item => item.id === id);
        console.log("Deleting index: " + dataToDelete);
        
        if (dataToDelete !== -1) {

            $.ajax({
                url: `https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php?id=${id}`,
                method: "DELETE",
                success: function() {
                    console.log("Data deleted successfully");
                    setTimeout(() => { createList(); }, 1000);
                },
                error: function(xhr, error) {
                    console.error("Failed to delete data on the server:", xhr, error);
                }
            });
        } else {
            console.error("Data not found in the array");
        }
    }
}

/*This function will ask for your API key, that will be sent to server wich will fetch latest data */
function getStockData(){
    const method = 2;
    $.get(`https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php?method=${method}`, function(data){
        console.log(data);
        StockData = data;
        createStockDataTables();
    }) 

}

function updateStockData(){
    const apikey = prompt("Please give your API key");
    if(apikey == null || apikey == ""){
        return;
    }
    $.get(`https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php?apikey=${apikey}`, function(data){
        console.log(data);
        getStockData();
    })
}

function createStockDataTables(){


}
