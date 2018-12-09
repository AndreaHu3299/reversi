var express = require("express");
var router = express.Router();

/* GET splash screen. */
router.get("/", function (req, res) {
  res.sendFile("splash.html", {root: "./public"});
});

/* Pressing the 'PLAY' button, returns this page */
router.get("/play", function(req, res) {
  res.sendFile("game.html", {root: "./public/stylesheets"});
});

module.exports = router;