const video = document.querySelector('[data-stream]');
if (video) {
    const source = video.dataset.stream;
    const notice = document.querySelector('[data-player-error]');
    const fail = () => { notice.hidden = false; };
    video.addEventListener('error', fail);
    const isHls = /\.m3u8(?:[?#]|$)/i.test(source);
    if (isHls && video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = source;
    } else if (isHls) {
        import('hls.js').then(({ default: Hls }) => {
            if (!Hls.isSupported()) return fail();
            const player = new Hls();
            player.loadSource(source);
            player.attachMedia(video);
            player.on(Hls.Events.ERROR, (_event, data) => { if (data.fatal) fail(); });
            window.addEventListener('pagehide', () => player.destroy(), { once: true });
        }).catch(fail);
    } else {
        video.src = source;
    }
}
