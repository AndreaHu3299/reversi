/* 
 In-memory game statistics "tracker".
 As future work, this object should be replaced by a DB backend.
*/

var gameStatus = {
  since: Date.now(),
  /* since we keep it simple and in-memory, keep track of when this object was created */
  visitors: 2,
  /* number of games initialized */
  gamesOnGoing: 3,
  /* number of games aborted */
  playersOnline: 4 /* number of games successfully completed */
};

module.exports = gameStatus;