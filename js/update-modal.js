(function () {
    const modalKey = "modalState";
    const formKey = "modalFormData";

    const accessibleModal = {
        settings: {
            openButtons: document.querySelectorAll(".modal-open"),
            closeButton: document.querySelector(".modal-close"),
            modalOverlay: document.querySelector(".modal-overlay"),
            activeClass: "is-active",
            form: document.querySelector(".modal form"),
            inputs: document.querySelectorAll(".modal input, .modal select, .modal textarea"),
        },

        init: function () {
            if (!this.settings.modalOverlay) return;
            this.bindEvents();
            this.restoreState();
        },

        bindEvents: function () {
            const { openButtons, closeButton, modalOverlay, form, inputs } = this.settings;

            openButtons.forEach((button) => {
                button.addEventListener("click", () => {
                    this.openModal();
                });
            });

            if (closeButton) {
                closeButton.addEventListener("click", () => {
                    this.closeModal();
                });
            }

            if (modalOverlay) {
                modalOverlay.addEventListener("click", (e) => {
                    if (e.target === modalOverlay) {
                        this.closeModal();
                    }
                });
            }

            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && this.isModalOpen()) {
                    this.closeModal();
                }
            });

            // Save input data on change
            inputs.forEach((input) => {
                input.addEventListener("input", () => {
                    this.saveFormData();
                });
            });

            // Close modal on form submission
            if (form) {
                form.addEventListener("submit", (e) => {
                    this.closeModal();
                });
            }
        },

        openModal: function () {
            this.settings.modalOverlay.classList.add(this.settings.activeClass);
            document.body.style.overflow = "hidden";
            this.saveModalState(true);
        },

        closeModal: function () {
            this.settings.modalOverlay.classList.remove(this.settings.activeClass);
            document.body.style.overflow = "";
            this.saveModalState(false);
            localStorage.removeItem(formKey); // Clear saved form data
        },

        isModalOpen: function () {
            return this.settings.modalOverlay.classList.contains(this.settings.activeClass);
        },

        saveModalState: function (isOpen) {
            localStorage.setItem(modalKey, isOpen ? "open" : "closed");
        },

        saveFormData: function () {
            const formData = {};
            let hasData = false;

            this.settings.inputs.forEach((input) => {
                if (input.value.trim() !== "") {
                    hasData = true;
                }
                formData[input.id] = input.value;
            });

            localStorage.setItem(formKey, JSON.stringify(formData));

            if (!hasData) {
                this.saveModalState(false);
            }
        },

        restoreState: function () {
            const savedData = JSON.parse(localStorage.getItem(formKey));
            const hasData = savedData && Object.values(savedData).some(value => value.trim() !== "");

            if (hasData) {
                this.openModal();
            } else {
                this.saveModalState(false);
            }

            if (savedData) {
                this.settings.inputs.forEach((input) => {
                    if (savedData[input.id] !== undefined) {
                        input.value = savedData[input.id];
                    }
                });
            }
        },
    };

    document.addEventListener("DOMContentLoaded", () => accessibleModal.init());
})();
