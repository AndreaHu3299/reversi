class Cell {
    constructor (x, y, value) {
        this.x = x;
        this.y = y;
        this.value = value;
    }

    getX () {
        return this.x;
    }

    getY () {
        return this.y;
    }

    getValue () {
        return this.value;
    }

    setValue (newValue) {
        this.value = newValue;
    }
}

const board = (function () {
    const CELL_EMPTY = 0;
    const CELL_WHITE = 1;
    const CELL_BLACK = 2;

    const DIRECTION = [ // Defines different directions based on polar coordinates int the format of (y, x)
        {
            "direction": 'N',
            "x": 0,
            "y": -1
        },
        {
            "direction": 'NE',
            "x": 1,
            "y": -1
        },
        {
            "direction": 'E',
            "x": 1,
            "y": 0
        },
        {
            "direction": 'SE',
            "x": 1,
            "y": 1
        },
        {
            "direction": 'S',
            "x": 0,
            "y": 1
        },
        {
            "direction": 'SW',
            "x": -1,
            "y": 1
        },
        {
            "direction": 'W',
            "x": -1,
            "y": 0
        },
        {
            "direction": 'NW',
            "x": -1,
            "y": -1
        }
    ];

    // Initialize 8x8 matrix of Cells
    const board = Array(...Array(8)).map((a, y) => Array(...Array(8)).map((b, x) => new Cell(x, y, CELL_EMPTY)));

    // Initialization of the first 4 disks
    board[3][3].setValue(CELL_WHITE);
    board[3][4].setValue(CELL_BLACK);
    board[4][3].setValue(CELL_BLACK);
    board[4][4].setValue(CELL_WHITE);

    /**
     * Given a disk color and the position to place it to, it checks if it's possible for that disk to be placed there
     * and if possible, it gets placed there.
     *
     * @param {*} player either CELL_BLACK or CELL_EMPTY
     * @param {String} position a two character string that contains the targeted cell (e.g.: f2)
     * Returns
     * @returns {boolean} return true if it was a valid move, false if it wasn't;
     */
    function placeDisk (player, position) {
        const cell = convertPosition(position);

        if (board[cell.y][cell.x].getValue() != CELL_EMPTY) {
 return false;
}

return checkValidAction(player, cell);
    }

    /**
     *
     * @param {*} player
     * @param {*} cell
     */
    function checkValidAction (player, cell) {
        const expected = player === CELL_WHITE ? CELL_BLACK : CELL_WHITE;
        const validDirections = DIRECTION.map((dir) => board[cell.y + dir.y][cell.x + dir.y].getValue() === expected ? checkValidActionDirection(player, cell, dir) : false);
        const result = validDirections.reduce((acc, dirBool) => acc || dirBool, false);


return result;
    }


    function checkValidActionDirection (player, cell, direction) {
        cell.x += direction.x * 2;
        cell.y += direction.y * 2;
        for (let x = cell.x, y = cell.y; x >= 0 && x < 8 && y >= 0 && y < 8;) {
            const disk = board[y][x].getValue();

            if (disk === player) {
                return true;
            } else if (disk === CELL_EMPTY) {
                break;
            }
        }

return false;
    }

    /**
     * Converts a string format of a cell to its indexes x and y
     * @param {*} position a string representation of a cell on the board of type (a1)
     * @returns {Object} returns an object containing values x and y of the given position
     */
    function convertPosition (position) {
        // Position = position.toLowerCase();
        x = position.charCodeAt(0) - 97; // 97 = ASCII(a)
        y = position.charAt(1) - 1; // 48 = ASCII(0)

return {
            x,
            y
        };
    }

    return {
        placeDisk,
        board, // TODO REMOVE! Testing purposes
        convertPosition, // TODO REMOVE! Testing purposes
        checkValidAction, // TODO REMOVE! Testing purposes
        checkValidActionDirection, // TODO REMOVE! Testing purposes
        DIRECTION // TODO REMOVE! Testing purposes
    };
}());

$(() => {
    $('.cell').click((e) => {
        board.placeDisk(1, 'd6');
    });
});