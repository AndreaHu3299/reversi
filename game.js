/* e*/
var Game = function (GameId) {
  this.playerWHite = null;
  this.playerBlack = null;
  this.id = GameId;
  this.board = null;
  this.GameState = "0 JOINED";
};

/*
 * The Game can be in a number of different states.
 */
Game.prototype.transitionStates = {};
Game.prototype.transitionStates["0 JOINED"] = 0;
Game.prototype.transitionStates["1 JOINED"] = 1;
Game.prototype.transitionStates["2 JOINED"] = 2;
Game.prototype.transitionStates["WHITE TURN"] = 3;
Game.prototype.transitionStates["BLACK TURN"] = 4;
Game.prototype.transitionStates["WHITE WIN"] = 5; //A won
Game.prototype.transitionStates["BLACK WIN"] = 6; //B won
Game.prototype.transitionStates["ABORTED"] = 7;

Game.prototype.transitionMatrix = [
  [0, 1, 0, 0, 0, 0, 0, 0], //0 JOINED
  [1, 0, 1, 0, 0, 0, 0, 0], //1 JOINED
  [0, 0, 0, 1, 0, 0, 0, 0], //2 JOINED
  [0, 0, 0, 0, 1, 1, 1, 1], //WHITE TURN
  [0, 0, 0, 1, 0, 1, 1, 1], //BLACK TURN
  [0, 0, 0, 0, 0, 0, 0, 0], //WHITE WIN
  [0, 0, 0, 0, 0, 0, 0, 0], //BLACK WIN
  [0, 0, 0, 0, 0, 0, 0, 0], //ABORTED
];


Game.prototype.isValidTransition = function (from, to) {

  console.assert(typeof from == "string", "%s: Expecting a string, got a %s", arguments.callee.name, typeof from);
  console.assert(typeof to == "string", "%s: Expecting a string, got a %s", arguments.callee.name, typeof to);
  console.assert(from in Game.prototype.transitionStates == true, "%s: Expecting %s to be a valid transition state", arguments.callee.name, from);
  console.assert(to in Game.prototype.transitionStates == true, "%s: Expecting %s to be a valid transition state", arguments.callee.name, to);


  let i, j;
  if (!(from in Game.prototype.transitionStates)) {
    return false;
  } else {
    i = Game.prototype.transitionStates[from];
  }

  if (!(to in Game.prototype.transitionStates)) {
    return false;
  } else {
    j = Game.prototype.transitionStates[to];
  }

  return (Game.prototype.transitionMatrix[i][j] > 0);
};

Game.prototype.isValidState = function (s) {
  return (s in Game.prototype.transitionStates);
};

Game.prototype.setStatus = function (w) {

  console.assert(typeof w == "string", "%s: Expecting a string, got a %s", arguments.callee.name, typeof w);

  if (Game.prototype.isValidState(w) && Game.prototype.isValidTransition(this.GameState, w)) {
    this.GameState = w;
    console.log("[STATUS] %s", this.GameState);
  } else {
    return new Error("Impossible status change from %s to %s", this.GameState, w);
  }
};

//TODO change
Game.prototype.getBoard = function () {
  return this.board;
};

Game.prototype.hasTwoConnectedPlayers = function () {
  return (this.GameState == "2 JOINED");
};

Game.prototype.addPlayer = function (p) {

  console.assert(p instanceof Object, "%s: Expecting an object (WebSocket), got a %s", arguments.callee.name, typeof p);

  if (this.GameState != "0 JOINED" && this.GameState != "1 JOINED") {
    return new Error("Invalid call to addPlayer, current state is %s", this.GameState);
  }

  /*
   * revise the Game state
   */
  var error = this.setStatus("1 JOINED");
  if (error instanceof Error) {
    this.setStatus("2 JOINED");
  }

  if (this.playerWhite == null) {
    this.playerWhite = p;
    return "White";
  } else {
    this.playerBlack = p;
    return "Black";
  }
};

module.exports = Game;