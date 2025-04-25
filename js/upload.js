document.querySelectorAll('.uploadTemplateForm').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let formData = new FormData(this);
        let templateId = this.getAttribute('data-template-name');
        let tableName = this.getAttribute('data-template-type');
        
        let imgElement = document.querySelector(`#template-${tableName}-${templateId}-img`);
        let fileInput = this.querySelector("input[type='file']");

        if (!fileInput || !fileInput.files.length) {
            alert("Please select a file before uploading.");
            return;
        }

        fetch("/Barangay752managementsystem/controller/admin/uploadController.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Upload successful!");
                if (imgElement) {
                    let newSrc = `${data.imageUrl}?t=${new Date().getTime()}`;
                    let newImg = new Image();
                    newImg.src = newSrc;
                    newImg.alt = "Uploaded Image";
                    newImg.className = imgElement.className;
                    newImg.onload = function () {
                        imgElement.replaceWith(newImg);
                    };
                    newImg.onerror = function () {
                        alert("Image failed to load, but upload succeeded.");
                    };
                }
            } else {
                alert("Upload failed: " + data.message);
            }
        })
        .catch(error => {
            alert("Upload failed: " + error.message);
        });
    });
});

document.querySelectorAll('.uploadDocumentForm').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let formData = new FormData(this);
        let templateId = this.querySelector("input[name='templateId']").value;
        let documentContainer = document.querySelector(`#document-${templateId}`);
        let fileInput = this.querySelector("input[type='file']");

        fetch("/Barangay752managementsystem/controller/admin/uploadController.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text()) // First, get raw response
        .then(text => {
            console.log("Server Response:", text); // 🔥 Debug: Show raw response
            try {
                return JSON.parse(text); // Parse JSON if it's valid
            } catch (error) {
                throw new Error("Invalid JSON response: " + text); // Handle unexpected HTML
            }
        })
        .then(data => {
            if (data.success) {
                alert("Document uploaded and modified successfully!");

                // Update document link in UI
                let docNameElem = documentContainer.querySelector(".document-name");
                let viewBtn = documentContainer.querySelector(".view-document-btn");
                let downloadBtn = documentContainer.querySelector(".download-document-btn");

                if (docNameElem) {
                    docNameElem.textContent = `Modified Template - ID: ${templateId}`;
                }

                if (viewBtn && downloadBtn) {
                    viewBtn.href = data.documentUrl;
                    downloadBtn.href = data.documentUrl;
                }

                fileInput.value = ""; // Clear file input
            } else {
                alert("Document upload failed: " + data.message);
            }
        })
        .catch(error => {
            console.error("Upload Error: ", error);
            alert("Upload failed. Check console for details.");
        });
    });
});

// **🔥 Keep checking until the document updates in real-time**
function waitForDocumentUpdate(templateId, container) {
    let attempts = 0;
    let maxAttempts = 10;
    let interval = 500;

    function checkForUpdate() {
        fetch(`/Barangay752managementsystem/controller/admin/documentController.php?templateId=${templateId}&nocache=${new Date().getTime()}`)
        .then(response => response.json())
        .then(updatedData => {
            if (updatedData.success && updatedData.template.document) {
                let updatedUrl = `${updatedData.template.document}?refresh=${new Date().getTime()}`;
                updateDocumentContainer(container, updatedUrl);
            } else if (attempts < maxAttempts) {
                attempts++;
                setTimeout(checkForUpdate, interval);
                interval *= 1.5; // 🔥 Gradually increase time between retries
            } else {
                console.error("Failed to verify document update.");
            }
        })
        .catch(error => console.error("Error fetching updated document: ", error));
    }

    checkForUpdate();
}

// **🔥 Instantly Update Document Buttons**
function updateDocumentContainer(container, documentUrl) {
    container.innerHTML = ''; // Clear previous content

    let viewButton = document.createElement('a');
    viewButton.href = documentUrl;
    viewButton.target = '_blank';
    viewButton.textContent = 'View';
    viewButton.classList.add('view-document-btn');

    let downloadButton = document.createElement('a');
    downloadButton.href = documentUrl;
    downloadButton.download = '';
    downloadButton.textContent = 'Download';
    downloadButton.classList.add('download-document-btn');

    container.appendChild(viewButton);
    container.appendChild(downloadButton);
}

// **🔥 Auto-update documents when the page loads**
document.querySelectorAll('.template-container').forEach(container => {
    let templateId = container.getAttribute('data-template-id');
    let documentContainer = container.querySelector(`#document-${templateId}`);

    waitForDocumentUpdate(templateId, documentContainer);
});
