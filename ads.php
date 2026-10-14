<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Video with Mid-Roll Ads</title>
  <style>
    video { width: 100%; max-width: 720px; margin: 20px auto; display: block; }
  </style>
</head>
<body>

<video id="mainVideo" controls>
  <source src="videos/my_video.mp4" type="video/mp4">
  Your browser does not support HTML5 video.
</video>

<script>
const video = document.getElementById('mainVideo');
const adUrls = [
  'https://www.w3schools.com/html/mov_bbb.mp4',
  'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4'
];

// Use short times for testing, e.g., [5, 10, 15]
const adTimes = [5, 10, 15];
let adPlayed = [];

const playAd = (adUrl, resumeTime) => {
  const originalSrc = video.currentSrc;

  const adVideo = document.createElement('video');
  adVideo.src = adUrl;
  adVideo.autoplay = true;
  adVideo.muted = true; // Required for autoplay in most browsers
  adVideo.controls = true;
  adVideo.style.width = video.clientWidth + 'px';
  adVideo.style.display = 'block';

  video.parentNode.insertBefore(adVideo, video);
  video.style.display = 'none';

  adVideo.addEventListener('ended', () => {
    adVideo.remove();
    video.style.display = 'block';
    video.src = originalSrc;
    video.currentTime = resumeTime + 1;
    video.play();
  });
};

video.addEventListener('timeupdate', () => {
  const currentTime = Math.floor(video.currentTime);
  adTimes.forEach((time, index) => {
    if (currentTime === time && !adPlayed.includes(time)) {
      adPlayed.push(time);
      video.pause();
      playAd(adUrls[index % adUrls.length], currentTime);
    }
  });
});
</script>

</body>
</html>
