/*console.log(
    '--------------------------------------\n' +
    'BARS VISUALIZER BY LIAM4\n' +
    'https://github.com/liam4/\n' +
    'https://scratch.mit.edu/users/liam48D/\n' +
    '\n' +
    '* Might only work in Google Chrome!  *\n' +
    '\n' +
    'You may create your own visualizers\n' +
    'based on this, but please don\'t claim\n' +
    'this code / file as your own.\n' +
    '\n' +
    '(This file requres Please.js, which\n' +
    'created by Jordan Checkman in 2014.\n' +
    'Credits to him. :))\n' +
    '--------------------------------------'
);*/

// Generate color
var color = Please.make_color({format: "rgb"});
var color_num = Math.random();
color_num = (
    color_num < 0.3 ? 1 : (
        color_num < 0.6 ? 2 : 3
    )
)

var drawFrame = function(frequencyData) {
    // The main frame function. This is called constantly
    // as the audio sound plays.

    var canvas = document.getElementById('vis-canvas');
    canvas.setAttribute("width", innerWidth);
    canvas.setAttribute("height", innerHeight);

    var ctx = canvas.getContext('2d');
    for (var i = 0; i < 256; i++) {
        var e = document.getElementById('bar' + i);
        var color_string = "rgb(" + [
            color.r + (color_num === 1 ? frequencyData[i] : 0),
            color.g + (color_num === 2 ? frequencyData[i] : 0),
            color.b + (color_num === 3 ? frequencyData[i] : 0)
        ].join(',') + ")";
        ctx.fillStyle = color_string;
        ctx.fillRect(innerWidth / 255 * i, innerHeight / 2, innerWidth / 255, frequencyData[i] > 4 ? frequencyData[i] : 4);
        ctx.fillRect(innerWidth / 255 * i, innerHeight / 2, innerWidth / 255, -(frequencyData[i] > 4 ? frequencyData[i] : 4));
    }
};

visualizer(
    "#myAudio",
    256 * 8,
    drawFrame
);
