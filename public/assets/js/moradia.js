if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}

let current_image = 0;
const change_image = function(mode){
    const images = document.querySelectorAll('.detalhes1-images-items');
    if (mode == 'next') {
        current_image++;
        if (current_image >= images.length) current_image = 0; 
        for (let i = 0; i < images.length; i++) {
            if (i == current_image) {
                images[i].style.display = "unset"
                continue;
            }            
            images[i].style.display = "none";    
        }
    } else if (mode == 'prev') {
        current_image--;
        if (current_image <= -1) current_image = (images.length-1); 
        for (let i = 0; i < images.length; i++) {
            if (i == current_image) {
                images[i].style.display = "unset"
                continue;
            }            
            images[i].style.display = "none";
        }
    } else if (mode == 'load') {
        for (let i = 0; i < images.length; i++) {
            if (i == 0) {
                images[i].style.display = "unset"
                continue;
            }            
            images[i].style.display = "none";
        }
    }
}

change_image('load');

const getStatus = document.querySelector(".detalhes1-vendedor-title p");
if (getStatus.textContent == "Aprovado") {
    getStatus.style.color = "green";
} else if (getStatus.textContent == "Em Análise") {
    getStatus.style.color = "#e7bc00";
}
else if (getStatus.textContent == "Rejeitado") {
    getStatus.style.color = "red";
}

const changeImg = function(element){
    if (element.checked) {
        element = element.nextElementSibling.firstChild;
        element.src = element.src.slice(0,-4)+"Blue.png";
    } else {
        element = element.nextElementSibling.firstChild;
        element.src = element.src.slice(0,-8)+".png";
    }
}