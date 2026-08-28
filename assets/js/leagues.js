(function () {
  const listNode = document.getElementById("leagues-list");
  if (!listNode) {
    return;
  }

  function isValidResource(resource, expectedType) {
    if (!resource || resource.type !== expectedType || !resource.label) {
      return false;
    }
    if (resource.available && !resource.url) {
      return false;
    }
    if (!resource.available && !resource.unavailableReason) {
      return false;
    }
    return true;
  }

  function buildResourceItem(resource) {
    if (resource.available) {
      const rel = resource.target === "_blank" ? "noopener noreferrer" : "noopener";
      const target = resource.target || "_blank";
      return `<a href="${resource.url}" target="${target}" rel="${rel}">${resource.label}</a>`;
    }
    return `<span class="badge-muted" title="${resource.unavailableReason}">${resource.label}: Unavailable</span>`;
  }

  function renderLeagues(leagues) {
    if (!Array.isArray(leagues) || leagues.length === 0) {
      listNode.dataset.state = "empty";
      listNode.innerHTML = '<p class="status-message">No leagues are published yet. Please check back soon.</p>';
      return;
    }

    const cards = leagues
      .filter((league) => league && league.id && league.name)
      .map((league) => {
        const scheduleOk = isValidResource(league.schedule, "schedule");
        const statsOk = isValidResource(league.stats, "stats");

        if (!scheduleOk || !statsOk) {
          return `
            <article class="league-card">
              <h2>${league.name || "League"}</h2>
              <p class="status-message">League resource data is incomplete. Please contact organizers.</p>
            </article>
          `;
        }

        return `
          <article class="league-card">
            <h2>${league.name}</h2>
            <p class="league-meta">${league.season || "Current Season"}</p>
            <div class="link-row">
              ${buildResourceItem(league.schedule)}
              ${buildResourceItem(league.stats)}
            </div>
          </article>
        `;
      })
      .join("");

    listNode.dataset.state = "ready";
    listNode.innerHTML = cards;
  }

  function renderError() {
    listNode.dataset.state = "error";
    listNode.innerHTML = '<p class="status-message">Unable to load league info right now. Please try again later.</p>';
  }

  fetch("data/leagues.json", { cache: "no-store" })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to load league data.");
      }
      return response.json();
    })
    .then((payload) => renderLeagues(payload.leagues))
    .catch(() => renderError());
})();
