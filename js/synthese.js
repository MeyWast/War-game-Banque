let username;
ajaxRequest('GET', 'php/requests.php/synthese/', displayProfil);



function displayProfil(profil){
    console.log(profil.balance);
    
    username = profil.user;
    document.getElementById('user').innerHTML = profil.user;
    document.getElementById('balance').innerHTML = profil.balance;
    for (const key in profil.transactions) {
        document.getElementById('transactions').innerHTML += `<li>${profil.transactions[key].amount} ${profil.transactions[key].description}</li>`;
    }
}

document.getElementById('downloadLogs').addEventListener('click', () => {
    const fileName = `../iban/iban_${username}.txt`;

    window.location.href = `php/requests.php/downloadLogs/?file=${fileName}`;
});

document.getElementById('btn-submit').addEventListener('click', () => {
    user_give = username;
    user_get = document.getElementById('user_get').value;
    amount = document.getElementById('amount').value;
    description = document.getElementById('description').value;

    const requestData = `user_give=${user_give}&user_get=${user_get}&amount=${amount}&description=${description}`;

    // console.log(requestData);
    ajaxRequest('POST', 'php/requests.php/transaction/', handleResponse, requestData);
    
});

function handleResponse (responseObject) {
    if (responseObject.ok) {
        // raffraichir la page
        location.reload();
    } else {
        while (document.getElementById('form-messages').firstChild) {
            document.getElementById('form-messages').removeChild(document.getElementById('form-messages').firstChild);
        }
        responseObject.messages.forEach((messages) => {
            const li = document.createElement('li');
            li.textContent = messages;
            document.getElementById('form-messages').appendChild(li);
        });
    }
}