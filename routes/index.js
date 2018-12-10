var express = require("express");
var router = express.Router();
const gameStatus = require("../statTracker");

/* GET and render splash screen. */
router.get("/", (req, res) => {
    let accessNum = (req.signedCookies.accessNum === undefined) ? 0 : req.signedCookies.accessNum;
    res.cookie("accessNum", ++accessNum, {
        signed: true
    });
    //example of data to render; here gameStatus is an object holding this information
    res.render("splash.ejs", {
        visitors: gameStatus.visitors,
        gamesOnGoing: gameStatus.gamesOnGoing,
        playerOnline: gameStatus.playersOnline
    });
});

/* Pressing the 'PLAY' button, returns this page */
router.get("/play", function (req, res) {
    res.sendFile("game.html", {
        root: "./public"
    });
});

module.exports = router;