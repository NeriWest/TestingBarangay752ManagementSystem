document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("fullImage");
    const closeModal = document.getElementsByClassName("close")[0];

    document.querySelectorAll(".template-img").forEach(img => {
        img.onclick = function () {
            modal.style.display = "block";
            modalImg.src = this.src;
        };
    });

    closeModal.onclick = function () {
        modal.style.display = "none";
    };

    modal.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});