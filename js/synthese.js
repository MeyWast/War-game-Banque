let username;
ajaxRequest('GET', 'php/requests.php/synthese/', (response) => {
    displayProfil(response);
});

function displayProfil(profil) {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(profil, "application/xml");

    const productId = xmlDoc.getElementsByTagName("productId")[0].childNodes[0].nodeValue;
    const balance = xmlDoc.getElementsByTagName("balance")[0].childNodes[0].nodeValue;

    document.getElementById('user').innerHTML = productId;
    document.getElementById('balance').innerHTML = balance;
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