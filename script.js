document.getElementById('createAccountForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch('/create-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account created successfully!');
        } else {
            alert('Error creating account.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});