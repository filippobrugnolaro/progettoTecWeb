function toggleMenu() {
  var menu = document.getElementById("menu");
  if (menu.className === "hideMenu") {
    menu.className = "showMenu";
  } else {
    menu.className = "hideMenu";
  }
}