ajaxRequest('GET', 'php/requests.php/synthese/', displayProfil);

console.log('synthese.js');

function displayProfil(profil){
    console.log(profil.balance);
    document.getElementById('balance').innerHTML = profil.balance;
    for (const key in profil.transactions) {
        document.getElementById('transactions').innerHTML += `<li>${profil.transactions[key].amount} ${profil.transactions[key].description}</li>`;
    }
}
