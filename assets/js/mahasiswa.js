var changeStatusForm = document.querySelectorAll(".formChangeStatus");

changeStatusForm.forEach((form) => {
  form.addEventListener("submit", function (e) {
    e.preventDefault()
    var currForm = this;
    var nama = this.getAttribute("nama-siswa");
    var status = this.getAttribute("status");
    Swal.fire({
      title: "Anda yakin?",
      html: `Siswa <b>${nama}</b> akan ${status == '1' ? 'dinonaktifkan' : 'diaktifkan'}`,
      icon: "question",
      confirmButtonText: "Ya!",
      showCancelButton: true,
      cancelButtonText: "Tidak!",
    }).then((response) => {
      if (response.isConfirmed) {
        currForm.submit();
      }
    });
})
});

var deleteForm = document.querySelectorAll('.formDelete');

deleteForm.forEach((form) => {
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var currForm = this;
    var nama = this.getAttribute("nama-siswa");
    Swal.fire({
      title: "Anda yakin?",
      html: `Siswa <b>${nama}</b> akan dihapus!`,
      icon: "question",
      confirmButtonText: "Ya!",
      showCancelButton: true,
      cancelButtonText: "Tidak!",
    }).then((response) => {
      if (response.isConfirmed) {
        currForm.submit();
      }
    });
  });
});
