(function () {
  const pageMap = {
    "index": "home",
    "": "home",
    "leagues": "leagues",
    "signup": "signupform",
    "signupform": "signupform",
    "signup-form": "signupform",
    "about": "about",
    "contact": "contact",
  };

  function resolvePageKey(pathname) {
    // Strip any extension so this still matches if the host serves
    // extension-less URLs (e.g. "/leagues" instead of "/leagues.php").
    const cleanPath = (pathname || "").split("?")[0].replace(/\/+$/, "");
    const file = cleanPath.split("/").pop() || "index";
    const base = file.replace(/\.[^.]+$/, "").toLowerCase();
    return pageMap[base] || "home";
  }

  function hasRequiredContentSections(doc) {
    const main = doc.getElementById("main-content");
    const header = doc.querySelector(".site-header");
    const nav = doc.getElementById("site-nav");
    return Boolean(main && header && nav);
  }

  function setActiveNavigation() {
    const pageKey = resolvePageKey(window.location.pathname);

    document.querySelectorAll(".site-nav a[data-page]").forEach((link) => {
      if (link.getAttribute("data-page") === pageKey) {
        link.setAttribute("aria-current", "page");
      } else {
        link.removeAttribute("aria-current");
      }
    });
  }

  function wireMobileMenu() {
    const toggle = document.getElementById("menu-toggle");
    const nav = document.getElementById("site-nav");
    if (!toggle || !nav) {
      return;
    }

    toggle.addEventListener("click", () => {
      const expanded = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!expanded));
      nav.classList.toggle("nav-open", !expanded);
    });

    nav.querySelectorAll("a").forEach((a) => {
      a.addEventListener("click", () => {
        nav.classList.remove("nav-open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  function setMainSkipFocus() {
    const main = document.getElementById("main-content");
    if (!main) {
      return;
    }
    window.addEventListener("hashchange", () => {
      if (window.location.hash === "#main-content") {
        main.focus();
      }
    });
  }

  setActiveNavigation();
  wireMobileMenu();
  setMainSkipFocus();

  window.D4EHelpers = {
    resolvePageKey,
    hasRequiredContentSections,
  };
})();
