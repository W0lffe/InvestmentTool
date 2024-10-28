let Data = [];
$(document).ready(function() {
    createList();   
});

$("#paina").click(function(){
        $("#table").slideToggle("slow");
})
$("#table").hide();

function createList(){
    $.get("https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php", function(data){
        console.log(data);
        Data = data;

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
    
                Data.forEach(item => {
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

function deleteData(id) {
    const choice = window.confirm("Are you sure you want to delete this data?");
    
    if (choice) {
        console.log("Deleting data for id:", id);

        const dataToDelete = Data.findIndex(item => item.id === id);
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
