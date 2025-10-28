document.addEventListener("DOMContentLoaded", () => {
  const darkModeToggle = document.querySelector(".light-dark-mode");
  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", () => {
      const htmlElement = document.documentElement;
      if (htmlElement.getAttribute("data-layout-mode") === "dark") {
        htmlElement.setAttribute("data-layout-mode", "light");
        localStorage.setItem("darkMode", "disabled");
      } else {
        htmlElement.setAttribute("data-layout-mode", "dark");
        localStorage.setItem("darkMode", "enabled");
      }
    });
  }
});


document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("fullscreen-overlay");
  const isErrorPage = document.body.classList.contains('error-page');

  if (isErrorPage) {
    // No hacemos nada en páginas de error
    return;
  }

  if (!overlay) return;

  // Mostrar overlay solo si modo fullscreen activado en localStorage y no está fullscreen
  if (localStorage.getItem("fullscreenMode") === "enabled" && !document.fullscreenElement) {
    overlay.style.display = "block";

    const tryEnterFullscreen = () => {
      document.documentElement.requestFullscreen()
        .then(() => {
          localStorage.setItem("fullscreenMode", "enabled");
          overlay.style.display = "none";
          overlay.removeEventListener("click", tryEnterFullscreen);
          document.removeEventListener("keydown", tryEnterFullscreen);
        })
        .catch(err => {
          console.error("No se pudo activar fullscreen:", err);
        });
    };

    overlay.addEventListener("click", tryEnterFullscreen);
    document.addEventListener("keydown", tryEnterFullscreen);
  } else {
    overlay.style.display = "none";
  }

  document.addEventListener("fullscreenchange", () => {
    const isFull = !!document.fullscreenElement;

    // Solo actualizamos localStorage si no estamos en página de error
    if (!isErrorPage) {
      localStorage.setItem("fullscreenMode", isFull ? "enabled" : "disabled");
    }

    overlay.style.display = isFull ? "none" : (localStorage.getItem("fullscreenMode") === "enabled" ? "block" : "none");
  });
});





