/*
 * Copyright 2016 Google Inc. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
var vrView;
var startImage;
var imagePath;
var isStereo;
var hotSpots;
var hotSpotsLink;
var baseHotSpotsLink;
var initialHotSpotsLink;
// All the scenes for the experience
/*var scenes = {
  first: {
    image: imagePath,
    preview: imagePath
  }
};*/

function setSourceParams() {
    startImage = drupalSettings.vr_base.start_image;
    imagePath = drupalSettings.vr_base.source;
    isStereo = drupalSettings.vr_base.is_stereo;
    //hotSpots = drupalSettings.vr_base.hot_spots;
    hotSpots = {
        hotspot: {
            pitch: 0,
            yaw: 10,
            radius: 0.05,
            distance: 1
        }
    };
    initialHotSpotsLink = document.getElementById('modal-button').getAttribute('href');
    baseHotSpotsLink = initialHotSpotsLink.replace('/0/0', '');
}

function onLoad() {
  setSourceParams();
  vrView = new VRView.Player('#vrview', {
    width: '100%',
    height: 480,
    image: startImage,
    preview: startImage,
    is_stereo: false,
    is_autopan_off: true
  });
  vrView.on('ready', loadScene);
  vrView.on('modechange', onModeChange);
  vrView.on('error', onVRViewError);
  vrView.on('click', onVRViewClick);
  vrView.on('getposition', onVRViewPosition);
}

function loadScene() {
    console.log('loadScene', 'load scene');
    vrView.setContent({
        image: imagePath,
        preview: imagePath,
        is_stereo: isStereo,
        is_autopan_off: true
    });
    // Add all the hotspots for the scene
    var sceneHotSpots = Object.keys(hotSpots);
    for (var i = 0; i < sceneHotSpots.length; i++) {
        var hotSpotKey = sceneHotSpots[i];
        var hotSpot = hotSpots[hotSpotKey];
        vrView.addHotspot(hotSpotKey, {
            pitch: hotSpot.pitch,
            yaw: hotSpot.yaw,
            radius: hotSpot.radius,
            distance: hotSpot.distance
        });
    }
}

function onVRViewClick(e) {
	vrView.getPosition();
	console.log('clicked:');
}

function onVRViewPosition(e) {
	var pitch = e.Pitch;
	var yaw = e.Yaw;
	console.log('pitch:' + pitch + ', yaw'+ yaw);
	document.getElementById('pitch-value').innerHTML = pitch.toString();
    document.getElementById('yaw-value').innerHTML = yaw.toString();
    hotSpotsLink = baseHotSpotsLink + '/'+yaw.toString()+'/'+pitch.toString()+"?_wrapper_format=drupal_modal";
    for(var i = 0; i < Drupal.ajax.instances.length; i++) {
        if(Drupal.ajax.instances[i].element.id === 'modal-button') {
            Drupal.ajax.instances[i].options.url = hotSpotsLink;
            alert(Drupal.ajax.instances[i].options.url);
        }
    }
}

function onVRViewReady(e) {
  console.log('onVRViewReady');

  // Create the carousel links
  var carouselItems = document.querySelectorAll('ul.carousel li a');
  for (var i = 0; i < carouselItems.length; i++) {
    var item = carouselItems[i];
    item.disabled = false;

    item.addEventListener('click', function(event) {
      event.preventDefault();
      loadScene(event.target.parentNode.getAttribute('href').substring(1));
    });
  }

  loadScene('petra');
}

function onModeChange(e) {
  console.log('onModeChange', e.mode);
}

function onVRViewError(e) {
  console.log('Error! %s', e.message);
}

window.addEventListener('load', onLoad);
