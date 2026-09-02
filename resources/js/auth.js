javascript
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | PASSWORD TOGGLE
    |--------------------------------------------------------------------------
    */

    const passwordInput =
        document.getElementById('password');

    const passwordToggle =
        document.getElementById('passwordToggle');


    if (passwordInput && passwordToggle) {

        passwordToggle.addEventListener('click', function () {

            const icon =
                passwordToggle.querySelector('i');


            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                icon.classList.remove('bi-eye');

                icon.classList.add('bi-eye-slash');

                passwordToggle.setAttribute(
                    'aria-label',
                    'Sembunyikan password'
                );

            } else {

                passwordInput.type = 'password';

                icon.classList.remove('bi-eye-slash');

                icon.classList.add('bi-eye');

                passwordToggle.setAttribute(
                    'aria-label',
                    'Tampilkan password'
                );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | LOGIN FORM
    |--------------------------------------------------------------------------
    */

    const loginForm =
        document.getElementById('loginForm');

    const loginButton =
        document.getElementById('loginButton');


    if (loginForm && loginButton) {

        loginForm.addEventListener('submit', function () {

            /*
             * Jangan cegah submit.
             * Biarkan Laravel memproses form.
             */

            loginButton.classList.add('loading');

            loginButton.disabled = true;

        });

    }



    /*
    |--------------------------------------------------------------------------
    | INPUT FOCUS EFFECT
    |--------------------------------------------------------------------------
    */

    const inputs =
        document.querySelectorAll('.input-wrapper input');


    inputs.forEach(function (input) {

        input.addEventListener('focus', function () {

            const wrapper =
                input.closest('.input-wrapper');


            if (wrapper) {

                wrapper.classList.add('focused');

            }

        });


        input.addEventListener('blur', function () {

            const wrapper =
                input.closest('.input-wrapper');


            if (wrapper) {

                wrapper.classList.remove('focused');

            }

        });

    });



    /*
    |--------------------------------------------------------------------------
    | REMOVE LOADING WHEN BACK BUTTON IS USED
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', function () {

        if (loginButton) {

            loginButton.classList.remove('loading');

            loginButton.disabled = false;

        }

    });

});
