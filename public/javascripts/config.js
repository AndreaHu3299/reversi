/* Code shared between client and server: game setup */

(function (exports) {
    
    exports.WEB_SOCKET_URL = "ws://145.94.174.123:3000"; /* WebSocket URL */
    exports.TIMER_MAX_TIME = 30;
    exports.COOKIE_SECRET = "COOOOOKIESSSSareAlie!!!!!!!1!11!!!!!1";

}(typeof exports === "undefined" ? this.Setup = {} : exports));
//if exports is undefined, we are on the client; else the server