const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = 3000;

app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, '..'))); // Mettre à jour le chemin pour les fichiers statiques

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '..', 'account.html')); // Mettre à jour le chemin pour account.html
});

app.post('/create-account', (req, res) => {
    const { username, password } = req.body;

    // Enregistrer les informations d'identification dans un fichier (ou une base de données)
    const userData = { username, password };
    fs.writeFileSync(path.join(__dirname, '..', 'users.json'), JSON.stringify(userData, null, 2)); // Mettre à jour le chemin pour users.json

    res.json({ success: true });
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});