var deleteForm = document.querySelectorAll('.formDelete');

deleteForm.forEach((form) => {
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var currForm = this;
    Swal.fire({
      title: "Anda yakin?",
      html: `List ini akan dihapus!`,
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
