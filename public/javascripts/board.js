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
Board.prototype.CELL_BLACK_HINT = 2;

Board.prototype.DIRECTION = [ // Defines different directions based on polar coordinates int the format of (y, x)
    {
        "direction": "N",
        "x": 0,
        "y": -1
    },
    {
        "direction": "NE",
        "x": 1,
        "y": -1
    },
    {
        "direction": "E",
        "x": 1,
        "y": 0
    },
    {
        "direction": "SE",
        "x": 1,
        "y": 1
    },
    {
        "direction": "S",
        "x": 0,
        "y": 1
    },
    {
        "direction": "SW",
        "x": -1,
        "y": 1
    },
    {
        "direction": "W",
        "x": -1,
        "y": 0
    },
    {
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
Board.prototype.placeDisk = function placeDisk(player, position) {
    if(player !== this.CELL_BLACK || player !== this.CELL_WHITE){   //better with console validate
        return false;
    }
    const cell = this.convertPosition(position);
    //console.assert(typeof player == "string", `${arguments.callee.name}: Expecting a string, got a ${typeof player}`);
    const expected = (player === this.CELL_BLACK) ? this.CELL_BLACK_HINT : this.CELL_WHITE_HINT;
    const result = (this.board[cell.y][cell.x].getValue() == expected);
    this.board[cell.y][cell.x].setValue(player);
    return result;
};

/**
 *  
 * @param {*} player
 * @param {*} cell
 */
Board.prototype.checkValidAction = function checkValidAction(player, cell) {
    const opponent = (player === this.CELL_WHITE) ? this.CELL_BLACK : this.CELL_WHITE;
    const validDirections = this.DIRECTION.map((dir) => {
        const tempX = cell.x + dir.x;
        const tempY = cell.y + dir.y;
        return (!(tempX < 0 || tempX > 7 || tempY < 0 || tempY > 7)&&this.board[cell.y + dir.y][cell.x + dir.x].getValue() === opponent) ? this.checkValidActionDirection(player, cell, dir) : false;
    });
    const result = validDirections.reduce((acc, dirBool) => acc || dirBool, false);
    return result;
};


Board.prototype.checkValidActionDirection = function checkValidActionDirection(player, cell, direction) {
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
Board.prototype.convertPosition = function convertPosition(position) {
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

Board.prototype.updateBoard = function updateBoard(player) {
    const hintCell = (player === this.CELL_BLACK) ? this.CELL_BLACK_HINT : this.CELL_WHITE_HINT;
    this.board.forEach((row, y) => {
        row.forEach((cell, x) => {
            if (this.checkValidAction(player, {x: x,y: y})) this.board[y][x].setValue(hintCell);
        });
    });
};

module.exports = Board;