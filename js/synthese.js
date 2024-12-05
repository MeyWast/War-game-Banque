let username;
ajaxRequest('GET', 'php/requests.php/synthese/', displayProfil);



function displayProfil(profil){
    console.log(profil.balance);
    
    username = profil.user;
    document.getElementById('balance').innerHTML = profil.balance;
    for (const key in profil.transactions) {
        document.getElementById('transactions').innerHTML += `<li>${profil.transactions[key].amount} ${profil.transactions[key].description}</li>`;
    }
}

document.getElementById('downloadLogs').addEventListener('click', () => {
    const fileName = `../logs/logs_${username}.txt`;

    window.location.href = `php/requests.php/downloadLogs/?file=${fileName}`;
});
