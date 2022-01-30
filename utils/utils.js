function toggleMenu() {
  var menu = document.getElementById("menu");
  var icon = document.getElementById("menu-icon");

  if (menu.className === "hideMenu") {
    menu.className = "showMenu";
    icon.className = "fa-solid fa-xmark";
  } else {
    menu.className = "hideMenu";
    icon.className = "fa-solid fa-bars";
  }
};

function setMenu() {
  var menu = document.getElementById("menu");
  menu.className = "hideMenu";
};

window.onload = setMenu;