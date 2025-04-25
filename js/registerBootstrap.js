function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

function validatePhoneNumber(input) {
    console.log('validatePhoneNumber called');
    let value = input.value.replace(/[^0-9]/g, '');
    if (!value.startsWith('09')) {
        value = '09' + value.substring(2);
    }
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    input.value = value;
    input.setCustomValidity('');
    if (value.length === 11) {
        console.log('Checking phone duplicate for:', value);
        checkPhoneDuplicate(value);
    } else {
        const errorDiv = document.getElementById('contactNo-error');
        input.classList.remove('is-invalid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        console.log('Phone input less than 11 digits, clearing error');
    }
}

function checkPhoneDuplicate(value) {
    if (!value) {
        console.log('No phone value provided, skipping check');
        return;
    }
    console.log('Sending AJAX for cellphone_number:', value);
    $.ajax({
        url: '',
        method: 'POST',
        data: { field: 'cellphone_number', value: value },
        dataType: 'json',
        success: function(response) {
            console.log('Phone AJAX response:', response);
            const input = document.getElementById('contactNo');
            const errorDiv = document.getElementById('contactNo-error');
            console.log('Phone error div found:', errorDiv);
            if (response && response.exists) {
                console.log('Phone duplicate found, showing error:', response.message);
                input.classList.add('is-invalid');
                errorDiv.textContent = response.message || 'Cellphone number already exists';
                errorDiv.style.display = 'block';
            } else {
                console.log('No phone duplicate, clearing error');
                input.classList.remove('is-invalid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        },
        error: function(xhr, status, error) {
            console.error('Phone AJAX error:', status, error, xhr.responseText);
        }
    });
}

function checkEmailDuplicate(value) {
    if (!value) {
        console.log('No email value provided, skipping check');
        return;
    }
    console.log('Sending AJAX for email:', value);
    $.ajax({
        url: '',
        method: 'POST',
        data: { field: 'email', value: value },
        dataType: 'json',
        success: function(response) {
            console.log('Email AJAX response:', response);
            const input = document.getElementById('email');
            const errorDiv = document.getElementById('email-error');
            console.log('Email error div found:', errorDiv);
            if (response && response.exists) {
                console.log('Email duplicate found, showing error:', response.message);
                input.classList.add('is-invalid');
                errorDiv.textContent = response.message || 'Email already exists';
                errorDiv.style.display = 'block';
            } else {
                console.log('No email duplicate, clearing error');
                input.classList.remove('is-invalid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        },
        error: function(xhr, status, error) {
            console.error('Email AJAX error:', status, error, xhr.responseText);
        }
    });
}

function toggleGuidedByFields() {
    const relationship = document.getElementById('relationship').value;
    const assistedByField = document.getElementById('assisted-by-field');
    assistedByField.style.display = relationship ? 'block' : 'none';
}

function toggleFields() {
    const disabilityRadios = document.getElementsByName('disability');
    const needAssistanceRadios = document.getElementsByName('need_assistance');
    const bedriddenRadios = document.getElementsByName('bedridden');
    const birthDate = document.getElementById('birth-date').value;
    const assistanceSection = document.getElementById('assistance-section');
    const guidedByIdFields = document.getElementById('guided-by-id-fields');
    const oscaSection = document.getElementById('osca-section');
    const needAssistanceSection = document.getElementById('need-assistance-section');

    let disability = '';
    let needAssistance = '';
    let bedridden = '';
    let age = null;

    for (const radio of disabilityRadios) {
        if (radio.checked) {
            disability = radio.value;
            break;
        }
    }
    for (const radio of needAssistanceRadios) {
        if (radio.checked) {
            needAssistance = radio.value;
            break;
        }
    }
    for (const radio of bedriddenRadios) {
        if (radio.checked) {
            bedridden = radio.value;
            break;
        }
    }
    if (birthDate) {
        age = calculateAge(birthDate);
    }

    oscaSection.style.display = age !== null && age >= 60 ? 'block' : 'none';
    needAssistanceSection.style.display = (disability === 'With Disability' && age !== null && age > 12 && bedridden !== 'Yes') ? 'block' : 'none';

    let showAssistance = false;
    if (age !== null && age <= 12) {
        showAssistance = true;
    } else if (bedridden === 'Yes') {
        showAssistance = true;
    } else if (disability === 'With Disability' && needAssistance === 'Yes') {
        showAssistance = true;
    }

    assistanceSection.style.display = showAssistance ? 'block' : 'none';
    guidedByIdFields.style.display = showAssistance ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded');
    toggleFields();
    document.getElementById('birth-date').addEventListener('change', toggleFields);
    document.getElementById('relationship').addEventListener('change', toggleGuidedByFields);
    const disabilityRadios = document.getElementsByName('disability');
    const needAssistanceRadios = document.getElementsByName('need_assistance');
    const bedriddenRadios = document.getElementsByName('bedridden');
    for (const radio of disabilityRadios) {
        radio.addEventListener('change', toggleFields);
    }
    for (const radio of needAssistanceRadios) {
        radio.addEventListener('change', toggleFields);
    }
    for (const radio of bedriddenRadios) {
        radio.addEventListener('change', toggleFields);
    }

    document.getElementById('email').addEventListener('blur', function() {
        console.log('Email blur event triggered');
        checkEmailDuplicate(this.value);
    });
    document.getElementById('contactNo').addEventListener('input', function() {
        console.log('ContactNo input event triggered');
        validatePhoneNumber(this);
    });

    // Ensure dropdown labels move when a value is selected
    document.querySelectorAll('.form-select').forEach(select => {
        if (select.value !== "" && !select.value.includes("Select")) {
            const label = select.nextElementSibling;
            if (label && label.classList.contains('form-label')) {
                label.classList.add('active');
            }
        }
    });

    // Handle modal clear button click
    document.getElementById('confirmClearButton').addEventListener('click', function() {
        const form = document.getElementById('registrationForm');
        form.reset();
        document.getElementById('filePreview').innerHTML = '';
        document.getElementById('guidedByIdPreview').innerHTML = '';
        toggleFields();
        toggleGuidedByFields();
        const modal = bootstrap.Modal.getInstance(document.getElementById('clearFormModal'));
        modal.hide();
    });
});

document.getElementById('proofOfIdentification').addEventListener('change', function (e) {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    if (e.target.files[0]) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.style.maxWidth = '200px';
        preview.appendChild(img);
    }
});

document.getElementById('guidedById').addEventListener('change', function (e) {
    const preview = document.getElementById('guidedByIdPreview');
    preview.innerHTML = '';
    if (e.target.files[0]) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.style.maxWidth = '200px';
        preview.appendChild(img);
    }
});

function toggleOtherSuffixInput(select) {
    const otherSuffixContainer = document.getElementById('other-suffix-container');
    const otherSuffixInput = document.getElementById('other-suffix');
    if (select.value === 'Others') {
        otherSuffixContainer.style.display = 'block';
        otherSuffixInput.name = 'suffix';
    } else {
        otherSuffixContainer.style.display = 'none';
        otherSuffixInput.name = 'other_suffix';
    }
}
