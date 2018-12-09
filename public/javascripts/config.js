/* Code shared between client and server: game setup */

(function (exports) {
    
    exports.WEB_SOCKET_URL = "ws://localhost:3000"; /* WebSocket URL */
    exports.TIMER_MAX_TIME = 10;

}(typeof exports === "undefined" ? this.Setup = {} : exports));
//if exports is undefined, we are on the client; else the server