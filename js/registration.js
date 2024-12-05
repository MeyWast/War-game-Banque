document.getElementById('btn-submit').addEventListener('click', () => {

    username = document.getElementById('user').value;
    password = document.getElementById('pwd').value;

    const requestData = `username=${username}&password=${password}`;
    ajaxRequest('GET', 'php/requests.php/authentification/', handleResponse, requestData);
    // console.log('http://localhost/WAR-GAME-PROJECT/War-game-Banque/php/requests.php/authentification/?' + requestData);
    
});

function handleResponse (responseObject) {
    if (responseObject.ok) {
        // location.href = 'synthese.html';
        console.log('authentification réussie');
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