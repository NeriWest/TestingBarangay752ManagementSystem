<?php
session_start();



// Check if the user is logged in by verifying the 'username' session variable
if (isset($_SESSION['username'])) {
    // Redirect to the admin dashboard if the user is already logged in
    header('Location: ../../controller/admin/adminDashboardController.php');
    exit(); // Ensure the script stops executing after redirection
} else {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay 752 | Official Website of Barangay 752</title>
    <link rel="stylesheet" href="../../css/loginRegistration.css">
    <link rel="stylesheet" href="../../css/homepage.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ephesis&family=Lexend:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ephesis&family=Lexend:wght@100..900&family=Metrophobic&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>


    <!-- Data Privacy Agreement Modal -->
    <div class="brgy752-modal-overlay" id="brgy752-privacy-modal">
        <div class="brgy752-modal">
            <div class="brgy752-modal-header">
                <h2>Data Privacy Agreement</h2>
                <button class="brgy752-modal-close" onclick="brgy752CloseModal()">&times;</button>
            </div>
            <div class="brgy752-modal-content">
                <p>
                    By proceeding with the registration, you acknowledge and agree that Barangay 752 may collect, store, and process your personal information, including but not limited to your name, contact details, date of birth, and proof of identification. This information will be used exclusively for government-related transactions, verification, and communication purposes.
                </p>
                <br>
                <p>
                    In compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>, Barangay 752 ensures that your personal data is handled securely and confidentially. We implement strict security measures to protect your data from unauthorized access, misuse, or disclosure. Your information will not be shared with third parties without your consent unless required by law.
                </p>
                <br>
                <p>
                    You have the right to access, update, or request the deletion of your personal data by submitting a written request to the barangay office. If you do not agree with our terms, please do not proceed with the registration.
                </p>
                <p>
                    By clicking "I Agree," you confirm that you have read and understood our Data Privacy Agreement.
                </p>
            </div>
            <div class="brgy752-modal-footer">
                <label>
                    <input type="checkbox" id="brgy752-privacy-checkbox">
                    I agree to the terms and conditions
                </label>
                <button class="brgy752-button" id="brgy752-agree-btn" onclick="brgy752AcceptAgreement()" disabled>Submit</button>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header">
        <div class="headerLogo">
            <img src="../../img/Barangay Logo.png" alt="Baranggay Logo">
        </div>
        <div class="barangayNumber">
            <h1>BARANGAY 752 ZONE-81</h1>
        </div>

        <div class="topNav">
            <div class="topNavAnchors">
                <a href="../../index.php">Home</a>
                <a href="AboutUs.php">About Us</a>
                <a href="Services.php">Services</a>
                <a class="loginAnchor" href="login.php">Login</a>
            </div>
        </div>
    </div>


    <div class="loginForm">
        <div class="loginLogo">
            <img src="../../img/Barangay Logo.png" alt="">
            <h1>REGISTRATION MODULE</h1>
        </div>
        <?php
        //Display error messages 
        if (isset($_SESSION['errorMessages'])) {
            echo '<h5 style="color:red; font-size="10px"; margin-top: -20px;">';
            foreach ($_SESSION['errorMessages'] as $errorMessage) {
                echo $errorMessage . '<br>';
            }
            echo '<br></h5>';
            unset($_SESSION['errorMessages']);
        }
        ?>
        <form class="loginLabel" action="../../controller/registerController.php" method="POST" enctype="multipart/form-data">
            <p style="margin-top: -50px;">Fill out the form for registration. All asterisk (*) is required.</p>
            <!-- SOON TO ADD INPUT VALIDATION -->
            <div class="fullNameInput" style="margin-top: 20px;">
                <div class="namesInput">
                    <label for="fName">First Name <p class="required" style="display: inline; color: red">*</p></label><br>
                    <input type="text" id="fName" name="first-name" minlength="2" maxlength="32"
                        pattern="[A-Za-z\s]+"
                        title="Please enter letters only (no numbers or special characters)"
                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                        required>
                </div>
                <div class="namesInput">
                    <label for="mName">Middle Name <br>
                        <input type="text" id="mName" name="middle-name" minlength="2" maxlength="32">
                </div>
                <div class="lastnameInput">
                    <label for="lName">Last Name <p class="required" style="display: inline; color: red">*</p> </label><br>
                    <input type="text" id="lName" name="last-name" minlength="2" maxlength="32" pattern="[A-Za-z\s]+"
                        title="Please enter letters only (no numbers or special characters)"
                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                        required>
                </div>
            </div>

            <div class="fullNameInput">
                <div class="namesInput">
                    <label for="suffix">Suffix</label><span class="grayOut">&nbsp&nbspex. Jr., Sr., I, III, etc.</span><br>
                    <select id="suffix" name="suffix" onchange="toggleOtherSuffixInput(this)">
                        <option value="" selected readonly>Select Suffix</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="Others">Others</option>
                    </select>
                    <script>
                        function toggleOtherSuffixInput(selectElement) {
                            const otherSuffixInput = document.getElementById('other-suffix');
                            const otherSuffixDiv = otherSuffixInput.parentElement;
                            if (selectElement.value === 'Others') {
                                otherSuffixDiv.style.display = 'inline-block';
                                otherSuffixInput.style.display = 'inline-block';
                                otherSuffixInput.focus();
                            } else {
                                otherSuffixDiv.style.display = 'none';
                                otherSuffixInput.style.display = 'none';
                                otherSuffixInput.value = ''; // Clear the input if not "Others"
                            }
                        }
                    </script>
                </div>
                <div class="namesInput" style="display: none;">
                    <input type="text" id="other-suffix" name="suffix" placeholder="Specify other suffix" style="display: none; margin-top: 10px;" minlength="1" maxlength="32">
                </div>
                <div class="namesInput">
                    <label for="date-of-birth">Birthdate<p class="required" style="display: inline; color: red"> *</p></label><br>
                    <input type="date" id="date-of-birth" name="date-of-birth" placeholder="Birthdate" min="<?= date('Y-m-d', strtotime('-120 year'));  ?>" max="<?= date('Y-m-d', strtotime('-16  year'));  ?>" required>
                </div>
            </div>

            <label for="username">Username:
                <span class="grayOut">(Must include letters, numbers, and special characters)</span>
                <!-- <span class="grayOut">@!abc123</span><br> -->
                <span class="required">*</span>
                <span>
                    <div class="validation-message" style="display: none; margin-bottom: 10px;"></div> <!-- Hidden by default -->
                </span>
            </label>
            <input type="text" id="username" name="username" minlength="3" maxlength="32" required>

            <label for="Contact No.">Contact No: <span class="grayOut">(ex. 09093729483)</span></label>
            <p class="required" style="display: inline; color: red"> *</p><br>
            <input type="tel" id="contactNo" name="cellphone_number" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required><br>

            <label for="Email">Email: <span class="grayOut">(Optional: ex. Juancruz@domain.com)</span></label><br>
            <input type="email" id="email" name="email" maxlength="32"
                oninput="this.setCustomValidity('')"
                pattern="[^\s@]+@[^\s@]+\.[^\s@]+"><br>

            <div class="input-with-icon">
                <label for="password">Password: <span class="grayOut">(Use one special character, numbers, and letters)</span>
                    <p class="required" style="display: inline; color: red">*</p>
                </label><br>
                <div class="password-container">
                    <input type="password" id="password" name="password" minlength="6" maxlength="32" required>
                    <span class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye-slash eye-icon" id="eye-password"></i>
                    </span>
                </div>

                <label for="confirmPassword">Confirm Password: <span class="grayOut">(Re-enter your password)</span>
                    <p class="required" style="display: inline; color: red">*</p>
                </label><br>
                <div class="password-container">
                    <input type="password" id="confirmPassword" name="confirm-password" minlength="6" maxlength="32" required>
                    <span class="toggle-password" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye-slash eye-icon" id="eye-confirmPassword"></i>
                    </span>
                </div>
            </div>

            <div class="centering" style="text-align: center;">
                <div class="proofOfIdentificationContainer">
                    <label class="proofOfIdentification" for="proofOfIdentification">
                        Proof of Identification <p class="required" style="display: inline; color: red">*</p>
                        <strong>
                            <br>"Upload a photo holding your valid ID with your <br>face clearly visible for verification."
                        </strong><br><br>
                        <img src="../../img/icons/attachIMG.png" alt="Upload Image" style="margin: auto;">
                    </label><br>
                    <!-- File input -->
                    <input id="proofOfIdentification" type="file" name="id_image" style="display: none;" accept=".jpg, .jpeg, .png">
                    <!-- Preview container -->
                    <div id="filePreview"></div>
                    <br><br>
                </div>
            </div>

            <div class="registerContainer">
                <p>Already have an account? <br><a href="login.php">LOGIN</a></p>
            </div>

            <div class="loginButton">
                <br>
                <button class="button" type="submit">REGISTER</button>
                <button type="reset" class="clearButton" onclick="confirmClearForm(event)">CLEAR ALL ENTRIES</button><br><br>

                <script>
                    function confirmClearForm(event) {
                        if (confirm("Are you sure you want to clear this form?")) {
                            localStorage.removeItem("registrationFormData");
                            location.reload();
                        } else {
                            event.preventDefault();
                        }
                    }
                </script>
            </div>

            <div>

            </div>
        </form>
    </div>

    <script>
        (function() {
            const formKey = "registrationFormData";

            function saveFormData() {
                const inputs = document.querySelectorAll('.loginForm input, .loginForm select');
                const formData = {};
                inputs.forEach(input => {
                    formData[input.id] = input.value;
                });
                localStorage.setItem(formKey, JSON.stringify(formData));
            }

            function loadFormData() {
                const savedData = JSON.parse(localStorage.getItem(formKey));
                if (savedData) {
                    const inputs = document.querySelectorAll('.loginForm input, .loginForm select');
                    inputs.forEach(input => {
                        if (savedData[input.id]) {
                            input.value = savedData[input.id];
                        }
                    });
                }
            }

            function clearFormData() {
                localStorage.removeItem(formKey);
            }

            document.addEventListener('DOMContentLoaded', () => {
                loadFormData();

                const inputs = document.querySelectorAll('.loginForm input, .loginForm select');
                inputs.forEach(input => {
                    input.addEventListener('input', saveFormData);
                });

                const form = document.querySelector('.loginForm form');
                if (form) {
                    form.addEventListener('submit', clearFormData);
                }
            });
        })();
    </script>




    <!-- FOOTER -->
    <div class="footer">
        <hr class="footerHR">
        <div class="footerContent">
            <!-- Address & Map -->
            <div class="first">

                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d444.4205693398933!2d120.99933729844516!3d14.5683702225061!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c99d33cee621%3A0xb5e2f10c0095d856!2s1194%20Florentino%20Torres%2C%20San%20Andres%20Bukid%2C%20Manila%2C%20Metro%20Manila!5e1!3m2!1sen!2sph!4v1740668301724!5m2!1sen!2sph"
                    width="350" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>

            <!-- Contact Section -->
            <div class="contact">
                <h3>Contact</h3>
                <p><i class="fas fa-phone phone-icon"></i> +63 912 345 6789</p>
                <p><a href="https://www.facebook.com/brgy752" target="_blank" class="facebook-link"><i
                            class="fab fa-facebook facebook-icon"></i> Barangay 752 Official</a></p>
            </div>

            <!-- More Section -->
            <div class="more">
                <h3>More</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                </ul>
            </div>

            <!-- Logos & Copyright -->
            <div class="footerLogo">
                <div class="logos">
                    <img src="../../img/lunsgodNgMayNilaLogo.png" alt="Seal of Lungsod ng Maynila">
                    <img src="../../img/Barangay Logo.png" alt="Barangay Logo">
                </div>
                <p class="footerParagraph">© 2024 | COPYRIGHT: BARANGAY 752</p>
            </div>
        </div>
    </div>
</body>
<!-- FOR TEST ONLY -->
<!-- <script>
    // Open Modal on Page Load
window.onload = function() {
    brgy752OpenModal();
};

// Open Modal
function brgy752OpenModal() {
    document.getElementById("brgy752-privacy-modal").style.display = "flex";
}

// Close Modal
function brgy752CloseModal() {
    document.getElementById("brgy752-privacy-modal").style.display = "none";
}

// Enable Submit Button when Checkbox is Checked
document.getElementById("brgy752-privacy-checkbox").addEventListener("change", function() {
    document.getElementById("brgy752-agree-btn").disabled = !this.checked;
});

// Handle Agreement
function brgy752AcceptAgreement() {
    brgy752CloseModal();
}

</script> -->

<!-- FINAL JS FOR MODAL -->
<script>
    // Check if the user has already agreed or registered
    window.onload = function() {
        if (!localStorage.getItem("brgy752-agreed")) {
            brgy752OpenModal();
        }
    };

    // Open Modal
    function brgy752OpenModal() {
        document.getElementById("brgy752-privacy-modal").style.display = "flex";
    }

    // Close Modal
    function brgy752CloseModal() {
        document.getElementById("brgy752-privacy-modal").style.display = "none";
    }

    // Enable Submit Button when Checkbox is Checked
    document.getElementById("brgy752-privacy-checkbox").addEventListener("change", function() {
        document.getElementById("brgy752-agree-btn").disabled = !this.checked;
    });

    // Handle Agreement
    function brgy752AcceptAgreement() {
        // Store the agreement in localStorage
        localStorage.setItem("brgy752-agreed", "true");
        brgy752CloseModal();
    }

    // Optionally, clear agreement after registration or when needed
    function brgy752ClearAgreement() {
        localStorage.removeItem("brgy752-agreed");
    }

    // Add to your validationMessages object
    const validationMessages = {
        // ... other fields ...
        'email': 'Please enter a valid email address'
    };

    // Add this check in your form submit handler
    const emailField = document.getElementById('email');
    if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
        emailField.setCustomValidity(validationMessages['email']);
        isValid = false;
    }

    function validateMiddleName(input) {
        if (input.value.length === 1) {
            input.setCustomValidity("Middle name must be at least 2 characters if provided");
        } else {
            input.setCustomValidity("");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.loginForm form');

        // Custom validation messages for each field
        const validationMessages = {
            'fName': 'Please enter your first name (at least 2 characters)',
            'lName': 'Please enter your last name (at least 2 characters)',
            'date-of-birth': 'Please enter your birthdate',
            'username': 'Please choose a username',
            'contactNo': 'Please enter a valid 11-digit phone number',
            'password': 'Please create a password',
            'confirmPassword': 'Please confirm your password',
            'proofOfIdentification': 'Please upload your ID photo'
        };

        // Set custom validation messages
        form.addEventListener('submit', function(event) {
            let isValid = true;

            Object.keys(validationMessages).forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // For file inputs
                    if (field.type === 'file' && field.files.length === 0) {
                        field.setCustomValidity(validationMessages[fieldId]);
                        isValid = false;
                    }
                    // For other inputs
                    else if (field.value.trim() === '' && field.required) {
                        field.setCustomValidity(validationMessages[fieldId]);
                        isValid = false;
                    }
                    // For minlength validation
                    else if (field.hasAttribute('minlength') &&
                        field.value.length < parseInt(field.getAttribute('minlength'))) {
                        field.setCustomValidity(validationMessages[fieldId]);
                        isValid = false;
                    } else {
                        field.setCustomValidity('');
                    }
                }
            });

            if (!isValid) {
                event.preventDefault();
                // Focus on first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.reportValidity();
                    firstInvalid.focus();
                }
            }
        });

        // Clear custom validation when user starts typing
        form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', function() {
                this.setCustomValidity('');
            });
        });
    });


    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(`eye-${fieldId}`);

        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    document.getElementById('proofOfIdentification').addEventListener('change', function(event) {
        const fileInput = event.target;
        const filePreview = document.getElementById('filePreview');
        const file = fileInput.files[0]; // Get the selected file

        // Clear the preview container
        filePreview.innerHTML = '';

        if (file) {
            // Check if the file is an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                // Load the image and display it
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Selected Image';
                    img.style.maxWidth = '600px';
                    img.style.maxHeight = '300px';
                    filePreview.appendChild(img);
                };

                reader.readAsDataURL(file);
            } else {
                // Display the file name if it's not an image
                const fileName = document.createElement('p');
                fileName.textContent = `Selected file: ${file.name}`;
                fileName.style.color = 'green';
                filePreview.appendChild(fileName);
            }
        }
    });

    const usernameInput = document.getElementById('username');
    const validationMessage = document.querySelector('.validation-message');
    let usernameCheckTimeout = null;

    // Track if the field has been interacted with
    let hasInteracted = false;

    usernameInput.addEventListener('focus', function() {
        hasInteracted = true;
    });

    // Real-time validation after first interaction
    usernameInput.addEventListener('input', function() {
        if (hasInteracted) {
            // Clear previous timeout if it exists
            clearTimeout(usernameCheckTimeout);

            // Basic validation first
            const validationResult = validateUsername();

            // Only check availability if basic validation passes
            if (validationResult.isValid) {
                // Debounce the AJAX call (wait 500ms after last keystroke)
                usernameCheckTimeout = setTimeout(() => {
                    checkUsernameAvailability(usernameInput.value.trim());
                }, 500);
            }
        }
    });

    // Validate on blur (when leaving the field)
    usernameInput.addEventListener('blur', function() {
        hasInteracted = true;
        validateUsername();
    });

    // Form submission validation
    usernameInput.form.addEventListener('submit', function(e) {
        hasInteracted = true;
        if (!validateUsername().isValid) {
            e.preventDefault();
        }
    });

    function validateUsername() {
        const value = usernameInput.value.trim();
        let message = '';
        let isValid = true;

        if (value.length < 3) {
            message = 'Username must be at least 3 characters';
            isValid = false;
        } else if (!/[a-zA-Z]/.test(value)) {
            message = 'Must contain at least one letter';
            isValid = false;
        } else if (!/\d/.test(value)) {
            message = 'Must contain at least one number';
            isValid = false;
        } else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value)) {
            message = 'Must contain at least one special character';
            isValid = false;
        }

        // Show/hide validation message
        if (message && hasInteracted) {
            validationMessage.style.display = 'block';
            validationMessage.textContent = message;
            validationMessage.style.color = 'red';
        } else if (!message) {
            validationMessage.style.display = 'none';
        }

        usernameInput.setCustomValidity(message);
        return {
            isValid,
            message
        };
    }

    function checkUsernameAvailability(username) {
        if (username.length < 3) return; // Don't check if too short

        fetch(`../../controller/check_username.php?username=${encodeURIComponent(username)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data.available) {
                    showValidationMessage(data.message, 'red');
                    usernameInput.setCustomValidity(data.message);
                } else {
                    showValidationMessage(data.message, 'green');
                    usernameInput.setCustomValidity('');
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
                showValidationMessage('Error checking username availability', 'red');
            });
    }

    function showValidationMessage(message, color) {
        validationMessage.style.display = 'block';
        validationMessage.textContent = message;
        validationMessage.style.color = color;
    }

    // Handle server-side validation messages
    <?php if (isset($_SESSION['usernameTaken']) && $_SESSION['usernameTaken']): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showValidationMessage('That username is already taken.', 'red');
            usernameInput.focus();
        });
    <?php
        unset($_SESSION['usernameTaken']);
    endif; ?>

    <?php if (isset($_SESSION['usernameTaken']) && $_SESSION['usernameTaken']) : ?>
            <
            script >
            window.addEventListener('DOMContentLoaded', function() {
                const usernameInput = document.getElementById('username');
                if (usernameInput) {
                    usernameInput.focus();
                    document.getElementById('usernameError').textContent = "That username is already taken.";
                }
            });
</script>
<?php unset($_SESSION['usernameTaken']); ?>
<?php endif; ?>
</script>


</html>