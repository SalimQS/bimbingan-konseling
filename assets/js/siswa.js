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
      if(response.isConfirmed) {
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
      if(response.isConfirmed) {
        currForm.submit();
      }
    });
  });
});

/*var dataTable = document.querySelectorAll('.dataTable tr:not(thead tr)');

dataTable.forEach((row) => {
  row.addEventListener("click", function (e) {
    var id = this.getAttribute("id-data");
    //document.getElementById("id-selector").value = id;
    document.querySelectorAll('.dataTable tr:not(thead tr)').forEach((doc) => {
      doc.style.backgroundColor = '#fff';
    })
    alert(e.target.parentElement.parentElement.parentElement.querySelector('span').style.innerHTML)
    //e.target.parentElement.style.backgroundColor = 'rgba(37, 52, 163, 0.219)';
  });
});*/

var kelasFilter = document.querySelector('#kelasFilter');

kelasFilter.addEventListener("change", (val) => {
  if(val.target.value != '-1') {
    window.location.href = "index.php?page=siswa&kelas=" + val.target.value;
  }
  else {
    window.location.href = "index.php?page=siswa";
  }
});