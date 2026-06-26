function showTable(id){
    document.querySelectorAll(".table-section").forEach(t => t.style.display="none");
    document.getElementById(id).style.display="block";
}
function searchTable(inputId, tableId) {
    let input = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll("#"+tableId+" tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}