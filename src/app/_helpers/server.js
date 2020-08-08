const express = require('express')
const app = express()
const port = 3000

// set up our routes

app.get('/', (req, res) => res.send('Hello World!'))

app.get("/hello", function (req, res) {
    res.send("Hello World!");
});

app.get("/goodbye", function (req, res) {
    res.send("Goodbye World!");
});

app.listen(port, () => console.log(`Example app listening at http://localhost:${port}`))
