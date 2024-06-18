// Načítanie obsahu YAML súboru
fetch('your_api.yaml')
    .then(response => response.text())
    .then(yaml => {
        // Prevod YAML na JavaScript objekt
        const data = jsyaml.load(yaml);

        // Zobrazenie YAML obsahu na webovej stránke
        document.getElementById('yamlContent').innerText = JSON.stringify(data, null, 2);
    })
    .catch(error => console.log('Error loading YAML file:', error));
