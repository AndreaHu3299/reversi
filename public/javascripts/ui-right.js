/* ESLint global variables information */
/* global Setup*/

function PlayerWhite(gameState) {
    this.timer;
    this.gameState = null;
    this.timerDisplay = document.getElementById("player1Timer");
    this.diskCount = document.getElementById("player1DiskCount");

    this.updateDiskCount = function (numDisks) {
        console.assert(((typeof numDisks === "number" && (numDisks % 1) === 0)) || typeof disksList == "string", `Expecting an array, got a ${typeof disksList} instead`);
        this.diskCount.innerHTML = numDisks;
    };

    this.setPlayerName = function (name) {
        document.getElementById("player1Username").innerHTML = name;
    };

    this.startCounter = function () {
        this.stopCounter();
        this.timer = setInterval(myClock, 1000);
        let c = Setup.TIMER_MAX_TIME;
        const _this = this;

        function myClock() {
            _this.timerDisplay.innerHTML = --c;
            if (c == 0) {
                clearInterval(_this.timer);
                console.log("Player white Gameover!");
                //TODO add gameover
                _this.gameState.sendTimeout("BLACK");
            }
        }
    };

    this.stopCounter = function () {
        clearInterval(this.timer);
    };

    this.setGameState = function (gameState) {
        this.gameState = gameState;
    };
}

function PlayerBlack(gameState) {
    this.timer;
    this.gameState = null;
    this.gameState = gameState;
    this.timerDisplay = document.getElementById("player2Timer");
    this.diskCount = document.getElementById("player2DiskCount");

    this.updateDiskCount = function (numDisks) {
        console.assert(((typeof numDisks === "number" && (numDisks % 1) === 0)) || typeof disksList == "string", `Expecting an array, got a ${typeof disksList} instead`);
        this.diskCount.innerHTML = numDisks;
    };

    this.setPlayerName = function (name) {
        document.getElementById("player2Username").innerHTML = name;
    };

    this.startCounter = function () {
        this.stopCounter();
        this.timer = setInterval(myClock, 1000);
        let c = Setup.TIMER_MAX_TIME;
        const _this = this;

        function myClock() {
            _this.timerDisplay.innerHTML = --c;
            if (c == 0) {
                clearInterval(_this.timer);
                console.log("Player black gameover!");
                _this.gameState.sendTimeout("WHITE");
            }
        }
    };

    this.stopCounter = function () {
        if (this.timer != undefined) {
            clearInterval(this.timer);
        }
    };

    this.setGameState = function (gameState) {
        this.gameState = gameState;
    };
}

function disableLoadingScreen() {
    document.getElementById("loader-wrapper").style.display = "none";
}

/*
 * Object representing the status bar.
 */
function StatusBar() {
    this.setStatus = function (status) {
        document.getElementById("status").innerHTML = status;
    };
}