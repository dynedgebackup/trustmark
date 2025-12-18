document.addEventListener("DOMContentLoaded", function () {
    const companyFields = ['company_name', 'registration_no'];
    const individualFields = ['owner_name', 'owner_nric'];

    function toggleFields() {
        const selectedType = document.querySelector('input[name="acc_type"]:checked')?.value;

        if (selectedType === "Company") {
            companyFields.forEach(id => document.getElementById(id).closest('.mb-3').style.display = 'block');
            individualFields.forEach(id => document.getElementById(id).closest('.mb-3').style.display = 'none');
        } else if (selectedType === "Individual") {
            companyFields.forEach(id => document.getElementById(id).closest('.mb-3').style.display = 'none');
            individualFields.forEach(id => document.getElementById(id).closest('.mb-3').style.display = 'block');
        }
    }

    document.querySelectorAll('input[name="acc_type"]').forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });

    toggleFields(); // Call once on page load
});
