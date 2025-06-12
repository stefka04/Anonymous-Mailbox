document.querySelectorAll('.menu-option').forEach(element => {
  element.addEventListener('click', function (event) {
      window.location.href = "../../inbox/index.html";
  })
});