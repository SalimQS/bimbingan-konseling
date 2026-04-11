document.addEventListener("DOMContentLoaded", () => {
  const kelasFilter = document.querySelector("#kelasFilter");

  if (!kelasFilter) {
    return;
  }

  kelasFilter.addEventListener("change", (event) => {
    const url = new URL(window.location.href);
    url.searchParams.set("page", "peringatan");
    url.searchParams.delete("action");

    if (event.target.value !== "-1") {
      url.searchParams.set("kelas", event.target.value);
    } else {
      url.searchParams.delete("kelas");
    }

    window.location.href = url.toString();
  });
});
  
