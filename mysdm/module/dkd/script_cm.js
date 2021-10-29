(function () {
  if (
    !"mediaDevices" in navigator ||
    !"getUserMedia" in navigator.mediaDevices
  ) {
    alert("Camera API is not available in your browser");
    return;
  }

  // get page elements
  const video = document.querySelector("#video");
  const btnPlay = document.querySelector("#btnPlay");
  const btnPause = document.querySelector("#btnPause");
  const btnScreenshot = document.querySelector("#btnScreenshot");
  const btnChangeCamera = document.querySelector("#btnChangeCamera");
  const btnScreenshot_2 = document.querySelector("#btnScreenshot_2");
  const btnChangeCamera_2 = document.querySelector("#btnChangeCamera_2");
  const screenshotsContainer = document.querySelector("#screenshots");
  const canvas = document.querySelector("#canvas");
  const devicesSelect = document.querySelector("#devicesSelect");

  // video constraints
  const constraints = {
    video: {
      width: {
        min: 384,
        ideal: 576,
        max: 768,
      },
      height: {
        min: 216,
        ideal: 324,
        max: 432,
      },
    },
    video2: {
      width: {
        min: 128,
        ideal: 192,
        max: 256,
      },
      height: {
        min: 72,
        ideal: 108,
        max: 144,
      },
    },
  };

  // use front face camera
  let useFrontCamera = true;

  // current video stream
  let videoStream;

  // handle events
  // play
  btnPlay.addEventListener("click", function () {
    video.play();
    btnPlay.classList.add("is-hidden");
    btnPause.classList.remove("is-hidden");
  });

  // pause
  btnPause.addEventListener("click", function () {
    video.pause();
    btnPause.classList.add("is-hidden");
    btnPlay.classList.remove("is-hidden");
  });

  // take screenshot
  btnScreenshot.addEventListener("click", function () {
	
	$("#screenshots").html("");
	$("#canvas").html("");
	document.getElementById("txt_arss").value="";
	
    const img = document.createElement("img");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0);
    img.src = canvas.toDataURL("image/png");
	document.getElementById("txt_arss").value=img.src;
    screenshotsContainer.prepend(img);
	
  });

  // switch camera
  btnChangeCamera.addEventListener("click", function () {
	document.getElementById("txt_arss").value="";
    useFrontCamera = !useFrontCamera;
    initializeCamera();
  });


  // take screenshot 2
  btnScreenshot_2.addEventListener("click", function () {
	
	$("#screenshots").html("");
	$("#canvas").html("");
	document.getElementById("txt_arss").value="";
	
    const img = document.createElement("img");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0);
    img.src = canvas.toDataURL("image/png");
	document.getElementById("txt_arss").value=img.src;
    screenshotsContainer.prepend(img);
	
  });
  
  // switch camera 2
  btnChangeCamera_2.addEventListener("click", function () {
	document.getElementById("txt_arss").value="";
    useFrontCamera = !useFrontCamera;
    initializeCamera();
  });

  // stop video stream
  function stopVideoStream() {
    if (videoStream) {
      videoStream.getTracks().forEach((track) => {
        track.stop();
      });
    }
  }

  // initialize
  async function initializeCamera() {
    stopVideoStream();
    constraints.video.facingMode = useFrontCamera ? "user" : "environment";

    try {
      videoStream = await navigator.mediaDevices.getUserMedia(constraints);
      video.srcObject = videoStream;
    } catch (err) {
      alert("Could not access the camera");
    }
  }

  initializeCamera();
})();
