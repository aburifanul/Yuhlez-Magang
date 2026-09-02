document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | PASSWORD TOGGLE
    |--------------------------------------------------------------------------
    */

    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {

        togglePassword.addEventListener('click', function () {

            const icon = this.querySelector('i');

            if (password.type === 'password') {

                password.type = 'text';

                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }

                this.setAttribute(
                    'aria-label',
                    'Sembunyikan password'
                );

            } else {

                password.type = 'password';

                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }

                this.setAttribute(
                    'aria-label',
                    'Tampilkan password'
                );
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRM PASSWORD TOGGLE
    |--------------------------------------------------------------------------
    */

    const togglePasswordConfirmation =
        document.getElementById('togglePasswordConfirmation');

    const passwordConfirmation =
        document.getElementById('password_confirmation');

    if (
        togglePasswordConfirmation &&
        passwordConfirmation
    ) {

        togglePasswordConfirmation.addEventListener(
            'click',
            function () {

                const icon = this.querySelector('i');

                if (
                    passwordConfirmation.type === 'password'
                ) {

                    passwordConfirmation.type = 'text';

                    if (icon) {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }

                    this.setAttribute(
                        'aria-label',
                        'Sembunyikan password'
                    );

                } else {

                    passwordConfirmation.type = 'password';

                    if (icon) {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }

                    this.setAttribute(
                        'aria-label',
                        'Tampilkan password'
                    );
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE ALERT
    |--------------------------------------------------------------------------
    */

    const alerts = document.querySelectorAll('.auth-alert');

    alerts.forEach(function (alert) {

        const closeButton =
            alert.querySelector('.auth-alert-close');

        if (closeButton) {

            closeButton.addEventListener(
                'click',
                function () {
                    alert.remove();
                }
            );

        }

    });


    /*
    |--------------------------------------------------------------------------
    | DISABLE SUBMIT BUTTON AFTER SUBMIT
    |--------------------------------------------------------------------------
    */

    const forms = document.querySelectorAll(
        '.auth-card form'
    );

    forms.forEach(function (form) {

        form.addEventListener('submit', function () {

            const submitButton =
                form.querySelector(
                    '.auth-submit-button'
                );

            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;

            const text =
                submitButton.querySelector('span');

            if (text) {
                text.textContent = 'Memproses...';
            }

        });

    });

});