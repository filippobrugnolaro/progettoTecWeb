function toggleMenu() {
  var menu = document.getElementById("menu");
  if (menu.className === "hideMenu") {
    menu.className = "showMenu";
  } else {
    menu.className = "hideMenu";
  }
};

function setMenu() {
  var menu = document.getElementById("menu");
  menu.className = "hideMenu";
};

window.onload = setMenu;