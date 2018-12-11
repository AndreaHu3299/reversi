/* ESLint global variables information */
/* global Setup */
var Status = {};
Status["gameWon"] = "Congratulations! You won!" + Status["playAgain"];
Status["gameLost"] = "Game over. You lost!" + Status["playAgain"];
Status["gameDraw"] = "Game over. You draw!" + Status["playAgain"];
Status["playAgain"] = " <a href='/play'>Play again!</a>";
Status["turnWhite"] = "White disk's turn";
Status["turnBlack"] = "Black disk's turn";
Status["aborted"] = "Your gaming partner is no longer available, game aborted. " + Status["playAgain"];
