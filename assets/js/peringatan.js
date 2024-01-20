
window.onload = () => {
    var kelasFilter = document.querySelector('#kelasFilter');
  
    kelasFilter.addEventListener("change", (val) => {
      if(val.target.value != '-1') {
        window.location.href = "index.php?page=peringatan&kelas=" + val.target.value;
      }
      else {
        window.location.href = "index.php?page=peringatan";
      }
    });
  }
  