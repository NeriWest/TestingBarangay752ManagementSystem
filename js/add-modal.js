(function () {
    const modalStates = {
        add: {
            modalKey: "modalAddState",
            formKey: "modalAddFormData",
            activeClass: "is-active-add",
            openButtonsClass: ".modal-open-add",
            modalOverlayClass: ".modal-overlay-add",
            closeButtonClass: ".modal-close-add",
            formClass: ".modal-add form",
            inputsClass: ".modal-add input, .modal-add select, .modal-add textarea"
        },
        view: {
            modalKey: "modalViewState",
            formKey: "modalViewFormData",
            activeClass: "is-active-view",
            openButtonsClass: ".modal-open-view",
            modalOverlayClass: ".modal-overlay-view",
            closeButtonClass: ".modal-close-view",
            formClass: ".modal-view form",
            inputsClass: ".modal-view input, .modal-view select, .modal-view textarea"
        }
    };

    function initializeModal(modalConfig) {
        const settings = {
            openButtons: document.querySelectorAll(modalConfig.openButtonsClass),
            closeButton: document.querySelector(modalConfig.closeButtonClass),
            modalOverlay: document.querySelector(modalConfig.modalOverlayClass),
            activeClass: modalConfig.activeClass,
            form: document.querySelector(modalConfig.formClass),
            inputs: document.querySelectorAll(modalConfig.inputsClass),
        };

        const modalStateManager = {
            init: function () {
                if (!settings.modalOverlay) return;
                this.bindEvents();
                this.restoreState();
            },

            bindEvents: function () {
                const { openButtons, closeButton, modalOverlay, form, inputs } = settings;

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

                inputs.forEach((input) => {
                    input.addEventListener("input", () => {
                        this.saveFormData();
                    });
                });

                if (form) {
                    form.addEventListener("submit", (e) => {
                        this.closeModal();
                    });
                }
            },

            openModal: function () {
                settings.modalOverlay.classList.add(settings.activeClass);
                document.body.style.overflow = "hidden";
                this.saveModalState(true);
            },

            closeModal: function () {
                settings.modalOverlay.classList.remove(settings.activeClass);
                document.body.style.overflow = "";
                this.saveModalState(false);
                localStorage.removeItem(modalConfig.formKey); // Clear saved form data
            },

            isModalOpen: function () {
                return settings.modalOverlay.classList.contains(settings.activeClass);
            },

            saveModalState: function (isOpen) {
                localStorage.setItem(modalConfig.modalKey, isOpen ? "open" : "closed");
            },

            saveFormData: function () {
                const formData = {};
                let hasData = false;

                settings.inputs.forEach((input) => {
                    if (input.value.trim() !== "") {
                        hasData = true;
                    }
                    formData[input.id] = input.value;
                });

                localStorage.setItem(modalConfig.formKey, JSON.stringify(formData));

                if (!hasData) {
                    this.saveModalState(false);
                }
            },

            restoreState: function () {
                const savedData = JSON.parse(localStorage.getItem(modalConfig.formKey));
                const hasData = savedData && Object.values(savedData).some(value => value.trim() !== "");

                if (hasData) {
                    this.openModal();
                } else {
                    this.saveModalState(false);
                }

                if (savedData) {
                    settings.inputs.forEach((input) => {
                        if (savedData[input.id] !== undefined) {
                            input.value = savedData[input.id];
                        }
                    });
                }
            },
        };

        modalStateManager.init();
    }

    document.addEventListener("DOMContentLoaded", () => {
        initializeModal(modalStates.add);
        initializeModal(modalStates.view);
    });
})();
