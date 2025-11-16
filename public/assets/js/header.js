let menuActive = false;

let toggleHeaderMenu = function () {
    let getHeaderButton = document.querySelector(".mobile-menu-button img");
    let getHeaderMain = document.querySelector(".header-main");
    let getHeaderLogo = document.querySelector(".header-logo img");
    let getOverlay = document.querySelector(".mobile-menu-overlay");

    if (!menuActive) {
        getHeaderButton.src = headerExitImg;
        getHeaderLogo.src = headerLogoWhiteImg;
        getHeaderMain.style.width = "100%";
        getOverlay.style.display = "flex";
        document.body.style.overflow = "hidden";
        menuActive = true;
    } else {
        getHeaderButton.src = headerMenuImg;
        getHeaderLogo.src = headerLogoBlueImg;
        getHeaderMain.style.width = "0%";
        getOverlay.style.display = "none";
        document.body.style.overflow = "unset";
        menuActive = false;
    }
}

let mediaQueryHeader = window.matchMedia('(min-width: 993px)');
mediaQueryHeader.addEventListener('change', function (e) {

    let getHeaderButton = document.querySelector(".mobile-menu-button img");
    let getHeaderMain = document.querySelector(".header-main");
    let getHeaderLogo = document.querySelector(".header-logo img");
    let getOverlay = document.querySelector(".mobile-menu-overlay");

    if (e.matches) {
        getHeaderMain.style.width = "100%";
        getHeaderButton.src = headerMenuImg;
        getHeaderLogo.src = headerLogoBlueImg;
        getOverlay.style.display = "none";
        document.body.style.overflow = "unset";
        menuActive = false;
    } else {
        getHeaderMain.style.width = "0%";
    }
});

let dropdownActive = false;

let toggleDropdown = function () {
    let getDropdown = document.querySelector(".header-dropdown");

    if (!dropdownActive) {
        getDropdown.style.maxHeight = getDropdown.scrollHeight+"px";
        dropdownActive = true;
    } else {
        getDropdown.style.maxHeight = "0px";
        dropdownActive = false;
    }
}