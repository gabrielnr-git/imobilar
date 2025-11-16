if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
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

let menuOpenFiltro = false;
const toggleFiltroMenu = function (){

    const getFormContent = document.querySelector(".filtro_form");

    const width = window. innerWidth

    if (width < 1200){ 
        if (!menuOpenFiltro) {
            getFormContent.style.maxHeight = "5000px";
            getFormContent.style.opacity = "1";
            getFormContent.style.visibility = "visible";
            getFormContent.style.overflow = "auto";
            menuOpenFiltro = true;
        } else {
            getFormContent.style.maxHeight = "0px";
            getFormContent.style.opacity = "0";
            getFormContent.style.visibility = "hidden";
            getFormContent.style.overflow = "hidden";
            menuOpenFiltro = false;
        }
    }
}

let mediaQueryFiltro = window.matchMedia('(max-width: 1200px)');
mediaQueryFiltro.addEventListener('change', function (e) {

    const getFormContent = document.querySelector(".filtro_form");

    if (e.matches) {
        getFormContent.style.maxHeight = "0px";
        getFormContent.style.opacity = "0";
        getFormContent.style.visibility = "hidden";
        getFormContent.style.overflow = "hidden";
        menuOpenFiltro = false;
    } else {
        getFormContent.style.maxHeight = "unset";
        getFormContent.style.opacity = "1";
        getFormContent.style.visibility = "visible";
        getFormContent.style.overflow = "auto";
        menuOpenFiltro = false;
    }

});

const formatCep = function(cep){
    const input = document.querySelector("#endereco_cep");
    cep = cep.replace(/\D/g,''); // Replace all chars to numbers
    const size = cep.length; // get input lenght
    if (size>5) { // if is higher than 5 chars
        cep = cep.slice(0,5)+"-"+cep.slice(5,8) // the input get a '-' after the first 5 chars
    }
    input.value = cep; // return the input
}

const blockCep = function (element) {
    const cidade = document.querySelector('#endereco_cidade');
    const estado = document.querySelector('#endereco_estado');
    if (element.value.length > 0) {
        cidade.disabled = true;
        cidade.style.opacity = '0.2';
        cidade.value = '';
        estado.disabled = true;
        estado.style.opacity = '0.2';
        estado.value = '';
    } else {
        cidade.disabled = false;
        cidade.style.opacity = '1';
        estado.disabled = false;
        estado.style.opacity = '1';
    }
}

const formatCurrency = function (input,max) {
    let rawValue = input.value.replace(/[^0-9]/g, '');

    // Handle the case when all numbers are deleted and NaN is generated
    if (rawValue == '' || rawValue == 0) {
        input.value = '';
        filter_overlay(input,'input');
        return 0;
    } else {
        // Limit the raw value to the specified maximum
        rawValue = Math.min(max, parseFloat(rawValue));
    }

    let formattedValue = formatCurrencyValue(rawValue);
    formattedValue = formattedValue.substring(3);
    formattedValue = "R$ " + formattedValue;

    input.value = formattedValue;
}

const formatCurrencyValue = function (rawValue) {
    const numericValue = Number(rawValue) / 100;

    const formattedValue = numericValue.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return formattedValue;
}

const formatNum = function (number){
    if (number.value.length > number.maxLength){
        number.value = number.value.slice(0, number.maxLength);
    } 
}

let saved = [];
// Input
saved['endereco_cep'] = document.querySelector("#endereco_cep").value;
saved['preco_minimo'] = document.querySelector("#preco_minimo").value;
saved['preco_maximo'] = document.querySelector("#preco_maximo").value;
// Select
saved['endereco_cidade'] = document.querySelector("#endereco_cidade").value;
saved['endereco_estado'] = document.querySelector("#endereco_estado").value;
// Check
saved['casa'] = document.querySelector("#casa").checked;
saved['apartamento'] = document.querySelector("#apartamento").checked;
saved['kitnet'] = document.querySelector("#kitnet").checked;
saved['república'] = document.querySelector("#república").checked;
saved['quarto'] = document.querySelector("#quarto").checked;
saved['quarto_compartilhado'] = document.querySelector("#quarto_compartilhado").checked;
saved['dormitório'] = document.querySelector("#dormitório").checked;
saved['pensão'] = document.querySelector("#pensão").checked;
saved['wifi'] = document.querySelector("#wifi").checked;
saved['refeicao'] = document.querySelector("#refeicao").checked;
saved['lazer'] = document.querySelector("#lazer").checked;
saved['estacionamento'] = document.querySelector("#estacionamento").checked;
saved['animais'] = document.querySelector("#animais").checked;

let filter_active = {};
const filter_overlay = function(element,type){
    const dhave = document.querySelector(".dhave button");
    const overlay = document.querySelector(".moradias-overlay");
    const moradias = document.querySelector(".moradias");

    if (type == 'input') {
        if (element.value !== saved[element.id]) {
            filter_active[element.id] = element.id;
        } else {
            delete filter_active[element.id];
        }
    }
    if (type == 'select') {
        if (element.value !== saved[element.id]) {
            filter_active[element.id] = element.id;
        } else {
            delete filter_active[element.id];
        }
    }
    if (type == 'check') {
        if (element.checked != saved[element.id]) {
            filter_active[element.id] = element.id;
        } else {
            delete filter_active[element.id];
        }
    }

    let anchor = document.createElement("a");
    if (Object.keys(filter_active).length > 0) {
        if (dhave === null) {
            moradias.style.filter = "blur(5px)";
            moradias.style.pointerEvents = "none";
            moradias.style.userSelect = "none";
            overlay.style.display = "flex";
        } else {
            dhave.style.display = "unset";
        }
    } else if (Object.keys(filter_active).length == 0) {
        if (dhave === null) {
            moradias.style.filter = "none";
            moradias.style.pointerEvents = "unset";
            moradias.style.userSelect = "unset";
            overlay.style.display = "none";
        } else {
            dhave.style.display = "none";
        }
    }
}
