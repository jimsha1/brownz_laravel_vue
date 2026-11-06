function toggleNav() {
  const sidebar = document.getElementById("mySidebar");
  const main = document.getElementById("main");
  sidebar.classList.toggle("closed");
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle("open");
  }
}

///side-bar-dropdown
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".bronwz-dropdown .nav-link-d").forEach((link) => {
    link.addEventListener("click", function () {
      const dropdownContent = link.nextElementSibling; // Get the dropdown-content sibling

      if (dropdownContent.classList.contains("opening")) {
        // If already open, initiate closing
        dropdownContent.classList.remove("opening");
        dropdownContent.classList.add("closing");
      } else {
        // Close all other dropdowns
        document.querySelectorAll(".dropdown-content").forEach((content) => {
          if (content !== dropdownContent) {
            content.classList.remove("opening");
            content.classList.add("closing");
          }
        });

        // Open the current dropdown
        dropdownContent.classList.remove("closing");
        dropdownContent.classList.add("opening");
      }
    });
  });

  // Cleanup after animation ends
  document.querySelectorAll(".dropdown-content").forEach((content) => {
    content.addEventListener("transitionend", function () {
      if (content.classList.contains("closing")) {
        content.classList.remove("closing");
      }
    });
  });
});

function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdown-menu');
  dropdownMenu.classList.toggle('show');
}

// Close dropdown if clicked outside
window.onclick = function (event) {
  if (!event.target.matches('.btn.dropdown-toggle')) {
    const dropdowns = document.getElementsByClassName('dropdown-menu');
    for (let i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
};
