window.addEventListener("DOMContentLoaded", () => {
  const deleteForms = document.querySelectorAll(".formDelete");

  deleteForms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();

      const currentForm = event.currentTarget;
      const studentName = currentForm.getAttribute("nama-siswa") || "siswa ini";

      if (typeof Swal === "undefined") {
        currentForm.submit();
        return;
      }

      Swal.fire({
        title: "Anda yakin?",
        html: `Data <b>${studentName}</b> akan dihapus.`,
        icon: "question",
        confirmButtonText: "Ya, hapus",
        showCancelButton: true,
        cancelButtonText: "Batal",
      }).then((response) => {
        if (response.isConfirmed) {
          currentForm.submit();
        }
      });
    });
  });

  const classFilter = document.querySelector("#kelasFilter");
  if (!classFilter) {
    return;
  }

  classFilter.addEventListener("change", (event) => {
    const currentUrl = new URL(window.location.href);
    const selectedValue = event.target.value;

    currentUrl.searchParams.set("page", "siswa");

    if (selectedValue !== "-1") {
      currentUrl.searchParams.set("kelas", selectedValue);
    } else {
      currentUrl.searchParams.delete("kelas");
    }

    window.location.href = currentUrl.toString();
  });
});
