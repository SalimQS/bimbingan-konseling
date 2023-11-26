window.onload = () => {
    if(window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
};

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
      if(response.isConfirmed) {
        currForm.submit();
      }
    });
  });
});
