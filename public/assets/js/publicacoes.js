let getStatus = document.querySelectorAll(".casa_status");
for (let i = 0; i < getStatus.length; i++) {
    if (getStatus[i].textContent == "Aprovado") {
        getStatus[i].style.color = "green";
    } else if (getStatus[i].textContent == "Em Análise") {
        getStatus[i].style.color = "#e7bc00";
    }
    else if (getStatus[i].textContent == "Rejeitado") {
        getStatus[i].style.color = "red";
    }
}
