class Connection {
    constructor(url, reconnectTimeout) {
        this.url = url;
        this.reconnectTimeout = reconnectTimeout;
        this.connected = false;
    }

    connect() {
        this.ws = new WebSocket(this.url);
        this.ws.onopen = this.onOpen.bind(this);
        this.ws.onmessage = this.onMessage.bind(this);
        this.ws.onclose = this.onClose.bind(this);
        this.ws.onerror = this.onError.bind(this);
        this.listeners = [];
    }

    send(type, data, callback = null) {
        if (this.connected) {
            const id = Date.now();
            if (callback != null) {
                this.listeners.push({ id: id, type: type, callback: callback });
            }
            this.ws.send(JSON.stringify({ id: id, type, data }));
        }
    }

    onOpen() {
        this.connected = true;
        console.log('Ws open');
        if (this.onConnected != undefined) {
            this.onConnected();
        }
    }

    onMessage(event) {
        const { id, type, data } = JSON.parse(event.data);

        // Resolve pending listeners
        for (const listener of this.listeners) {
            if (listener.id == id) {
                listener.callback(data);
            }
        }
        this.listeners = this.listeners.filter(listener => listener.type + '.response' != type);
    }

    onClose() {
        this.connected = false;
        console.log('Ws close');
        setTimeout(this.connect.bind(this), this.reconnectTimeout);
    }

    onError() {

    }
}

function Game(data) {
    // Create connection
    const connection = new Connection(data.WEBSOCKETS_URL, data.WEBSOCKETS_RECONNECT_TIMEOUT);
    connection.connect();
    connection.onConnected = () => {
        connection.send('auth.login', {
            'token': data.authToken
        }, message => {
            if (message.success) {
                connection.send('world.connect', {
                    'world_id': data.worldId
                }, message => {
                    if (message.success) {
                        console.log(message.world);
                    } else {
                        alert(JSON.stringify(message.errors));
                    }
                });
            } else {
                alert(JSON.stringify(message.errors));
            }
        });
    };

    // Scene and camera
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, 0, 0.1, 1000);
    camera.position.z = 5;

    // Renderer
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('game-canvas') });
    function resize() {
        const height = window.innerHeight - document.querySelector('.navbar').offsetHeight;
        camera.aspect = window.innerWidth / height;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, height);
    }
    window.addEventListener('resize', resize);
    resize();

    // Spawn cube
    const spawnGeometry = new THREE.BoxGeometry(1, 1, 1);
    const spawnMaterial = new THREE.MeshNormalMaterial();
    const spawn = new THREE.Mesh(spawnGeometry, spawnMaterial);
    scene.add(spawn);

    // Stats
    let stats;
    if ('Stats' in window) {
        stats = new Stats();
        stats.dom.style.top = '';
        stats.dom.style.left = '';
        stats.dom.style.right = '16px';
        stats.dom.style.bottom = '16px';
        document.body.appendChild(stats.dom);
    }

    // Loop
    const clock = new THREE.Clock();
    function loop() {
        window.requestAnimationFrame(loop);
        if ('Stats' in window) stats.begin();

        const delta = clock.getDelta();

        // Rotate spawn
        spawn.rotation.x += 5 * delta;
        spawn.rotation.y += 1 * delta;

        renderer.render(scene, camera);
        if ('Stats' in window) stats.end();
    }
    loop();
}
