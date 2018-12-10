const express = require("express");
const http = require("http");
const websocket = require("ws");

const indexRouter = require("./routes/index");
const messages = require("./public/javascripts/messages");

const gameStatus = require("./statTracker");
const Game = require("./game");

const port = process.argv[2] || process.env.PORT || 3000;
const app = express();

app.set("view engine", "ejs");
app.use(express.static(__dirname + "/public"));

app.get("/play", indexRouter);

var server = http.createServer(app);
const wss = new websocket.Server({
  server
});

var websockets = {};

/*
 * regularly clean up the websockets object
 */
setInterval(function () {
  for (let i in websockets) {
    if (websockets.hasOwnProperty(i)) {
      let gameObj = websockets[i];
      //if the gameObj has a final status, the game is complete/aborted
      if (gameObj.finalStatus != null) {
        console.log("\tDeleting element " + i);
        delete websockets[i];
      }
    }
  }
}, 50000);

var currentGame = new Game(gameStatus.gamesOnGoing++);
var connectionID = 0; //each websocket receives a unique ID

wss.on("connection", (ws) => {
  let con = ws;
  con.id = connectionID++;
  let playerType = currentGame.addPlayer(con);
  websockets[con.id] = currentGame;

  console.log(`Player ${con.id} placed in game ${currentGame.id} as ${playerType}`);

  /*
   * inform the client about its assigned player type
   */
  con.send((playerType == "WHITE") ? messages.S_PLAYER_WHITE : messages.S_PLAYER_BLACK);

});

app.set("／views", __dirname + "views");
//only the route to / needs to be changed
app.get("/", (req, res) => {
  //example of data to render; here gameStatus is an object holding this information
  res.render("splash.ejs", {
    visitors: gameStatus.visitors,
    gamesOnGoing: gameStatus.gamesOngoing,
    playerOnline: gameStatus.playersOnline
  });
})
server.listen(port, () => console.log(`Listening on port ${port}`));