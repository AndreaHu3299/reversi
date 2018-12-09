function Board() {
    this.board = document.getElementById("gameBoard");
    this.updateBoard = function (disksList) {
        console.assert(Array.isArray(disksList) || typeof disksList == "string", `Expecting an array, got a ${typeof disksList} instead`);
        if (Array.isArray(disksList)) {
            disksList.forEach(disk => {
                let classes = ["disk"];
                Board.prototype.CELL_WHITE = 1;
                Board.prototype.CELL_BLACK = 2;
                Board.prototype.CELL_WHITE_HINT = 3;
                Board.prototype.CELL_BLACK_HINT = 2;
                switch (disk.value) {
                case 1: //cell white
                case 3:
                    classes.push("disk_white");
                    if (disk.value === 3) classes.push("diskHint");
                    break;
                case 2: //cell black
                case 4:
                    classes.push("disk_black");
                    if (disk.value === 4) classes.push("diskHint");
                    break;
                default: //fallback
                    classes.push("hidden");
                }
                this.board.children[disk.y * 8 + disk.x].className = classes.join(" ");
            });
        }
    };
}

function PlayerWhite() {
    this.timer;
    this.timerDisplay = document.getElementById("player1Timer");

    this.updateDiskCount = function (numDisks) {
        console.assert(((typeof numDisks === "number" && (numDisks % 1) === 0)) || typeof disksList == "string", `Expecting an array, got a ${typeof disksList} instead`);
    };

    this.startCounter = function() {
        this.timer = setInterval(myClock, 1000);
        let c = Setup.TIMER_MAX_TIME;
        function myClock() {
            this.timerDisplay.innerHTML = --c;
            if (c == 0) {
                clearInterval(this.timer);
                alert("Player white Gameover!");
                //TODO add gameover
            }
        }
    };

    this.stopCounter = function () {
        clearInterval(this.timer);
    };
}

function PlayerBlack() {

}