const express = require("express");
const http = require("http");
const websocket = require("ws");
const cookies = require("cookie-parser");//

const indexRouter = require("./routes/index");
const messages = require("./public/javascripts/messages");
const config = require("./public/javascripts/config");//

const gameStatus = require("./statTracker");
const Game = require("./game");

const port = process.argv[2] || process.env.PORT || 3000;
const app = express();

app.set("view engine", "ejs");// templating
app.use(express.static(__dirname + "/public"));
app.use(cookies(config.COOKIE_SECRET));//

app.get("/", indexRouter);
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
    //increment the number of total visitors
    gameStatus.visitors++;

    let con = ws;
    con.id = connectionID++;
    let playerType = currentGame.addPlayer(con);
    websockets[con.id] = currentGame;

    console.log(`Player ${con.id} placed in game ${currentGame.id} as ${playerType}`);

    /*
     * inform the client about its assigned player type
     */
    con.send((playerType === "WHITE") ? messages.S_PLAYER_WHITE : messages.S_PLAYER_BLACK);

    /*if (playerType === "BLACK"){
      let msg = messages.O_PLACE_A_DISK;
      msg.data = currentGame.getWord();
      con.send(JSON.stringify(msg));
    }*/

    /*
     * once we have two players, there is no way back; 
     * a new game object is created;
     * if a player now leaves, the game is aborted (player is not preplaced)
     */
    if (currentGame.hasTwoConnectedPlayers()) {
        currentGame = new Game(gameStatus.gamesOnGoing++);
    }


    /*
     * message coming in from a player:
     *  1. determine the game object
     *  2. determine the opposing player OP
     *  3. send the message to OP 
     */
    con.on("message", function incoming(message) {

        let oMsg = JSON.parse(message);

        let gameObj = websockets[con.id];
        let isPlayerWhite = (gameObj.playerWhite == con) ? true : false;

        if (isPlayerWhite) {
            if (gameObj.gameState == "WHITE TURN") {
                if (oMsg.type == messages.T_PLACE_A_DISK) {
                    gameObj.playerPlaceDisk("WHITE", oMsg.data);
                    
                    if (gameObj.hasTwoConnectedPlayers()) {
                        gameObj.playerB.send(message);
                    }
                }
            }
            /*
             * player A cannot do a lot, just send the target word;
             * if player B is already available, send message to B
             */
            /*if (oMsg.type == messages.T_TARGET_WORD) {
                gameObj.setWord(oMsg.data);

                if (gameObj.hasTwoConnectedPlayers()) {
                    gameObj.playerB.send(message);
                }
            }*/
        } else {
            /*
             * player B can make a guess; 
             * this guess is forwarded to A
             */
            /*if (oMsg.type == messages.T_MAKE_A_GUESS) {
                gameObj.playerA.send(message);
                gameObj.setStatus("CHAR GUESSED");
            }*/

            /*
             * player B can state who won/lost
             */
            /*if (oMsg.type == messages.T_GAME_WON_BY) {
                gameObj.setStatus(oMsg.data);
                //game was won by somebody, update statistics
                gameStatus.gamesCompleted++;
            }*/
        }
    });


    con.on("close", function (code) {

        /*
         * code 1001 means almost always closing initiated by the client;
         * source: https://developer.mozilla.org/en-US/docs/Web/API/CloseEvent
         */
        console.log(con.id + " disconnected ...");

        if (code == "1001") {
            /*
             * if possible, abort the game; if not, the game is already completed
             */
            let gameObj = websockets[con.id];

            if (gameObj.isValidTransition(gameObj.gameState, "ABORTED")) {
                gameObj.setStatus("ABORTED");
                gameStatus.gamesAborted++;

                /*
                 * determine whose connection remains open;
                 * close it
                 */
                try {
                    gameObj.playerA.close();
                    gameObj.playerA = null;
                } catch (e) {
                    console.log("Player A closing: " + e);
                }

                try {
                    gameObj.playerB.close();
                    gameObj.playerB = null;
                } catch (e) {
                    console.log("Player B closing: " + e);
                }
            }

        }
    });
});

//app.set("／views", __dirname + "views");
//only the route to / needs to be changed

server.listen(port, () => console.log(`Listening on port ${port}`));