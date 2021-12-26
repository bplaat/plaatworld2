class Connection {
    constructor(url, reconnectTimeout) {
        this.url = url;
        this.reconnectTimeout = reconnectTimeout;
        this.connected = false;
    }

    connect() {
        this.ws = new WebSocket(this.url);
        this.ws.onopen = this.onOpen.bind(this);
        this.ws.onmessage = this._onMessage.bind(this);
        this.ws.onclose = this.onClose.bind(this);
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
        console.log('Connected to server');
        if (this.onConnected != undefined) {
            this.onConnected();
        }
    }

    _onMessage(event) {
        const { id, type, data } = JSON.parse(event.data);

        for (const listener of this.listeners) {
            if (listener.id == id) {
                listener.callback(data);
            }
        }
        this.listeners = this.listeners.filter(listener => listener.type + '.response' != type);

        this.onMessage(id, type, data);
    }

    onClose() {
        this.connected = false;
        console.log('Disconnected to server');
        setTimeout(this.connect.bind(this), this.reconnectTimeout);
    }
}

let game;

function Game(config) {
    let user, world, groupObjects, textures, taunts, renderer, keys = {}, velocity = new THREE.Vector3(), canJump = true,
        stats, scene, clock, serverPosition, serverRotation, sendMoveTimeout = Date.now(), camera, sprites = [],
        players = new THREE.Group(), meshes = new THREE.Group();

    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);

    const materials = {};
    function createMaterial(object) {
        const textureId = object.texture_id + '@' + object.texture_repeat_x + 'x' + object.texture_repeat_y;
        if (materials[textureId] == undefined) {
            const texture = textures.find(texture => texture.id == object.texture_id);
            materials[textureId] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + texture.image),
                transparent: texture.transparent,
                side: THREE.DoubleSide
            });
            if (object.texture_repeat_x != 1) {
                materials[textureId].map.repeat.x = object.texture_repeat_x;
                materials[textureId].map.wrapS = THREE.RepeatWrapping;
            }
            if (object.texture_repeat_y != 1) {
                materials[textureId].map.repeat.y = object.texture_repeat_y;
                materials[textureId].map.wrapT = THREE.RepeatWrapping;
            }
        }
        return materials[textureId];
    }

    function createMesh(object) {
        let mesh;
        if (object.type == config.OBJECT_TYPE_GROUP) {
            mesh = new THREE.Group();
            const childObjects = groupObjects.find(otherObject => otherObject.id == object.id).objects;
            for (const childObject of childObjects) {
                const child = createMesh(childObject);
                child.position.set(childObject.pivot.position_x, childObject.pivot.position_y + (childObject.type == config.OBJECT_TYPE_SPHERE ? childObject.height : childObject.height / 2), childObject.pivot.position_z);
                child.rotation.set(childObject.pivot.rotation_x, childObject.pivot.rotation_y, childObject.pivot.rotation_z);
                mesh.add(child);
            }
        }
        if (object.type == config.OBJECT_TYPE_SPRITE || object.type == config.OBJECT_TYPE_FIXED_SPRITE) {
            mesh = new THREE.Mesh(planeGeometry, createMaterial(object));
            mesh.scale.set(object.width, object.height, 1);
            if (object.type == config.OBJECT_TYPE_SPRITE) {
                sprites.push(mesh);
            }
        }
        if (object.type == config.OBJECT_TYPE_CUBE) {
            mesh = new THREE.Mesh(boxGeometry, createMaterial(object));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == config.OBJECT_TYPE_CYLINDER) {
            mesh = new THREE.Mesh(cylinderGeometry, createMaterial(object));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == config.OBJECT_TYPE_SPHERE) {
            mesh = new THREE.Mesh(sphereGeometry, createMaterial(object));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == config.OBJECT_TYPE_PYRAMID) {
            mesh = new THREE.Mesh(new THREE.CylinderGeometry(0, Math.min(object.width, object.depth), object.height, 4), createMaterial(object));
        }
        return mesh;
    }

    game = new Vue({
        el: '#game',

        data: {
            connection: new Connection(config.WEBSOCKETS_URL, config.WEBSOCKETS_RECONNECT_TIMEOUT),
            pointerlock: false,
            worldLoaded: false,
            users: [],
            chatMessage: '',
            chats: []
        },

        mounted() {
            this.initRenderer();
            this.connect();
            clock = new THREE.Clock();
            window.requestAnimationFrame(this.renderLoop.bind(this));
        },

        computed: {
            sortedUsers() {
                return this.users.sort((a, b) => {
                    return a.username.localeCompare(b.username);
                });
            }
        },

        methods: {
            connect() {
                // Connection handlers
                this.connection.onConnected = () => {
                    this.connection.send('auth.login', { 'token': config.authToken }, data => {
                        if (data.success) {
                            user = data.user;
                            this.connection.send('world.connect', { 'world_id': config.worldId }, data => {
                                if (data.success) {
                                    this.worldLoaded = true;
                                    world = data.world;
                                    groupObjects = data.groupObjects;
                                    textures = data.textures;
                                    taunts = data.taunts;
                                    this.users = [];
                                    this.chats = [];
                                    players.clear();

                                    // Load world sky
                                    const skyTextureData = textures.find(texture => texture.id == world.sky_texture_id);
                                    if (skyTextureData != null) {
                                        const skyTexture = new THREE.TextureLoader().load('/storage/textures/' + skyTextureData.image, () => {
                                            const rt = new THREE.WebGLCubeRenderTarget(skyTexture.image.height);
                                            rt.fromEquirectangularTexture(renderer, skyTexture);
                                            scene.background = rt.texture;
                                        });
                                    }

                                    // Create ground
                                    const grassTexture = textures.find(texture => texture.name == 'Grass');
                                    const groundMaterial = new THREE.MeshBasicMaterial({
                                        map: new THREE.TextureLoader().load('/storage/textures/' + grassTexture.image),
                                        side: THREE.DoubleSide
                                    });
                                    groundMaterial.map.repeat.set(world.width / 5, world.height / 5);
                                    groundMaterial.map.wrapS = THREE.RepeatWrapping;
                                    groundMaterial.map.wrapT = THREE.RepeatWrapping;
                                    const ground = new THREE.Mesh(planeGeometry, groundMaterial);
                                    ground.scale.x = world.width;
                                    ground.scale.y = world.height;
                                    ground.rotation.x = -Math.PI / 2;
                                    ground.position.y = -0.01;
                                    scene.add(ground);

                                    // Create world objects
                                    this.createObjects();
                                } else {
                                    alert(JSON.stringify(data.errors));
                                }
                            });
                        } else {
                            alert(JSON.stringify(data.errors));
                        }
                    });
                };

                this.connection.onMessage = (id, type, data) => {
                    // console.log(id, type, data);

                    if (type == 'user.connect') {
                        data.user.position = { x: data.position.x, y: data.position.y, z: data.position.z };
                        data.user.rotation = { x: data.rotation.x, y: data.rotation.y, z: data.rotation.z };
                        this.users.push(data.user);

                        // Move camera to right position
                        if (data.user.id == config.userId) {
                            camera.position.set(data.position.x, data.position.y + config.PLAYER_HEIGHT, data.position.z);
                            camera.rotation.set(data.rotation.x, data.rotation.y, data.rotation.z);
                            serverPosition = { x: data.position.x, y: data.position.y, z: data.position.z };
                            serverRotation = { x: data.rotation.x, y: data.rotation.y, z: data.rotation.z };
                        }
                        // Create player mesh
                        else {
                            const playerMesh = new THREE.Group();
                            playerMesh.userData = data.user;
                            playerMesh.position.set(data.position.x, data.position.y + config.PLAYER_HEIGHT / 2, data.position.z);
                            playerMesh.rotation.y = data.rotation.y;
                            players.add(playerMesh);

                            const playerHeadMesh = new THREE.Mesh(boxGeometry, new THREE.MeshBasicMaterial({ color: 0xff0000 }));
                            playerHeadMesh.position.y = config.PLAYER_HEIGHT - 1;
                            playerHeadMesh.rotation.x = data.rotation.y;
                            playerHeadMesh.rotation.z = data.rotation.z;
                            playerHeadMesh.scale.set(0.75, 0.75, 0.75);
                            playerMesh.add(playerHeadMesh);

                            const playerBodyMesh = new THREE.Mesh(boxGeometry, new THREE.MeshBasicMaterial({ color: 0xffffff }));
                            playerBodyMesh.scale.y = config.PLAYER_HEIGHT - 0.75;
                            playerMesh.add(playerBodyMesh);
                        }
                    }

                    if (type == 'user.disconnect') {
                        const user = this.users.find(user => user.id == data.user_id);

                        // Remove player mesh
                        if (user.id != config.userId) {
                            for (const player of players.children) {
                                if (player.userData.id == user.id) {
                                    player.removeFromParent();
                                    break;
                                }
                            }
                        }

                        this.users = this.users.filter(user => user.id != data.user_id);
                    }

                    if (type == 'user.move') {
                        const user = this.users.find(user => user.id == data.user_id);
                        user.position.x = data.position.x;
                        user.position.y = data.position.y;
                        user.position.z = data.position.z;
                        user.rotation.x = data.rotation.x;
                        user.rotation.y = data.rotation.y;
                        user.rotation.z = data.rotation.z;

                        // Move player mesh
                        if (user.id != config.userId) {
                            for (const player of players.children) {
                                if (player.userData.id == user.id) {
                                    new TWEEN.Tween(player.position)
                                        .to({ x: user.position.x, y: user.position.y + config.PLAYER_HEIGHT / 2, z: user.position.z }, config.PLAYER_MOVE_SEND_TIMEOUT * 0.67)
                                        .easing(TWEEN.Easing.Quadratic.InOut)
                                        .start();
                                    new TWEEN.Tween(player.rotation)
                                        .to({ x: 0, y: user.rotation.y, z: 0 }, config.PLAYER_MOVE_SEND_TIMEOUT * 0.67)
                                        .easing(TWEEN.Easing.Quadratic.InOut)
                                        .start();
                                    new TWEEN.Tween(player.children[0].rotation)
                                        .to({ x: user.rotation.x, y: 0, z: user.rotation.z }, config.PLAYER_MOVE_SEND_TIMEOUT * 0.67)
                                        .easing(TWEEN.Easing.Quadratic.InOut)
                                        .start();
                                    break;
                                }
                            }
                        }
                    }

                    if (type == 'user.chat') {
                        data.chat.user = this.users.find(user => user.id == data.user_id);
                        this.handleChat(data.chat);
                    }
                };

                this.connection.connect();
            },

            initRenderer() {
                // Renderer
                renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('game-canvas') });
                window.addEventListener('resize', this.rendererResize.bind(this));
                window.addEventListener('mousemove', this.rendererMousemove.bind(this));
                renderer.domElement.addEventListener('click', this.rendererClick.bind(this));
                window.addEventListener('contextmenu', event => event.preventDefault());
                document.addEventListener('pointerlockchange', this.rendererPointerlockChange.bind(this));
                window.addEventListener('keydown', this.rendererKeydown.bind(this));
                window.addEventListener('keyup', this.rendererKeyup.bind(this));

                // Scene
                scene = new THREE.Scene();
                scene.background = new THREE.Color(getComputedStyle(document.querySelector('.has-navbar-fixed-top')).backgroundColor);
                scene.add(meshes);
                scene.add(players);

                // Camera
                camera = new THREE.PerspectiveCamera(75, 0, 0.1, 1000);

                // Stats
                if ('Stats' in window) {
                    stats = new Stats();
                    stats.dom.style.top = '';
                    stats.dom.style.left = '';
                    stats.dom.style.right = '16px';
                    stats.dom.style.bottom = '16px';
                    document.body.appendChild(stats.dom);
                }
                this.rendererResize();
            },

            rendererResize() {
                const height = window.innerHeight - document.querySelector('.navbar').offsetHeight;
                camera.aspect = window.innerWidth / height;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, height);
            },

            rendererMousemove(event) {
                if (this.pointerlock) {
                    const rotateSensitivity = 0.004;
                    const euler = new THREE.Euler(0, 0, 0, 'YXZ');
                    euler.setFromQuaternion(camera.quaternion);
                    euler.y -= event.movementX * rotateSensitivity;
                    euler.x -= event.movementY * rotateSensitivity;
                    euler.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, euler.x));
                    camera.quaternion.setFromEuler(euler);
                }
            },

            rendererClick() {
                if (!this.pointerlock) {
                    renderer.domElement.requestPointerLock();
                }
            },

            rendererPointerlockChange() {
                this.pointerlock = document.pointerLockElement == renderer.domElement;
            },

            rendererKeydown(event) {
                const key = event.key.toLowerCase();
                const chatInput = document.getElementById('chat-input');

                if (key == 't' || key == 'enter') {
                    if (chatInput.value == '' && chatInput == document.activeElement) {
                        chatInput.blur();
                    }
                }

                if (event.target != document.body) return;
                keys[key] = true;

                if (key == 't' || key == 'enter') {
                    if (chatInput != document.activeElement) {
                        keys = {};
                        chatInput.focus();
                    }
                }

                if (key == ' ' && canJump) {
                    canJump = false;
                    velocity.y += config.PLAYER_WEIGHT * 1.5;
                }
            },

            rendererKeyup(event) {
                if (event.target != document.body) return;
                const key = event.key.toLowerCase();
                keys[key] = false;
            },

            update(delta) {
                // Camera position controls
                if (world != undefined) {
                    velocity.z -= velocity.z * 10 * delta;
                    velocity.x -= velocity.x * 10 * delta;
                    velocity.y -= world.gravity * config.PLAYER_WEIGHT * delta;

                    if (this.pointerlock) {
                        if (keys['w'] || keys['arrowup']) velocity.z -= config.PLAYER_SPEED * delta;
                        if (keys['s'] || keys['arrowdown']) velocity.z += config.PLAYER_SPEED * delta;
                        if (keys['a'] || keys['arrowleft']) velocity.x -= config.PLAYER_SPEED * delta;
                        if (keys['d'] || keys['arrowright']) velocity.x += config.PLAYER_SPEED * delta;
                    }

                    const oldY = camera.position.y;
                    camera.translateX(velocity.x * delta);
                    camera.translateZ(velocity.z * delta);
                    camera.position.y = oldY;
                    camera.position.y += velocity.y * delta;
                    if (camera.position.x < -world.width / 2) camera.position.x = -world.width / 2;
                    if (camera.position.x > world.width / 2) camera.position.x = world.width / 2;
                    if (camera.position.z < -world.height / 2) camera.position.z = -world.height / 2;
                    if (camera.position.z > world.height / 2) camera.position.z = world.height / 2;

                    if (camera.position.y < config.PLAYER_HEIGHT) {
                        velocity.y = 0;
                        camera.position.y = config.PLAYER_HEIGHT;
                        canJump = true;
                    }
                }

                // Rotate sprites
                for (const sprite of sprites) {
                    const spritePosition = sprite.position.clone();
                    if (!('pivot' in sprite.userData)) sprite.parent.localToWorld(spritePosition);
                    sprite.rotation.y = Math.atan2((camera.position.x - spritePosition.x), (camera.position.z - spritePosition.z)) -
                        (!('pivot' in sprite.userData) ? sprite.parent.rotation.y : 0);
                }

                // Send player position to server
                if (serverPosition != undefined && serverRotation != undefined) {
                    if (Date.now() - sendMoveTimeout >= config.PLAYER_MOVE_SEND_TIMEOUT) {
                        if (
                            camera.position.x != serverPosition.x || camera.position.y - config.PLAYER_HEIGHT != serverPosition.y || camera.position.z != serverPosition.z ||
                            camera.rotation.x != serverRotation.x || camera.rotation.y != serverRotation.y || camera.rotation.z != serverRotation.z
                        ) {
                            serverPosition = { x: camera.position.x, y: camera.position.y - config.PLAYER_HEIGHT, z: camera.position.z };
                            serverRotation = { x: camera.rotation.x, y: camera.rotation.y, z: camera.rotation.z };
                            this.connection.send('user.move', { position: serverPosition, rotation: serverRotation });
                        }
                        sendMoveTimeout = Date.now()
                    }
                }
            },

            renderLoop(time) {
                window.requestAnimationFrame(this.renderLoop.bind(this));
                if ('Stats' in window) stats.begin();
                this.update(clock.getDelta());
                TWEEN.update(time);
                renderer.render(scene, camera);
                if ('Stats' in window) stats.end();
            },

            createObjects() {
                for (const object of world.objects) {
                    const mesh = createMesh(object);
                    mesh.userData = object;
                    mesh.position.set(object.pivot.position_x, object.pivot.position_y + (object.type == config.OBJECT_TYPE_GROUP ? 0 : (object.type == config.OBJECT_TYPE_SPHERE ? object.height : object.height / 2)), object.pivot.position_z);
                    mesh.rotation.set(object.pivot.rotation_x, object.pivot.rotation_y, object.pivot.rotation_z);
                    meshes.add(mesh);
                }
            },

            handleChat(chat) {
                // Play taunt
                for (const taunt of taunts) {
                    if (chat.message.trim() == taunt.taunt) {
                        chat.message = taunt.taunt + ': ' + taunt.text_en;
                        new Audio('/storage/sounds/' + taunt.sound.audio).play();
                        break;
                    }
                }

                // Add chat to chats and set timeout for removal
                setTimeout(() => {
                    this.chats = this.chats.filter(otherChat => otherChat.id != chat.id);
                }, config.CHAT_FADE_TIMEOUT);
                this.chats.push(chat);
            },

            sendChat() {
                document.getElementById('chat-input').blur();
                this.connection.send('user.chat', { user_id: config.userId, message: this.chatMessage }, data => {
                    if (data.success) {
                        data.chat.user = user;
                        this.handleChat(data.chat);
                        this.chatMessage = '';
                    }
                });
            }
        }
    });
}
