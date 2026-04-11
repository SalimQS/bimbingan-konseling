function handlePreview(input) {
  const preview = document.querySelector("#previewFoto");
  const [file] = input.files;

  if (preview && file) {
    preview.src = URL.createObjectURL(file);
  }
}
