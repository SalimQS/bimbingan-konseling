window.addEventListener("DOMContentLoaded", () => {
  const classFilter = document.querySelector(".kelasFilter");
  if (!classFilter) {
    return;
  }

  classFilter.addEventListener("change", (event) => {
    const currentUrl = new URL(window.location.href);
    const selectedValue = event.target.value;

    currentUrl.searchParams.delete("page");

    if (selectedValue !== "-1") {
      currentUrl.searchParams.set("kelas", selectedValue);
    } else {
      currentUrl.searchParams.delete("kelas");
    }

    window.location.href = currentUrl.toString();
  });
});
