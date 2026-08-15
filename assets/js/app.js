function showAlert(message, type="success") {
    const box = document.getElementById("alertBox");
    if (!box) return;
    box.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">${message}<button class="btn-close" data-bs-dismiss="alert"></button></div>`;
}
