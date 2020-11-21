
// importScripts('https://unpkg.com/dexie@2.0.3/dist/dexie.js');
importScripts('./dexie.js');
// Listen to fetch requests


const CACHE_NAME = 'SirvezApp';
const urlsToCache = [
  '/',
  '/pixie/styles.min.css/',
  '/pixie/styles.min.js/',
];

var FOLDER_NAME = 'post_requests'
var IDB_VERSION = 1
var frmData
var our_db

openDatabase()
// Install a service worker
self.addEventListener('install', event => {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        return cache.addAll(urlsToCache);
      })
  );
});

function getObjectStore (storeName, mode) {
	return our_db.transaction(storeName, mode).objectStore(storeName)
}
  
async function savePostRequests (event, payload) {
	var frmDt = {}
	var request = event.request
	for(var pair of payload.entries()) {
		var key = pair[0];
		var value =  pair[1];
		frmDt[key] = value;
	}
	var headers = {};
	// `for(... of ...)` is ES6 notation but current browsers supporting SW, support this
	// notation as well and this is the only way of retrieving all the headers.
	for (var entry of request.headers.entries()) {
		if (entry[0] === 'x-auth-token')
			headers[entry[0]] = entry[1];
	}
	//delete same post request in indexedDB 
	// var savedRequests = []
	// var req = getObjectStore(FOLDER_NAME).openCursor()
	// req.onsuccess = async function (event) {
	//   var cursor = event.target.result
	// 	if (cursor) {
	// 		savedRequests.push(cursor.value)
	// 		cursor.continue()
	// 	} else {
	// 		for (let savedRequest of savedRequests) {
	// 			var payload = savedRequest.payload
	// 			if((payload['id'] ==frmDt['id'])&&(savedRequest.url ==request.url)){
	// 				getObjectStore(FOLDER_NAME, 'readwrite').delete(savedRequest.id)
	// 				break;
	// 			}
	// 		}
			
	// 	}
	// }
	var store_request = getObjectStore(FOLDER_NAME, 'readwrite').add({
		url: request.url,
		headers: headers,
		payload: frmDt,
		method: 'POST'
	})
	store_request.onsuccess = function (evt) {
		//console.log('a new pos_ request has been added to indexedb')
	}

	store_request.onerror = function (error) {
		console.error(error)
	}
}

function openDatabase () {
// if `flask-form` does not already exist in our browser (under our site), it is created
	var indexedDBOpenRequest = indexedDB.open('flask-form', )

	indexedDBOpenRequest.onerror = function (error) {
		// errpr creatimg db
		console.error('IndexedDB error:', error)
	}


	indexedDBOpenRequest.onupgradeneeded = function () {
		// This should only execute if there's a need to create/update db.
		this.result.createObjectStore(FOLDER_NAME, { autoIncrement: true, keyPath: 'id' })
	}

	// This will execute each time the database is opened.
	indexedDBOpenRequest.onsuccess = function () {
		our_db = this.result
	}
}




self.addEventListener('fetch', function(event) {
	// We will cache all POST requests, but in the real world, you will probably filter for
	// specific URLs like if(... || event.request.url.href.match(...))
	if(!(event.request.url.indexOf('http') === 0)) return;
	if (event.request.method === "GET"){
		event.respondWith(
			caches.open(CACHE_NAME).then(function(cache) {
				if (navigator.onLine){
					return fetch(event.request).then(function(networkResponse) {
						cache.put(event.request, networkResponse.clone());
						sendPostToServer();
						return networkResponse;
					})
				}
				else{
					return cache.match(event.request)
					.then(function(response) {
						return response;
					})
				}
			})
		);
	}
	else if (event.request.clone().method === 'POST') {
		// attempt to send request normally
		event.respondWith(
			fetch(event.request.clone())
			.catch(function (error) {
				event.request.clone().formData().then(formData => {
					return savePostRequests(event, formData)
				})
			})
		);
	
	}
})

self.addEventListener('message', function (event) {
	if (event.data.hasOwnProperty('frmData')) {
	  frmData = event.data.frmData
	}
})
sending = 0
function sendPostToServer () {
	if (!navigator.onLine) return;
	if(sending==1) return;
	var savedRequests = []
	var req = getObjectStore(FOLDER_NAME).openCursor() // FOLDERNAME = 'post_requests'
	
	req.onsuccess = async function (event) {
	  var cursor = event.target.result
		if (cursor) {
			// Keep moving the cursor forward and collecting saved requests.
			savedRequests.push(cursor.value)
			cursor.continue()
		} else {
			
			sending = 1
			// At this point, we have collected all the post requests in indexedb.
			for (let savedRequest of savedRequests) {
				// send them to the server one after the other
				var requestUrl = savedRequest.url
				var payload = savedRequest.payload
				var method = savedRequest.method
				var headers = savedRequest.headers
				var formData = new FormData()
				for (var key in payload) 
					formData.append(key, payload[key])
				await fetch(requestUrl, {
					headers: headers,
					method: method,
					body: formData
				})
				// .then(function (result) {
				// 	return result.json()
				// })
				// .then(function (response){ 
				// 	console.log('fetch_rewult:',requestUrl,response)
				// })
				getObjectStore(FOLDER_NAME, 'readwrite').delete(savedRequest.id)
			}
			sending = 0
		}
	}
}
  
  
self.addEventListener('sync', function (event) {
	//if (event.tag === 'sendFormData') { // event.tag name checked here must be the same as the one used while registering sync
	  event.waitUntil(
		// Send our POST request to the server, now that the user is online
		//sendPostToServer()
		//console.log(navigator.onLine)
		)
	//}
})

// Update a service worker
self.addEventListener('activate', event => {
  const cacheWhitelist = ['SirvezApp'];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});