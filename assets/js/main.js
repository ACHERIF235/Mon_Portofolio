document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.lang-link');
    links.forEach((link) => {
        link.addEventListener('click', function () {
            links.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const contactForm = document.querySelector('form[data-validate="contact"]');
    if (contactForm) {
        const statusContainer = document.getElementById('contact-form-status');

        contactForm.addEventListener('submit', function (event) {
            const name = contactForm.querySelector('input[name="name"]');
            const email = contactForm.querySelector('input[name="email"]');
            const message = contactForm.querySelector('textarea[name="message"]');
            let valid = true;
            let messageText = '';

            [name, email, message].forEach((field) => {
                if (!field.value.trim()) {
                    field.classList.add('input-error');
                    valid = false;
                } else {
                    field.classList.remove('input-error');
                }
            });

            if (email.value.trim() && !validateEmail(email.value.trim())) {
                email.classList.add('input-error');
                valid = false;
                messageText = 'Invalid email address';
            }

            if (!valid) {
                event.preventDefault();
                if (!messageText) {
                    messageText = 'Veuillez remplir tous les champs correctement.';
                }
                if (statusContainer) {
                    statusContainer.innerHTML = '<div class="form-status error">' + messageText + '</div>';
                }
            }
        });
    }
});

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
