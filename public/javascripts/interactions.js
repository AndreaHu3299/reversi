/* ESLint global variables information */
/* global Setup, Status, Messages*/

function GameState(board, playerWhite, playerBlack, statusBar, socket) {

    this.playerType = null;
    this.board = board;
    board.setGameState(this);
    this.playerWhite = playerWhite;
    this.playerWhite.setGameState(this);
    this.playerBlack = playerBlack;
    this.playerBlack.setGameState(this);
    this.statusBar = statusBar;
    this.socket = socket;
    this.playerTurn = null;

    this.getPlayerType = function () {
        return this.playerType;
    };

    this.setPlayerType = function (p) {
        console.assert(typeof p == "string", `${arguments.callee.name}: Expecting a string, got a ${typeof p}`);
        this.playerType = p;
        const pYou = (p == "WHITE") ? this.playerWhite : this.playerBlack;
        const pOpp = (p == "WHITE") ? this.playerBlack : this.playerWhite;
        pYou.setPlayerName("You");
        pOpp.setPlayerName("Opponent");
    };

    this.sendMove = function (clickedCell) {
        console.assert((typeof clickedCell == "string" && clickedCell.length == 2), `${arguments.callee.name}: Expecting a string of length 2, got a ${typeof clickedLetter}`);
        if (document.getElementById("cell_" + clickedCell).getElementsByClassName("diskHint").length > 0) {
            this.stopCounters();
            var outgoingMsg = Messages.O_PLACE_A_DISK;
            outgoingMsg.data = clickedCell;
            socket.send(JSON.stringify(outgoingMsg));
        }
    };

    this.sendTimeout = function (winner) {
        console.assert(typeof winner == "string", `${arguments.callee.name}: Expecting a string, got a ${typeof clickedLetter}`);

        var outgoingMsg = Messages.O_TIMEOUT;
        outgoingMsg.data = winner;
        this.socket.send(JSON.stringify(outgoingMsg));
    };

    this.updateDiskCounts = function (diskCounts) {
        playerWhite.updateDiskCount(diskCounts.playerWhite);
        playerBlack.updateDiskCount(diskCounts.playerBlack);
    };

    this.setPlayerTurn = function (player) {
        this.playerTurn = player;
    };

    this.startCounters = function () {
        if (this.playerTurn == "WHITE TURN") {
            playerWhite.startCounter();
        } else {
            playerBlack.startCounter();
        }
    };

    this.stopCounters = function () {
        playerWhite.stopCounter();
        playerBlack.stopCounter();
    };

    this.gameover = function () {
        this.stopCounters();
        const hintDisks = document.querySelectorAll(".diskHint");
        Array.from(hintDisks).forEach(function (disk) {
            disk.parentElement.removeChild(disk);
        });
    };

    //main update function
    this.updateBoard = function (board) {
        this.startCounters();
        this.board.updateBoard(board);
        this.board.initialize();
    };
}

function GameBoard() {
    this.gameState = null;
    this.boardView = document.getElementById("gameBoard");

    this.updateBoard = function (disksList) { //TODO change to matrix
        console.assert(Array.isArray(disksList) || typeof disksList == "string", `Expecting an array, got a ${typeof disksList} instead`);
        let whiteCount = 0;
        let blackCount = 0;
        if (Array.isArray(disksList)) {
            disksList.forEach(row => {
                if (Array.isArray(row)) {
                    row.forEach(cell => {
                        if (cell.value != 0) {
                            let classes = ["disk"];
                            switch (cell.value) {
                                case 1: //cell white
                                    whiteCount++;
                                case 3:
                                    classes.push("disk_white");
                                    if (cell.value === 3) classes.push("diskHint");
                                    break;
                                case 2: //cell black
                                    blackCount++;
                                case 4:
                                    classes.push("disk_black");
                                    if (cell.value === 4) classes.push("diskHint");
                                    break;
                            }
                            const currentCellIndex = cell.y * 8 + cell.x;
                            if (this.boardView.children[currentCellIndex].getElementsByClassName("disk").length == 0) {
                                const newDisk = document.createElement("div");
                                newDisk.className = "disk";
                                this.boardView.children[currentCellIndex].appendChild(newDisk);
                            }
                            this.boardView.children[currentCellIndex].getElementsByClassName("disk")[0].className = classes.join(" ");
                        }
                    });
                }
            });
            this.gameState.updateDiskCounts({
                playerWhite: whiteCount,
                playerBlack: blackCount
            });
            this.gameState.startCounters();
        }
    };
    //initialize after getting the board state
    this.initialize = function () {

        const disks = document.querySelectorAll(".diskHint");
        const _this = this;
        Array.from(disks).forEach(function (el) {
            el.addEventListener("click", function singleClick(e) {
                const clickedCell = e.target.parentElement.dataset.cellcoord;
                new Audio("../sound/click.wav").play();
                _this.gameState.sendMove(clickedCell);

                //remove all disk click listeners
                Array.from(disks).forEach(function (disk) {
                    disk.parentElement.removeChild(disk);
                });
            });
        });
    };

    this.setGameState = function (gameState) {
        this.gameState = gameState;
    };
}


//set everything up, including the WebSocket
(function setup() {
    var socket = new WebSocket(Setup.WEB_SOCKET_URL);

    const gameBoard = new GameBoard();
    const playerWhite = new PlayerWhite();
    const playerBlack = new PlayerBlack();
    const statusBar = new StatusBar();

    var gameState = new GameState(gameBoard, playerWhite, playerBlack, statusBar, socket);

    socket.onmessage = function (event) {

        let incomingMsg = JSON.parse(event.data);
        if (incomingMsg.type == Messages.T_GAME_STARTED) {
            //alert("DISABLYING SCREEN"); //TODO remove
            disableLoadingScreen();
        }

        if (incomingMsg.type == Messages.T_BOARD_STATE) {
            //alert("GAMEBOARD UPDATED"); TODO remove
            gameState.updateBoard(incomingMsg.data);
        }

        if (incomingMsg.type == Messages.T_PLAYER_TURN) {
            gameState.setPlayerTurn(incomingMsg.data);
            statusBar.setStatus(Status[(incomingMsg.data=="WHITE TURN")?"turnWhite":"turnBlack"]);
        }

        //set player type
        if (incomingMsg.type == Messages.T_PLAYER_TYPE) {
            //alert("IM PLAYER "+incomingMsg.data); //TODO remov
            gameState.setPlayerType(incomingMsg.data); //should be "WHITE" or "BLACK"
        }

        if (incomingMsg.type == Messages.T_GAME_OVER) {
            statusBar.setStatus(incomingMsg.data);
            gameState.gameover();
        }
    };

    socket.onopen = function () {
        socket.send("{}");
    };

    //server sends a close event only if the game was aborted from some side
    socket.onclose = function () {
        if (gameState.gameover === undefined) {
            statusBar.setStatus(Status["aborted"]);
        }
    };

    socket.onerror = function () {};
})(); //execute immediately