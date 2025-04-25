    function showSection(templateId) {
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });

        const activeSection = document.getElementById(templateId);
        if (activeSection) {
            activeSection.style.display = 'block';
            activeSection.classList.add('active');

            // If the Permissions section is selected, show the first permission div
            if (templateId === 'permissions') {
                const firstPermission = document.querySelector('.permission');
                if (firstPermission) {
                    showPermission(firstPermission.id);
                }
            }
        } else {
            console.error(`Section '${templateId}' not found`);
        }
    }

    function showPermission(role) {
        const permissions = document.querySelectorAll('.permission');
        permissions.forEach(permission => {
            permission.style.display = 'none';
            permission.classList.remove('active');
        });

        const activePermission = document.getElementById(role);
        if (activePermission) {
            activePermission.style.display = 'block';
            activePermission.classList.add('active');

            // Update settings-nav button active state
            const navButtons = document.querySelectorAll('#permissions .settings-nav button');
            navButtons.forEach(button => {
                button.classList.remove('active');
                if (button.querySelector('p').textContent === role) {
                    button.classList.add('active');
                }
            });
        } else {
            console.error(`Permission div for role '${role}' not found`);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const firstSection = document.querySelector('.section');
        if (firstSection) {
            showSection(firstSection.id);
        }

        // Add click handlers for settings-nav buttons in Permissions
        const permissionNavButtons = document.querySelectorAll('#permissions .settings-nav button');
        permissionNavButtons.forEach(button => {
            button.addEventListener('click', () => {
                const role = button.querySelector('p').textContent;
                showPermission(role);
            });
        });

        // Handle edit button clicks to populate the modal
        document.querySelectorAll(".edit-template-btn").forEach(button => {
            button.addEventListener("click", function () {
                const templateId = this.getAttribute("data-template-id");
                const templateName = this.getAttribute("data-template-name");
                const priceEnabled = this.getAttribute("data-price-enabled") === "1";
                const price = this.getAttribute("data-price");

                // Populate the edit modal
                document.getElementById("edit-template-id").value = templateId;
                document.getElementById("edit-template-name").value = templateName;
                document.getElementById("edit-price-enabled").checked = priceEnabled;
                const priceInput = document.getElementById("edit-price");
                const priceInputContainer = document.getElementById("edit-price-input");
                
                if (priceInput) {
                    priceInput.value = price ? parseFloat(price).toFixed(2) : "0.00";
                } else {
                    console.error("Price input element with id 'edit-price' not found");
                }
                
                if (priceInputContainer) {
                    if (priceEnabled) {
                        priceInputContainer.style.display = "block";
                        priceInputContainer.removeAttribute("hidden");
                    } else {
                        priceInputContainer.style.display = "none";
                        priceInputContainer.setAttribute("hidden", "hidden");
                    }
                    console.log(`Price input container display set to: ${priceInputContainer.style.display}, hidden: ${priceInputContainer.hasAttribute("hidden")} (priceEnabled: ${priceEnabled})`);
                } else {
                    console.error("Price input container with id 'edit-price-input' not found");
                }

                // Show the modal
                document.getElementById("editModal").style.display = "block";
            });
        });

        // Handle checkbox toggle for real-time price_enabled and price update
        const priceEnabledCheckbox = document.getElementById("edit-price-enabled");
        if (priceEnabledCheckbox) {
            priceEnabledCheckbox.addEventListener("change", async function () {
                const templateId = document.getElementById("edit-template-id").value;
                const priceEnabled = this.checked ? 1 : 0;
                const price = priceEnabled ? document.getElementById("edit-price").value : 0;
                const priceInput = document.getElementById("edit-price");
                const priceInputContainer = document.getElementById("edit-price-input");

                // Immediately toggle visibility for real-time UI feedback
                if (priceInputContainer) {
                    if (priceEnabled) {
                        priceInputContainer.style.display = "block";
                        priceInputContainer.removeAttribute("hidden");
                    } else {
                        priceInputContainer.style.display = "none";
                        priceInputContainer.setAttribute("hidden", "hidden");
                    }
                    console.log(`Price input container display set to: ${priceInputContainer.style.display}, hidden: ${priceInputContainer.hasAttribute("hidden")} (priceEnabled: ${priceEnabled})`);
                } else {
                    console.error("Price input container with id 'edit-price-input' not found during checkbox toggle");
                }

                // Update price input immediately if unchecked
                if (!priceEnabled && priceInput) {
                    priceInput.value = "0.00";
                }

                // Disable checkbox to prevent multiple clicks
                this.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append("templateId", templateId);
                    formData.append("priceEnabled", priceEnabled);
                    formData.append("price", price);

                    const response = await fetch('../../controller/admin/editTemplate.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        console.log(`price_enabled updated to ${priceEnabled}, price updated to ${price} for template ${templateId}`);
                        // Update data attributes on the edit button
                        const editButton = document.querySelector(`.edit-template-btn[data-template-id="${templateId}"]`);
                        if (editButton) {
                            editButton.setAttribute("data-price-enabled", priceEnabled);
                            editButton.setAttribute("data-price", price);
                        }
                        // Update template table price column in real-time
                        const row = document.querySelector(`tr[data-template-id="${templateId}"]`);
                        if (row) {
                            const priceCell = row.querySelector('.template-price');
                            if (priceCell) {
                                priceCell.textContent = priceEnabled ? `$${parseFloat(price).toFixed(2)}` : 'N/A';
                                console.log(`Updated template-price to: ${priceCell.textContent} for template ${templateId}`);
                            } else {
                                console.error(`Price cell with class 'template-price' not found for template ${templateId}`);
                            }
                        } else {
                            console.error(`Table row with data-template-id="${templateId}" not found`);
                        }
                    } else {
                        alert("Failed to update price enabled status: " + (result.message || "Unknown error"));
                        // Revert checkbox state and UI on failure
                        this.checked = !this.checked;
                        if (priceInputContainer) {
                            if (this.checked) {
                                priceInputContainer.style.display = "block";
                                priceInputContainer.removeAttribute("hidden");
                            } else {
                                priceInputContainer.style.display = "none";
                                priceInputContainer.setAttribute("hidden", "hidden");
                            }
                        }
                        if (this.checked && priceInput) {
                            priceInput.value = price ? parseFloat(price).toFixed(2) : "0.00";
                        }
                    }
                } catch (error) {
                    alert("An error occurred: " + error.message);
                    // Revert checkbox state and UI on failure
                    this.checked = !this.checked;
                    if (priceInputContainer) {
                        if (this.checked) {
                            priceInputContainer.style.display = "block";
                            priceInputContainer.removeAttribute("hidden");
                        } else {
                            priceInputContainer.style.display = "none";
                            priceInputContainer.setAttribute("hidden", "hidden");
                        }
                    }
                    if (this.checked && priceInput) {
                        priceInput.value = price ? parseFloat(price).toFixed(2) : "0.00";
                    }
                } finally {
                    // Re-enable checkbox
                    this.disabled = false;
                }
            });
        }

        // Handle form submission for template updates
        const editForm = document.getElementById("editTemplateForm");
        if (editForm) {
            editForm.addEventListener("submit", async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.set("price_enabled", document.getElementById("edit-price-enabled").checked ? "1" : "0");

                try {
                    const response = await fetch('../../controller/admin/editTemplate.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert("Template updated successfully!");
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else {
                        alert("Failed to update template: " + (result.message || "Unknown error"));
                    }
                } catch (error) {
                    alert("An error occurred: " + error.message);
                }
            });
        }

        // Handle template form submission
        document.querySelectorAll('.template-form').forEach(form => {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(form);

                try {
                    const response = await fetch('/Barangay752ManagementSystem/controller/admin/documentController.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert("Template updated successfully!");
                        window.location.reload();
                    } else {
                        alert("Failed to update template: " + (result.message || "Unknown error"));
                    }
                } catch (error) {
                    alert("An error occurred: " + error.message);
                }
            });
        });

        // Close modal
        document.getElementById("cancel-edit-btn").addEventListener("click", function () {
            document.getElementById("editModal").style.display = "none";
        });

        // Close modal when clicking outside
        window.addEventListener("click", function (event) {
            if (event.target === document.getElementById("editModal")) {
                document.getElementById("editModal").style.display = "none";
            }
        });

        // Permission checkbox handling
        const checkboxes = document.querySelectorAll(".permission-checkbox");
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                const roleName = this.getAttribute("data-role");
                const permissionName = this.getAttribute("data-permission");
                const is_enabled = this.checked ? 1 : 0;

                fetch("/Barangay752ManagementSystem/config/rolePermissions.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `role=${encodeURIComponent(roleName)}&permission=${encodeURIComponent(permissionName)}&is_enabled=${is_enabled}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("Error: " + (data.error || "Unknown error"));
                        this.checked = !this.checked; // Revert on failure
                    }
                })
                .catch(err => {
                    console.error("Error updating permission:", err);
                    alert("An error occurred while updating the permission.");
                    this.checked = !this.checked; // Revert on failure
                });
            });
        });

        const forms = document.querySelectorAll('.payment-form');
        forms.forEach(function (form) {
            const paymentId = form.querySelector('input[name="paymentId"]').value;
            const fileInput = form.querySelector('input[name="payment_image"]');
            const imgElement = form.closest('.payment').querySelector('img');
            const statusMessage = document.getElementById('status-message-' + paymentId);

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imgElement.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(form);

                fetch('/Barangay752ManagementSystem/controller/admin/qrController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusMessage.innerHTML = `<p style="color: green;">${data.message}</p>`;
                        imgElement.src = data.new_qr_photo;
                    } else {
                        statusMessage.innerHTML = `<p style="color: red;">${data.message}</p>`;
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    statusMessage.innerHTML = `<p style="color: red;">An error occurred. Please try again later.</p>`;
                });
            });
        });

        // Add Payment Type Modal
        const addPaymentModal = document.getElementById('add-payment-modal');
        const addPaymentBtn = document.getElementById('add-payment-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const addPaymentForm = document.getElementById('add-payment-form');
        const paymentNameInput = document.getElementById('payment-name');

        if (addPaymentBtn) {
            addPaymentBtn.addEventListener('click', function () {
                addPaymentModal.style.display = 'block';
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function () {
                addPaymentModal.style.display = 'none';
            });
        }

        if (addPaymentForm) {
            addPaymentForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const paymentName = paymentNameInput.value.trim();

                if (paymentName) {
                    fetch('/Barangay752ManagementSystem/controller/admin/qrController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ action: 'add', name: paymentName })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                            addPaymentModal.style.display = 'none';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error adding payment type:", error);
                        alert("An error occurred while adding the payment type.");
                    });
                }
            });
        }

        // Delete Payment Type
        document.querySelectorAll('.delete-payment-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const paymentIdToDelete = this.dataset.paymentId;
                if (confirm("Are you sure you want to delete this payment type?")) {
                    fetch('/Barangay752ManagementSystem/controller/admin/qrController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ action: 'delete', paymentId: paymentIdToDelete })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error deleting payment type:", error);
                        alert("An error occurred while deleting the payment type.");
                    });
                }
            });
        });

        // Add Template Modal
        const modal = document.getElementById("addTemplateModal");
        const modalForm = document.getElementById("addTemplateForm");

        if (modal && modalForm) {
            document.querySelectorAll(".add-template-btn").forEach(button => {
                button.addEventListener("click", function () {
                    document.getElementById("modalTemplateType").value = this.dataset.templateType;
                    modal.style.display = "block";
                });
            });

            document.querySelector(".close").addEventListener("click", function () {
                modal.style.display = "none";
            });

            modalForm.addEventListener("submit", function (event) {
                event.preventDefault();
                const formData = new FormData(modalForm);

                fetch("/Barangay752managementsystem/controller/admin/addTemplate.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (error) {
                        console.error("Invalid JSON response:", text);
                        throw new Error("Server did not return valid JSON.");
                    }
                })
                .then(data => {
                    if (data.success) {
                        alert("Template added successfully!");
                        window.location.reload();
                    } else {
                        alert("Failed to add template: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while adding the template.");
                });
            });
        }

        // Delete Template
        document.querySelectorAll(".delete-template-btn").forEach(button => {
            button.addEventListener("click", function () {
                const templateId = this.getAttribute("data-template-id");
                const templateDiv = document.getElementById(`template-${templateId}`);

                if (!templateId || !templateDiv) {
                    console.error("Error: Could not find the template element.");
                    return;
                }

                if (confirm("Are you sure you want to delete this template?")) {
                    console.log("Sending delete request for template ID:", templateId);

                    fetch("/Barangay752ManagementSystem/controller/admin/deleteTemplate.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ templateId: templateId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Server response:", data);

                        if (data.success) {
                            alert("Template deleted successfully.");
                            templateDiv.remove();   
                        } else {
                            alert("Failed to delete template: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Request failed:", error);
                        alert("Error: Could not communicate with server.");
                    });
                }
            });
        });
    });