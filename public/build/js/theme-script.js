
(function () {
  const html = document.documentElement;
  const defaultTheme = "dark";

  try {
    const saved = localStorage.getItem("__THEME_CONFIG__");
    const savedConfig = saved ? JSON.parse(saved) : {};
    html.setAttribute("data-bs-theme", savedConfig.theme || defaultTheme);
  } catch {
    html.setAttribute("data-bs-theme", defaultTheme);
  }
})();

// --- THEME CONFIG (dark by default; user can switch to light via button) ---
const defaults = { theme: "dark" };
let storedConfig = {};
try {
  const saved = localStorage.getItem("__THEME_CONFIG__");
  storedConfig = saved ? JSON.parse(saved) : {};
} catch {
  storedConfig = {};
}
const config = { ...defaults, ...storedConfig };
document.documentElement.setAttribute("data-bs-theme", config.theme);

// --- THEME CUSTOMIZER CLASS ---
class ThemeCustomizer {
  constructor() {
    this.html = document.documentElement;
    this.config = { ...config };
  }

  saveConfig() {
    localStorage.setItem("__THEME_CONFIG__", JSON.stringify(this.config));
    localStorage.setItem("__THEME_LABEL__", this.config.theme === "dark" ? "Dark" : "Light");
  }

  applyTheme(theme) {
    this.config.theme = theme;
    this.html.setAttribute("data-bs-theme", theme);
    this.updateUI();
    this.saveConfig();
  }

  resetTheme() {
    this.applyTheme(defaults.theme);
  }

  updateUI() {
    // Set radio inputs
    document.querySelectorAll("input[name=data-bs-theme]").forEach(radio => {
      radio.checked = radio.value === this.config.theme;
    });

    // Update dropdown label
    const dropdownToggle = document.querySelector(".theme-dropdown .dropdown-toggle");
    const savedLabel = localStorage.getItem("__THEME_LABEL__");
    if (dropdownToggle && savedLabel) dropdownToggle.textContent = savedLabel;

    // Update light/dark button icon and title: dark mode = show sun (click for light), light mode = show moon (click for dark)
    document.querySelectorAll(".light-dark-mode").forEach(btn => {
      const icon = btn.querySelector("i");
      if (icon) {
        icon.className = this.config.theme === "dark" ? "icon-sun fs-16" : "icon-moon fs-16";
      }
      btn.setAttribute("aria-label", this.config.theme === "dark" ? "Switch to light mode" : "Switch to dark mode");
      btn.setAttribute("title", this.config.theme === "dark" ? "Switch to light mode" : "Switch to dark mode");
    });
  }

  initListeners() {
    // Radio buttons
    document.querySelectorAll("input[name=data-bs-theme]").forEach(radio => {
      radio.addEventListener("change", () => this.applyTheme(radio.value));
    });

    // Toggle buttons
    document.querySelectorAll(".light-dark-mode").forEach(btn => {
      btn.addEventListener("click", () => {
        this.applyTheme(this.config.theme === "light" ? "dark" : "light");
      });
    });

    // Reset button
    const resetBtn = document.querySelector("#reset-layout");
    if (resetBtn) resetBtn.addEventListener("click", () => this.resetTheme());
  }

  init() {
    this.updateUI();
    this.initListeners();
  }
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", () => {
  new ThemeCustomizer().init();
});
