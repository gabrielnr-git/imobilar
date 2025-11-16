if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

let formatPhoneNumber = function() {
    let inputValue = phoneNumberInput.value.replace(/\D/g, ''); // Remove non-numeric characters

    if (inputValue.length > 2) {
      inputValue = `(${inputValue.substring(0, 2)}) ${inputValue.substring(2)}`;
    }

    if (inputValue.length > 10) {
      inputValue = `${inputValue.substring(0, 10)}-${inputValue.substring(10)}`;
    }

    if (inputValue.length > 15) {
      inputValue = inputValue.substring(0, 15);
    }

    phoneNumberInput.value = inputValue;
}
const phoneNumberInput = document.getElementById('input_phone');
phoneNumberInput.addEventListener('input', formatPhoneNumber);
