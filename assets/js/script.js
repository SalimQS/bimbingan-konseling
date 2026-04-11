document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector("#sidebar");
  const toggle = document.querySelector("#sidebarCollapse");

  if (!sidebar || !toggle) {
    return;
  }

  const storageKey = "bk-sidebar-collapsed";
  const collapsed = window.localStorage.getItem(storageKey) === "1";

  if (collapsed) {
    sidebar.classList.add("active");
  }

  toggle.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    window.localStorage.setItem(
      storageKey,
      sidebar.classList.contains("active") ? "1" : "0"
    );
  });
});
