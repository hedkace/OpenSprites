// Generate color
var color = Please.make_color({format: "rgb"});
var color_num = Math.random();
color_num = (
    color_num < 0.3 ? 1 : (
        color_num < 0.6 ? 2 : 3
    )
)

var frame = function(data) {
    var canvas = document.getElementById("vis-canvas");
    var ctx = canvas.getContext('2d');

    canvas.setAttribute('width', window.innerWidth);
    canvas.setAttribute('height', window.innerHeight);

    ctx.fillStyle = "#000";
    ctx.fillRect(0, 0, window.innerWidth, window.innerHeight);

    ctx.lineCap = 'round';

    var lx, ly;
    ctx.beginPath();
    for (var i = 0; i < 256; i++) {
        var color_string = "rgb(" + [
            color.r + (color_num === 1 ? data[i] : 0),
            color.g + (color_num === 2 ? data[i] : 0),
            color.b + (color_num === 3 ? data[i] : 0)
        ].join(',') + ")";

        var x = window.innerWidth / 2 + data[i] * Math.cos(i / 128 * Math.PI);
        var y = window.innerHeight / 2 + data[i] * Math.sin(i / 128 * Math.PI);
        lx = lx || x;
        ly = ly || y;
        //ctx.moveTo(lx, ly);
        ctx.lineTo(x, y);
        lx = x;
        ly = y;
        //ctx.stroke();
    }

    // The last cap
    ctx.fillStyle = "rgb(" + color.r + "," + color.g + "," + color.b + ")";
    ctx.moveTo(lx, ly);
    var x = window.innerWidth / 2 + data[0] * Math.cos(0 / 128 * Math.PI);
    var y = window.innerHeight / 2 + data[0] * Math.sin(0 / 128 * Math.PI);
    ctx.lineTo(x, y);
    ctx.closePath();
    ctx.fill();
    //ctx.stroke();
};
visualizer('#myAudio', 256 * 8, frame);
