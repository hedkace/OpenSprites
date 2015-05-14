/*console.log(
    '--------------------------------------\n' +
    'VISUALIZER BASE BY LIAM4\n' +
    'https://github.com/liam4/\n' +
    'https://scratch.mit.edu/users/liam48D/\n' +
    '\n' +
    '* Might only work in Google Chrome!  *\n' +
    '\n' +
    'Feel free to use this file in your own\n' +
    'projects, so long as you keep this li-\n' +
    'cense here. :)\n' +
    '--------------------------------------'
);*/

function visualizer(audio, fftSize, f) {
    VISUALIZER_TEMP_DATA = {
        audio: audio,
        fftSize: fftSize,
        frame: f
    };
    window.onload = function() {
        // Load temporary saved data
        var audio = document.querySelector(VISUALIZER_TEMP_DATA.audio);
        var fftSize = VISUALIZER_TEMP_DATA.fftSize;
        var f = VISUALIZER_TEMP_DATA.frame;
        delete VISUALIZER_TEMP_DATA;

        var ctx = new AudioContext();
        var audioSrc = ctx.createMediaElementSource(audio);
        var analyser = ctx.createAnalyser();
        audioSrc.connect(analyser);
        audioSrc.connect(ctx.destination);
        analyser.fftSize = fftSize;

        var frequencyData = new Uint8Array(analyser.frequencyBinCount);

        var renderFrame = function() {
            requestAnimationFrame(renderFrame);

            // Update data in frequencyData
            analyser.getByteFrequencyData(frequencyData);

            f(frequencyData);
        }

        audio.play();

        renderFrame();
    };
};