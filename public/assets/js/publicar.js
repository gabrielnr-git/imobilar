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

const formatCep = function(cep){
    const input = document.querySelector("#input_cep");
    cep = cep.replace(/\D/g,''); // Replace all chars to numbers
    const size = cep.length; // get input lenght
    if (size>5) { // if is higher than 5 chars
        cep = cep.slice(0,5)+"-"+cep.slice(5,8) // the input get a '-' after the first 5 chars
    }
    input.value = cep; // return the input
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

let formatNum = function (number){
    if (number.value.length > number.maxLength){
        number.value = number.value.slice(0, number.maxLength);
    } 
}
