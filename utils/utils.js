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

/* ??? */
function toggleSideNav() {
  var menu = document.getElementById("sideContainer");
  var icon = document.getElementById("sidenav-icon");

  if (menu.className === "hideMenu") {
    menu.className = "showMenu";
    icon.className = "fa-solid fa-angle-left";
  } else {
    menu.className = "hideMenu";
    icon.className = "fa-solid fa-angle-right"; 
  }
};

function setMenu() {
  var menu = document.getElementById("menu");
  menu.className = "hideMenu";
};

window.onload = setMenu;