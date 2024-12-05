const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');

const app = express();
const PORT = 3000;

app.use(bodyParser.json());

app.post('/create-account', (req, res) => {
    const { username, password } = req.body;

    // Enregistrer les informations d'identification dans un fichier (ou une base de données)
    const userData = { username, password };
    fs.writeFileSync('users.json', JSON.stringify(userData, null, 2));

    res.json({ success: true });
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});