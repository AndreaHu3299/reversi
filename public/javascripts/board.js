class Cell {
    constructor(x, y, value) {
        this.x = x;
        this.y = y;
        this.value = value;
    }

    getX() {
        return this.x;
    }

    getY() {
        return this.y;
    }

    getValue() {
        return this.value;
    }

    setValue(newValue) {
        this.value = newValue;
    }
}

var Board = function () {
    // Initialize 8x8 matrix of Cells 
    this.board = Array(...Array(8)).map((a, y) => Array(...Array(8)).map((b, x) => new Cell(x, y, this.CELL_EMPTY)));

    // Initialization of the first 4 disks
    this.board[3][3].setValue(this.CELL_WHITE);
    this.board[3][4].setValue(this.CELL_BLACK);
    this.board[4][3].setValue(this.CELL_BLACK);
    this.board[4][4].setValue(this.CELL_WHITE);
};

Board.prototype.CELL_EMPTY = 0;
Board.prototype.CELL_WHITE = 1;
Board.prototype.CELL_BLACK = 2;
Board.prototype.CELL_WHITE_HINT = 3;
Board.prototype.CELL_BLACK_HINT = 4;

Board.prototype.DIRECTION = [ // Defines different directions based on polar coordinates int the format of (y, x)
    { // 0
        "direction": "N",
        "x": 0,
        "y": -1
    },
    { // 1
        "direction": "NE",
        "x": 1,
        "y": -1
    },
    { // 2
        "direction": "E",
        "x": 1,
        "y": 0
    },
    { // 3
        "direction": "SE",
        "x": 1,
        "y": 1
    },
    { // 4
        "direction": "S",
        "x": 0,
        "y": 1
    },
    { // 5
        "direction": "SW",
        "x": -1,
        "y": 1
    },
    { // 6
        "direction": "W",
        "x": -1,
        "y": 0
    },
    { // 7g
        "direction": "NW",
        "x": -1,
        "y": -1
    }
];


/**
 * Given a disk color and the position to place it to, it checks if it"s possible for that disk to be placed there
 * and if possible, it gets placed there.
 *
 * @param {*} player either CELL_BLACK or CELL_WHITE
 * @param {String} position a two character string that contains the targeted cell (e.g.: f2)
 * Returns
 * @returns {boolean} return true if it was a valid move, false if it wasn"t;
 */
Board.prototype.placeDisk = function (player, position) {
    const cell = this.convertPosition(position);
    //console.assert(typeof player == "string", `${arguments.callee.name}: Expecting a string, got a ${typeof player}`);
    const expected = (player === this.CELL_WHITE) ? this.CELL_WHITE_HINT : this.CELL_BLACK_HINT;
    this.updateBoard(player);
    const result = (this.board[cell.y][cell.x].getValue() == expected);
    this.board[cell.y][cell.x].setValue(player);
    this.getValidDirections(player, cell).forEach((dir) => {
        for (let x = cell.x + dir.x, y = cell.y + dir.y; x >= 0 && x < 8 && y >= 0 && y < 8; x += dir.x, y += dir.y) {
            if (this.board[y][x].getValue() === player) {
                break;
            } else {
                this.board[y][x].setValue(player);
            }
        }
    });
    return result;
};

Board.prototype.getBoard = function (viewPlayer, turnPlayer) {
    if (viewPlayer == turnPlayer) {
        this.updateBoard(turnPlayer);
    } else {
        this.cleanBoard();
    }
    return this.board;
};

/**
 *  
 * @param {*} player
 * @param {*} cell
 */
Board.prototype.getValidDirections = function (player, cell) {
    const opponent = (player === this.CELL_WHITE) ? this.CELL_BLACK : this.CELL_WHITE;
    const validDirections = this.DIRECTION.filter((dir) => {
        const tempX = cell.x + dir.x;
        const tempY = cell.y + dir.y;
        return (!(tempX < 0 || tempX > 7 || tempY < 0 || tempY > 7) && this.board[cell.y + dir.y][cell.x + dir.x].getValue() === opponent) ? this.checkValidActionDirection(player, cell, dir) : false;
    });
    return validDirections;
};


Board.prototype.checkValidActionDirection = function (player, cell, direction) {
    for (let x = cell.x + direction.x * 2, y = cell.y + direction.y * 2; x >= 0 && x < 8 && y >= 0 && y < 8; x += direction.x, y += direction.y) {
        const disk = this.board[y][x].getValue();

        if (disk === player) {
            return true;
        } else if (disk === this.CELL_EMPTY) {
            break;
        }
    }
    return false;
};

/**
 * Converts a string format of a cell to its indexes x and y
 * @param {*} position a string representation of a cell on the board of type (a1)
 * @returns {Object} returns an object containing values x and y of the given position
 */
Board.prototype.convertPosition = function (position) {
    // check valid input
    console.assert(typeof position == "string", `${arguments.callee.name}: Expecting a string, got a ${typeof player}`);
    console.assert(position.length === 2, `${arguments.callee.name}: Expecting a string of length 2, got length ${position.length}`);

    // 
    let x = position.charCodeAt(0) - 97; // 97 = ASCII(a)
    let y = position.charAt(1) - 1; // 48 = ASCII(0)

    console.assert((x >= 0 && x < 8), `${arguments.callee.name}: Expecting first character to be small case character between a and h, got ${position.charAt(0)}`);
    console.assert((y >= 0 && y < 8), `${arguments.callee.name}: Expecting second character to be a digit between 1 and 8, got a ${position.charAt(1)}`);

    return {
        x: x,
        y: y
    };
};

Board.prototype.updateBoard = function (player) {
    const hintCell = (player === this.CELL_WHITE) ? this.CELL_WHITE_HINT : this.CELL_BLACK_HINT;
    this.board.forEach((row, y) => {
        row.forEach((cell, x) => {
            if (cell.getValue() != this.CELL_WHITE && cell.getValue() != this.CELL_BLACK && this.getValidDirections(player, {
                    x: x,
                    y: y
                }).length > 0) this.board[y][x].setValue(hintCell);
        });
    });
};

Board.prototype.cleanBoard = function () {
    this.board.forEach((row) => {
        row.forEach((cell) => {
            if (cell.getValue() === this.CELL_WHITE_HINT || cell.getValue() === this.CELL_BLACK_HINT)
                cell.setValue(this.CELL_EMPTY);
        });
    });
};

Board.prototype.isGameover = function (player) {
    this.updateBoard(player);
    return (!this.hasValidMovesLeft());
};

Board.prototype.hasValidMovesLeft = function () {
    return this.board.reduce((accBoard, row) => {
        return accBoard || row.reduce((accRow, cell) => {
            return accRow || (cell.getValue() === this.CELL_WHITE_HINT || cell.getValue() === this.CELL_BLACK_HINT);
        }, false);
    }, false);
};

Board.prototype.getDiskCounts = function () {
    //can be included in one single pass
    let playerWhiteCount = this.board.reduce((accBoard, row) => {
        return accBoard + row.reduce((accRow, cell) => {
            return (cell.getValue() === this.CELL_WHITE) ? accRow + 1 : accRow;
        }, 0);
    }, 0);
    let playerBlackCount = this.board.reduce((accBoard, row) => {
        return accBoard + row.reduce((accRow, cell) => {
            return (cell.getValue() === this.CELL_BLACK) ? accRow + 1 : accRow;
        }, 0);
    }, 0);
    return {
        playerWhiteCount: playerWhiteCount,
        playerBlackCount: playerBlackCount
    };
};

module.exports = Board;