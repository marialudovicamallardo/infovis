<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Quadrifoglio</title>
  <script src="https://d3js.org/d3.v6.min.js"></script>
  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
    }

    #quadrifoglio {
      width: 100%;
      height: 100%;
    }
  </style>
</head>

<body>
  <svg id="quadrifoglio"></svg>

  <script>
    document.addEventListener("contextmenu", function (e) {
      e.preventDefault();
    });

    // Lettura dei dati dal file JSON utilizzando D3.js
    async function fetchJSON() {
      try {
        const data = await d3.json('dati.json');
        return data;
      } catch (error) {
        console.error('Errore durante il recupero dei dati JSON:', error);
      }
    }

    // Funzione per disegnare il quadrifoglio
    function drawQuadrifoglio(data) {
      const svg = d3.select("#quadrifoglio");

      // Definizione dei parametri del quadrifoglio
      const radius = 150;
      const petalCount = 4;
      const petalColor = "green";
      const gridSize = 300;
      const margin = 100;

      // Calcolo degli angoli delle foglie del quadrifoglio
      const angle = 2 * Math.PI / petalCount;

      // Calcolo del numero di quadrifogli e delle dimensioni dell'area di disegno
      const quadrifoglioCount = Object.keys(data).length;
      const rows = Math.ceil(Math.sqrt(quadrifoglioCount));
      const cols = Math.ceil(quadrifoglioCount / rows);
      const width = cols * gridSize + 2 * margin;
      const height = rows * gridSize + 2 * margin;

      svg.attr("width", width).attr("height", height);
      let quadrifoglioId = 1; // Variabile di conteggio per gli ID dei quadrifogli
      // Creazione dei quadrifogli
      const quadrifogli = svg
        .selectAll(".quadrifoglio")
        .data(Object.keys(data))
        .enter()
        .append("g")
        .attr("class", "quadrifoglio")
        .attr("id", () => "quadrifoglio" + quadrifoglioId++)
        .attr("transform", (d, i) => {
          const row = Math.floor(i / cols);
          const col = i % cols;
          const x = margin + col * gridSize + gridSize / 2;
          const y = margin + row * gridSize + gridSize / 2;

          return `translate(${x},${y})`;
        })
        .attr("x", (d, i) => margin + (i % cols) * gridSize + gridSize / 2)
        .attr("y", (d, i) => margin + Math.floor(i / cols) * gridSize + gridSize / 2);

      // Creazione dei petali del quadrifoglio
      const petals = quadrifogli
        .selectAll(".petal")
        .data(d => data[d][0].foglie)
        .enter()
        .append("path")
        .attr("class", "petal")
        .attr("fill", petalColor)
        .attr("transform", (d, i) => `rotate(${i * angle * 180 / Math.PI})`)
        .attr("d", (d, i) => {
          const key = Object.keys(d)[0];
          const value = d[key];
          const petalPath = `M0,0 L${value},10 Q${value + 10},0 ${value},-10 Z`;
          return petalPath;
        })
        .on("mousedown", handleMouseClick);

      // Funzione per gestire i click del mouse sui petali del quadrifoglio
      function handleMouseClick(event, d) {
        const selectedPetal = d3.select(this);
        const selectedVariable = Object.keys(d)[0];
        const selectedValue = d[selectedVariable];

        const buttonCode = event.button;

        if (buttonCode === 0) { // Pulsante sinistro del mouse
          moveQuadrifoglio(selectedPetal, selectedVariable, selectedValue, "x", data);
        } else if (buttonCode === 2) { // Pulsante destro del mouse
          moveQuadrifoglio(selectedPetal, selectedVariable, selectedValue, "y", data);
        }
      }

      // Funzione per muovere il quadrifoglio in base al valore selezionato
      function moveQuadrifoglio(selectedPetal, variable, value, axis, data) {
        const quadrifoglio = selectedPetal.node().parentNode.parentNode; // Seleziona il genitore <g> del genitore <path>
        const selectedValue = selectedPetal.data()[0][variable]; // Ottieni il valore corretto del petalo selezionato

        const scaleFactor = axis === "x" ? 10 : 100;
        const translateValue = selectedValue * scaleFactor;

        // Stampa il valore della variabile per tutti i quadrifogli
        var x = 0;
        var y = 0;
        Object.keys(data).forEach(quadrifoglioKey => {
          const foglie = data[quadrifoglioKey][0].foglie;
          foglie.forEach(foglia => {
            const value = foglia[variable];
            if (value != undefined) {
              console.log(`Valore ${variable} del quadrifoglio ${quadrifoglioKey}:`, value);
              var selectedElement = d3.select(`#${quadrifoglioKey}`);

              x = selectedElement.attr("x");
              y = selectedElement.attr("y");

              const newX = axis === "x" ? parseFloat(x) + value : parseFloat(x);
              const newY = axis === "y" ? parseFloat(y) + value : parseFloat(y);
              console.log(`Valore ${variable} del quadrifoglio ${quadrifoglioKey}:`, value);

              console.log("x: " + x + ", y: " + y);
              console.log("newX: " + newX + ", newY: " + newY);

              d3.select(`#${quadrifoglioKey}`)
                .transition()
                .duration(600)
                .attr("x", newX)
                .attr("y", newY)
                .attr("transform", `translate(${newX},${newY})`);
            }
          });
        });
      }

    }
    // Esecuzione del codice
    fetchJSON()
      .then(data => {
        drawQuadrifoglio(data); // Passaggio di 'data' come argomento
      })
      .catch(error => {
        console.error('Errore durante l\'esecuzione del codice:', error);
      });

  </script>
</body>

</html>