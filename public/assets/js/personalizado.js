document.addEventListener("DOMContentLoaded", () => {
  const htmlElement = document.documentElement;
  const darkModeToggle = document.querySelector(".light-dark-mode");

  const toggleBrandLogos = (isDarkMode) => {
    document.querySelectorAll("#page-topbar .logo-dark, .app-menu .logo-dark").forEach((el) => {
      el.style.display = isDarkMode ? "none" : "inline-block";
    });
    document.querySelectorAll("#page-topbar .logo-light, .app-menu .logo-light").forEach((el) => {
      el.style.display = isDarkMode ? "inline-block" : "none";
    });
  };

  const applyModePreferences = (mode) => {
    const normalizedMode = mode === "dark" ? "dark" : "light";

    if (htmlElement.getAttribute("data-layout-mode") !== normalizedMode) {
      htmlElement.setAttribute("data-layout-mode", normalizedMode);
    }

    const desiredSidebar = normalizedMode === "dark" ? "dark" : "light";
    if (htmlElement.getAttribute("data-sidebar") !== desiredSidebar) {
      htmlElement.setAttribute("data-sidebar", desiredSidebar);
    }

    if (htmlElement.getAttribute("data-topbar") !== "light") {
      htmlElement.setAttribute("data-topbar", "light");
    }

    localStorage.setItem("darkMode", normalizedMode === "dark" ? "enabled" : "disabled");
    toggleBrandLogos(normalizedMode === "dark");
  };

  const loadPreferredMode = () => {
    const storedPreference = localStorage.getItem("darkMode");
    if (storedPreference === "enabled") {
      return "dark";
    }
    if (storedPreference === "disabled") {
      return "light";
    }
    return htmlElement.getAttribute("data-layout-mode") || "light";
  };

  applyModePreferences(loadPreferredMode());

  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", () => {
      const nextMode = htmlElement.getAttribute("data-layout-mode") === "dark" ? "light" : "dark";
      applyModePreferences(nextMode);
    });
  }

  const modeObserver = new MutationObserver((records) => {
    const hasModeChange = records.some((record) => record.attributeName === "data-layout-mode");
    if (hasModeChange) {
      applyModePreferences(htmlElement.getAttribute("data-layout-mode"));
    }
  });

  modeObserver.observe(htmlElement, { attributes: true, attributeFilter: ["data-layout-mode"] });
});


document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("fullscreen-overlay");
  const isErrorPage = document.body.classList.contains("error-page");

  if (isErrorPage) {
    // No hacemos nada en páginas de error
    return;
  }

  if (!overlay) return;

  // Mostrar overlay solo si modo fullscreen activado en localStorage y no está fullscreen
  if (localStorage.getItem("fullscreenMode") === "enabled" && !document.fullscreenElement) {
    overlay.style.display = "block";

    const tryEnterFullscreen = () => {
      document.documentElement
        .requestFullscreen()
        .then(() => {
          localStorage.setItem("fullscreenMode", "enabled");
          overlay.style.display = "none";
          overlay.removeEventListener("click", tryEnterFullscreen);
          document.removeEventListener("keydown", tryEnterFullscreen);
        })
        .catch((err) => {
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

    if (!isErrorPage) {
      localStorage.setItem("fullscreenMode", isFull ? "enabled" : "disabled");
    }

    overlay.style.display = isFull ? "none" : localStorage.getItem("fullscreenMode") === "enabled" ? "block" : "none";
  });
});
