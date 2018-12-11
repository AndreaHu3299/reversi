(function (exports) {

    /*
     * Server to client: abort game (e.g. if second player exited the game)
     */
    exports.O_GAME_ABORTED = {
        "type": "GAME-ABORTED"
    };
    exports.S_GAME_ABORTED = JSON.stringify(exports.O_GAME_ABORTED);

    /*
     * Server to client: player's turn (can be WHITE or BLACK)
     */
    exports.T_PLAYER_TURN = "PLAYER-TURN";
    exports.O_PLAYER_TURN = {
        "type": exports.T_PLAYER_TURN,
        "data": null
    };

    /*
     * Server to client: set as player white
     */
    exports.T_PLAYER_TYPE = "PLAYER-TYPE";
    exports.O_PLAYER_WHITE = {
        "type": exports.T_PLAYER_TYPE,
        "data": "WHITE"
    };
    exports.S_PLAYER_WHITE = JSON.stringify(exports.O_PLAYER_WHITE);

    /*
     * Server to client: set as player black
     */
    exports.O_PLAYER_BLACK = {
        "type": exports.T_PLAYER_TYPE,
        "data": "BLACK"
    };
    exports.S_PLAYER_BLACK = JSON.stringify(exports.O_PLAYER_BLACK);

    /*
     * Server to client: start game and remove waiting screen
     */
    exports.T_GAME_STARTED = "GAME-STARTED";
    exports.O_GAME_STARTED = {
        "type": exports.T_GAME_STARTED
    };
    exports.S_GAME_STARTED = JSON.stringify(exports.O_GAME_STARTED);

    /*
     * Server to players: their respective board state to be visualized
     */
    exports.T_BOARD_STATE = "BOARD-STATE";
    exports.O_BOARD_STATE = {
        "type": exports.T_BOARD_STATE,
        "data": null
    };

    /*
     * Server to clients: game over with result won/loss/draw
     */
    exports.T_GAME_OVER = "GAME-OVER";
    exports.O_GAME_OVER = {
        "type": exports.T_GAME_OVER,
        "data": null
    };

    /*
     * Player to server: sends the coordinates of the cell where to place the new disk 
     */
    exports.T_PLACE_A_DISK = "PLACE-A-DISK";
    exports.O_PLACE_A_DISK = {
        "type": exports.T_PLACE_A_DISK,
        "data": null
    };

    /*
     * Player to server: sends the timeout message if the opponent ran out of time
     */
    exports.T_TIMEOUT = "TIMEOUT";
    exports.O_TIMEOUT = {
        "type": exports.T_TIMEOUT,
        "data": null
    };

}(typeof exports === "undefined" ? this.Messages = {} : exports));
// If exports is undefined, we are on the client; else the server