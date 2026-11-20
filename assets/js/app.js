function toggleModal(id){const m=document.getElementById(id);if(m)m.classList.toggle("show")}
function filterTable(inputId,tableId){const q=(document.getElementById(inputId).value||"").toLowerCase();document.querySelectorAll("#"+tableId+" tbody tr").forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?"":"none")}
document.addEventListener("click",e=>document.querySelectorAll(".modal").forEach(m=>{if(e.target===m)m.classList.remove("show")}));
