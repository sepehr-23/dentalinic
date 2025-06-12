document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('patientRegistrationForm');
    const patientDisplayArea = document.getElementById('patientDisplayArea');

    // --- Function to load and display patients ---
    async function loadPatients() {
        try {
            const response = await fetch('php/get_patients.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            patientDisplayArea.innerHTML = ''; // Clear existing patient cards

            if (data.status === 'success' && data.patients) {
                data.patients.forEach(patient => {
                    const patientCard = document.createElement('div');
                    patientCard.classList.add('patient-card');
                    patientCard.dataset.patientId = patient.id; // Store patient ID

                    // Patient details
                    const nameElement = document.createElement('h3');
                    nameElement.textContent = patient.full_name;
                    patientCard.appendChild(nameElement);

                    const phoneElement = document.createElement('p');
                    phoneElement.textContent = `شماره تماس: ${patient.phone_number || 'ثبت نشده'}`;
                    patientCard.appendChild(phoneElement);

                    const ageElement = document.createElement('p');
                    ageElement.textContent = `سن: ${patient.age || 'ثبت نشده'}`;
                    patientCard.appendChild(ageElement);

                    const genderElement = document.createElement('p');
                    genderElement.textContent = `جنسیت: ${patient.gender || 'ثبت نشده'}`;
                    patientCard.appendChild(genderElement);

                    // Payment Status
                    const paymentStatusDiv = document.createElement('div');
                    paymentStatusDiv.classList.add('payment-status');
                    let paymentStatusText = 'وضعیت نامشخص';
                    let paymentStatusClass = 'unpaid'; // Default

                    // Assuming 'payment_status' from DB contains Farsi values
                    // Note: The database should ideally store these as ENUMs or specific codes,
                    // and the Farsi text should be for display purposes.
                    // For now, we map based on the Farsi text directly as per requirements.
                    if (patient.payment_status === 'تصفیه شده') {
                        paymentStatusText = 'تصفیه شده';
                        paymentStatusClass = 'paid';
                    } else if (patient.payment_status === 'در حال تسویه') {
                        paymentStatusText = 'در حال تسویه';
                        paymentStatusClass = 'pending';
                    } else if (patient.payment_status === 'تصفیه نشده') {
                        paymentStatusText = 'تصفیه نشده';
                        paymentStatusClass = 'unpaid';
                    } else if (patient.payment_status) { // If status exists but not matched
                        paymentStatusText = patient.payment_status; // Display as is
                         paymentStatusClass = 'unpaid'; // Default to unpaid style
                    }


                    paymentStatusDiv.textContent = paymentStatusText;
                    paymentStatusDiv.classList.add(paymentStatusClass);
                    patientCard.appendChild(paymentStatusDiv);

                    // "Mark as Paid" Button
                    const markAsPaidButton = document.createElement('button');
                    markAsPaidButton.textContent = 'علامت گذاری به عنوان پرداخت شده';
                    markAsPaidButton.classList.add('pay-button');
                    if (paymentStatusClass === 'paid') markAsPaidButton.disabled = true;
                    markAsPaidButton.addEventListener('click', function() {
                        // Here you would ideally also update the status on the server
                        // For now, client-side only:
                        paymentStatusDiv.textContent = 'پرداخت شده';
                        paymentStatusDiv.classList.remove('unpaid', 'pending');
                        paymentStatusDiv.classList.add('paid');
                        markAsPaidButton.disabled = true;
                        markAsPendingButton.disabled = false; // Re-enable pending button if needed
                         // TODO: Add fetch call to an update_payment_status.php script
                        console.log(`Patient ID ${patient.id} marked as paid (client-side).`);
                    });
                    patientCard.appendChild(markAsPaidButton);

                    // "Mark as Pending" Button
                    const markAsPendingButton = document.createElement('button');
                    markAsPendingButton.textContent = 'علامت گذاری به عنوان در انتظار پرداخت';
                    markAsPendingButton.classList.add('pending-button');
                     if (paymentStatusClass === 'pending') markAsPendingButton.disabled = true;
                    markAsPendingButton.addEventListener('click', function() {
                        paymentStatusDiv.textContent = 'در انتظار پرداخت';
                        paymentStatusDiv.classList.remove('unpaid', 'paid');
                        paymentStatusDiv.classList.add('pending');
                        markAsPendingButton.disabled = true;
                        markAsPaidButton.disabled = false;
                         // TODO: Add fetch call to an update_payment_status.php script
                        console.log(`Patient ID ${patient.id} marked as pending (client-side).`);
                    });
                    patientCard.appendChild(markAsPendingButton);

                    // Disable buttons if status is already paid
                    if (paymentStatusClass === 'paid') {
                        markAsPaidButton.disabled = true;
                        markAsPendingButton.disabled = true;
                    }


                    patientDisplayArea.appendChild(patientCard);
                });
            } else if (data.status === 'error') {
                console.error('Error loading patients:', data.message);
                patientDisplayArea.innerHTML = `<p style="color:red;">خطا در بارگذاری لیست بیماران: ${data.message}</p>`;
            }
        } catch (error) {
            console.error('Fetch error for get_patients.php:', error);
            patientDisplayArea.innerHTML = `<p style="color:red;">مشکلی در ارتباط با سرور برای دریافت لیست بیماران پیش آمد.</p>`;
        }
    }

    // --- Form Submission Handler ---
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);

        // Clear previous messages
        const existingMessage = form.querySelector('.form-message');
        if (existingMessage) existingMessage.remove();

        fetch('php/add_patient.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('form-message'); // For styling

            if (data.status === 'success') {
                messageDiv.textContent = data.message || 'بیمار با موفقیت اضافه شد.';
                messageDiv.style.color = 'green';
                form.reset(); // Reset the form fields
                loadPatients(); // Refresh the patient list
            } else {
                messageDiv.textContent = data.message || 'خطا در ثبت اطلاعات بیمار.';
                messageDiv.style.color = 'red';
            }
            form.prepend(messageDiv); // Add message at the top of the form
            setTimeout(() => messageDiv.remove(), 5000); // Remove message after 5 seconds
        })
        .catch(error => {
            console.error('خطا در ارسال اطلاعات:', error);
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('form-message');
            errorDiv.style.color = 'red';
            errorDiv.textContent = 'متاسفانه در ارسال اطلاعات به سرور مشکلی پیش آمد. لطفا دوباره تلاش کنید.';
            form.prepend(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        });
    });

    // --- Initial load of patients ---
    loadPatients();
});
