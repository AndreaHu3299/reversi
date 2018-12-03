(function (exports) {

    /*
     * Client to server: game is complete, the winner is ...
     */
    exports.T_GAME_WON_BY = "GAME-WON-BY";
    exports.O_GAME_WON_BY = {
        "type": exports.T_GAME_WON_BY,
        "data": null
    };

    /*
     * Server to client: abort game (e.g. if second player exited the game)
     */
    exports.O_GAME_ABORTED = {
        "type": "GAME-ABORTED"
    };
    exports.S_GAME_ABORTED = JSON.stringify(exports.O_GAME_ABORTED);

    /*
     * Server to client: choose target word
     */
    exports.O_TURN_WHITE = {
        "type": "TURN-WHITE"
    };
    exports.S_TURN_WHITE = JSON.stringify(exports.O_TURN_WHITE);

    exports.O_TURN_BLACK = {
        "type": "TURN-BLACK"
    };
    exports.S_TURN_BLACK = JSON.stringify(exports.O_TURN_BLACK);

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
     * Player to server: send 
     */
    exports.T_PLACE_A_DISK = "PLACE_A_DISK";
    exports.O_PLACE_A_DISK = {
        "type": exports.T_PLACE_A_DISK,
        "data": null
    };
    // Exports.S_MAKE_A_GUESS does not exist, as data needs to be set

    /*
     * Server to Player A & B: game over with result won/loss
     */
    exports.T_GAME_OVER = "GAME-OVER";
    exports.O_GAME_OVER = {
        "type": exports.T_GAME_OVER,
        "data": null
    };

}(typeof exports === "undefined" ? this.Messages = {} : exports));
// If exports is undefined, we are on the client; else the server