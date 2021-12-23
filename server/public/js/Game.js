class Connection {
    constructor() {
        this.ws = new WebSocket('ws://localhost:8080/');
        this.connected = false;
        this.ws.onopen = this.onOpen.bind(this);
        this.ws.onmessage = this.onMessage.bind(this);
        this.ws.onclose = this.onClose.bind(this);
        this.ws.onerror = this.onError.bind(this);
        this.listeners = [];
    }

    send(type, data, callback = null) {
        if (this.connected) {
            if (callback != null) {
                this.listeners.push({ type: type, callback: callback });
            }
            this.ws.send(JSON.stringify({ type, data }));
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
        const { type, data } = JSON.parse(event.data);

        // Resolve pending listeners
        for (const listener of this.listeners) {
            if (listener + '.response' == type) {
                listener.callback(data);
            }
        }
        this.listeners = this.listeners.filter(listener => listener.type + '.response' != type);
    }

    onClose() {
        this.connected = false;
        console.log('Ws close');
    }

    onError() {

    }
}

const connection = new Connection();
connection.onConnected = () => {
    connection.send('auth.login', {
        'token': 'token'
    });
};



function Game(data) {
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
    const stats = new Stats();
    stats.dom.style.top = '';
    stats.dom.style.left = '';
    stats.dom.style.right = '16px';
    stats.dom.style.bottom = '16px';
    document.body.appendChild(stats.dom);

    // Loop
    const clock = new THREE.Clock();
    function loop() {
        window.requestAnimationFrame(loop);
        stats.begin();

        const delta = clock.getDelta();

        // Rotate spawn
        spawn.rotation.x += 5 * delta;
        spawn.rotation.y += 1 * delta;

        renderer.render(scene, camera);
        stats.end();
    }
    loop();
}
