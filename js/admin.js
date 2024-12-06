ajaxRequest('GET', 'php/requests.php/adminPanel/', displayProfil);

function displayProfil(profil) {
    const userTableBody = document.querySelector('#userList tbody');

    for (const key in profil.users) {
        if (profil.users.hasOwnProperty(key)) {
            const user = profil.users[key];

            const row = document.createElement('tr');

            const usernameCell = document.createElement('td');
            usernameCell.textContent = user.username;
            row.appendChild(usernameCell);

            const balanceCell = document.createElement('td');
            balanceCell.textContent = user.balance;
            row.appendChild(balanceCell);

            const ibanCell = document.createElement('td');
            ibanCell.textContent = user.iban;
            row.appendChild(ibanCell);

            userTableBody.appendChild(row);
        }
    }
}

document.getElementById('btn-submit').addEventListener('click', function (event) {
    event.preventDefault();

    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];

    var formData = new FormData();
    formData.append('clientFile', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/requests.php/uploadFile', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('Fichier téléchargé avec succès:', xhr.responseText);
        } else {
            console.log('Erreur lors du téléchargement du fichier');
            console.log(xhr.status);
            console.log(xhr.responseText);
        }
    };

    try 
    {
        xhr.send(formData);
    }
    catch (e)
    {
        console.log(e);
    }

});

