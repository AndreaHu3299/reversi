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
    gameStatus.playersOnline++;
    let con = ws;
    con.id = connectionID++;
    let playerType = currentGame.addPlayer(con);
    websockets[con.id] = currentGame;

    console.log(`Player ${con.id} placed in game ${currentGame.id} as ${playerType}`);

    /*
     * inform the client about its assigned player type
     */
    con.send((playerType === "WHITE") ? messages.S_PLAYER_WHITE : messages.S_PLAYER_BLACK);

    if (playerType === "BLACK") {
        let msg = messages.S_GAME_STARTED;
        con.send(msg);
        let gameObj = websockets[con.id];
        gameObj.playerWhite.send(msg);
        //start game, set gamestate to white turn
        gameObj.setStatus("BLACK TURN");
        //send first game state to players
        let playerMsg = messages.O_BOARD_STATE;
        playerMsg.data = gameObj.getBoard(gameObj.board.CELL_WHITE);
        gameObj.playerWhite.send(JSON.stringify(playerMsg));
        let otherPlayerMsg = messages.O_BOARD_STATE;
        otherPlayerMsg.data = gameObj.getBoard(gameObj.board.CELL_BLACK);
        gameObj.playerBlack.send(JSON.stringify(otherPlayerMsg));
    }

    /*
     * once we have two players, there is no way back; 
     * a new game object is created;
     * if a player now leaves, the game is aborted (player is not preplaced)
     */
    if (currentGame.hasTwoConnectedPlayers()) {
        currentGame = new Game(gameStatus.gamesOnGoing++);
    }


    /*
     * message coming in from a player
     */
    con.on("message", function incoming(message) {

        let oMsg = JSON.parse(message);

        let gameObj = websockets[con.id];
        let isPlayerWhite = (gameObj.playerWhite == con) ? true : false;

        if (oMsg.type == messages.T_PLACE_A_DISK) {
            const otherPlayer = (isPlayerWhite) ? gameObj.playerBlack : gameObj.playerWhite;
            if (isPlayerWhite && gameObj.gameState === "WHITE TURN") {
                gameObj.playerPlaceDisk("WHITE", oMsg.data);
            } else if (!isPlayerWhite && gameObj.gameState === "BLACK TURN") {
                gameObj.playerPlaceDisk("BLACK", oMsg.data);
            }
            if (gameObj.gameover != undefined) {
                let gameoverMsg = messages.O_GAME_OVER;
                const winner = gameObj.getWinner();
                let playerWinner;
                let playerLoser;
                switch (winner) {
                case "DRAW":
                case "WHITE":
                    playerWinner = gameObj.playerWhite;
                    playerLoser = gameObj.playerBlack;
                    break;
                case "BLACK":
                    playerWinner = gameObj.playerBlack;
                    playerLoser = gameObj.playerWhite;
                    break;
                }
                if (winner !== "DRAW") {
                    gameoverMsg.data = "gameWon";
                    playerWinner.send(JSON.stringify(gameoverMsg));
                    gameoverMsg.data = "gameLost";
                    playerLoser.send(JSON.stringify(gameoverMsg));
                } else {
                    gameoverMsg.data = "gameDraw";
                    playerWinner.send(JSON.stringify(gameoverMsg));
                    playerLoser.send(JSON.stringify(gameoverMsg));
                }
                gameObj.finalStatus = winner;
            } else {
                let playerTurnMsg = messages.O_PLAYER_TURN;
                playerTurnMsg.data = gameObj.gameState;
                con.send(JSON.stringify(playerTurnMsg));
                otherPlayer.send(JSON.stringify(playerTurnMsg));

                let playerMsg = messages.O_BOARD_STATE;
                playerMsg.data = gameObj.getBoard((isPlayerWhite) ? gameObj.board.CELL_WHITE : gameObj.board.CELL_BLACK);
                con.send(JSON.stringify(playerMsg));

                let otherPlayerMsg = messages.O_BOARD_STATE;
                otherPlayerMsg.data = gameObj.getBoard((isPlayerWhite) ? gameObj.board.CELL_BLACK : gameObj.board.CELL_WHITE);
                otherPlayer.send(JSON.stringify(otherPlayerMsg));
            }
        }


        if (oMsg.type == messages.T_TIMEOUT) {
            let gameoverMsg = messages.O_GAME_OVER;
            const winner = oMsg.data;
            let playerWinner;
            let playerLoser;
            switch (winner) {
            case "WHITE":
                playerWinner = gameObj.playerWhite;
                playerLoser = gameObj.playerBlack;
                break;
            case "BLACK":
                playerWinner = gameObj.playerBlack;
                playerLoser = gameObj.playerWhite;
                break;
            }
            gameoverMsg.data = "gameWon";
            playerWinner.send(JSON.stringify(gameoverMsg));
            gameoverMsg.data = "gameLost";
            playerLoser.send(JSON.stringify(gameoverMsg));
            gameObj.finalStatus = winner;
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
                    gameObj.playerWhite.close();
                    gameObj.playerWhite = null;
                } catch (e) {
                    console.log("Player White closing: " + e);
                }

                try {
                    gameObj.playerBlack.close();
                    gameObj.playerBlack = null;
                } catch (e) {
                    console.log("Player Black closing: " + e);
                }
            }
            gameStatus.playersOnline--;
        }
    });
});

server.listen(port, () => console.log(`Listening on port ${port}`));