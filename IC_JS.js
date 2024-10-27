
let Data = [];
function createList(){

const table = document.getElementById("list");
const row = document.createElement("tr");

table.innerHTML = "";
row.innerHTML = `
        <td>#</td>
        <td>Deposit</td>
        <td>Percentage</td>
        <td>Time</td>
        <td>After Intrest</td>
        <td>Earnings</td>
        <td>Volatility</td>`;
    table.appendChild(row);


fetch("https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php",{
    method: "GET"
    })
    .then(response => {
        return response.json();
    })
    .then(data =>{
        console.log("Fetched data: ");
        console.log(data);
        Data = data;

        Data.forEach(item => {
        let vol;
        if(item.volatility == true){
            vol = "Yes";
        }else{
            vol = "No";
        }

        let id = item.id;
        const row = document.createElement("tr");
        row.innerHTML = `
        <td>${item.id}</td>
        <td>${item.deposit.toFixed(2)}€</td>
        <td>${item.percentage.toFixed(2)}%</td>
        <td>${item.time} ${item.period}</td>
        <td>${item.afterIntrest.toFixed(2)}€</td>
        <td>${item.earnings.toFixed(2)}€</td>
        <td>${vol}</td>
        <td> <button id="deleteButton" onclick="deleteData(${id})">Delete</button>
        `
        table.appendChild(row);
        });
    })
    .catch(error => {
        console.log("error", error);
    })


}

function deleteData(id){
    const choice = window.confirm("Are you sure you want to delete this data?");
    
    if (choice) {
        console.log("Deleting data for id:", id);

        const dataToDelete = Data.findIndex(item => item.id === id);
        console.log("Deleting index: " + dataToDelete);
        
        if (dataToDelete !== -1) {

            fetch(`https://www.cc.puv.fi/~e2301740/IC_Backend/IC_Backend.php?id=${id}`, {
                method: "DELETE"
            })
            .then(response => {
                if (response.ok) {
                    console.log("Data deleted successfully");
                    setTimeout(() => {createList()}, 1000);
                } else {
                    console.error("Failed to delete data on the server");
                }
            })
            .catch(error => {
                console.error("ERROR:", error);
            });
        } else {
            console.error("Data not found in the array");
        }
    }
}

createList();
