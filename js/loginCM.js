document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.querySelector('#loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const username = document.getElementById('username-field').value.trim();
            const password = document.getElementById('password-field').value.trim();

            if (username === '' || password === '') {
                alert('Please fill in both username and password.');
                return;
            }

            fetch('loginCM.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    username: username,
                    password: password,
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Handle 401 Unauthorized
                            return response.json().then(data => {
                                throw new Error(data.message || 'Unauthorized');
                            });
                        }
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                    }
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error during fetch:', error);
                    alert(error.message);
                });
        });
    }
});
