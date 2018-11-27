var game=function(gameID){
    this.player1=null;
    this.player2=null;
    this.id=gameID;
    this.gameState="0 JOINT" //"1" means player1 won, "2" means player2 won, "ABORTED" means the game was aborted
}
