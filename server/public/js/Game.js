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

function Game(config) {
    let user, world, textures, renderer, keys = {}, stats, scene, skyboxBackground, pageBackground, clock, camera, sprites = [];
    const meshes = new THREE.Group();

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
            for (const childObject of object.objects) {
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

    const game = new Vue({
        el: '#game',

        data: {
            connection: new Connection(config.WEBSOCKETS_URL, config.WEBSOCKETS_RECONNECT_TIMEOUT),
            users: [],
            chatMessage: '',
            chats: []
        },

        mounted() {
            this.initRenderer();
            this.connect();
            clock = new THREE.Clock();
            this.renderLoop();
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
                // Create connection
                this.connection = new Connection(config.WEBSOCKETS_URL, config.WEBSOCKETS_RECONNECT_TIMEOUT);
                this.connection.connect();
                this.connection.onConnected = () => {
                    this.connection.send('auth.login', { 'token': config.authToken }, data => {
                        if (data.success) {
                            user = data.user;
                            this.connection.send('world.connect', { 'world_id': config.worldId }, data => {
                                if (data.success) {
                                    world = data.world;
                                    textures = data.textures;

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
                    console.log(id, type, data);

                    if (type == 'user.connect') {
                        this.users.push(data.user);

                        // Move camera to right position
                        if (data.user.id == config.userId) {
                            camera.position.set(data.position.x, data.position.y + 1.5, data.position.z);
                            camera.rotation.set(data.rotation.x, data.rotation.y, data.rotation.z);
                        }
                    }
                    if (type == 'user.disconnect') {
                        this.users = this.users.filter(user => user.id != data.user_id);
                    }
                    if (type == 'user.chat') {
                        data.chat.user = this.users.find(user => user.id == data.user_id);
                        this.chats.push(data.chat);
                        setTimeout(() => {
                            this.chats = this.chats.filter(chat => chat.id != data.chat.id);
                        }, config.CHAT_FADE_TIMEOUT);
                    }
                };
            },

            // Renderer
            initRenderer() {
                // Renderer
                renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('game-canvas') });
                window.addEventListener('resize', this.rendererResize.bind(this));
                window.addEventListener('keydown', this.rendererKeydown.bind(this));
                window.addEventListener('keyup', this.rendererKeyup.bind(this));

                // Scene
                pageBackground = new THREE.Color(getComputedStyle(document.querySelector('.has-navbar-fixed-top')).backgroundColor);
                scene = new THREE.Scene();
                scene.background = pageBackground;
                scene.add(meshes);

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

                // Ground
                // const grassTexture = data.textures.find(texture => texture.name == 'Grass');
                // const groundMaterial = new THREE.MeshBasicMaterial({
                //     map: new THREE.TextureLoader().load('/storage/textures/' + grassTexture.image),
                //     side: THREE.DoubleSide
                // });
                // groundMaterial.map.repeat.set(data.world.width / 5, data.world.height / 5);
                // groundMaterial.map.wrapS = THREE.RepeatWrapping;
                // groundMaterial.map.wrapT = THREE.RepeatWrapping;
                // const ground = new THREE.Mesh(planeGeometry, groundMaterial);
                // ground.scale.x = data.world.width;
                // ground.scale.y = data.world.height;
                // ground.rotation.x = -Math.PI / 2;
                // ground.position.y = -0.01;
                // scene.add(ground);
            },

            rendererResize() {
                const height = window.innerHeight - document.querySelector('.navbar').offsetHeight;
                camera.aspect = window.innerWidth / height;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, height);
            },

            rendererKeydown(event) {
                if (event.target != document.body) return;
                const key = event.key.toLowerCase();
                keys[key] = true;
            },

            rendererKeyup(event) {
                if (event.target != document.body) return;
                const key = event.key.toLowerCase();
                keys[key] = false;
            },

            update(delta) {
                // Check key input
                if (this.selectedObjectId != null) {
                    const object = world.objects.find(object => object.pivot.id == this.selectedObjectId);
                    const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);

                    // Object position
                    const positionStep = 2 * delta, rotationStep = 45 * delta;
                    if (keys['arrowleft']) mesh.translateX(-positionStep);
                    if (keys['arrowright']) mesh.translateX(positionStep);
                    if (keys['arrowup']) mesh.translateZ(-positionStep);
                    if (keys['arrowdown']) mesh.translateZ(positionStep);
                    if (keys['arrowleft'] || keys['arrowright'] || keys['arrowup'] || keys['arrowdown']) {
                        this.selectedObject.position_x = mesh.position.x;
                        this.selectedObject.position_z = mesh.position.z;
                    }

                    // Object rotation
                    if (object.type != config.OBJECT_TYPE_SPRITE) {
                        if (keys['q']) this.selectedObject.rotation_y = parseFloat(this.selectedObject.rotation_y) - rotationStep;
                        if (keys['e']) this.selectedObject.rotation_y = parseFloat(this.selectedObject.rotation_y) + rotationStep;
                    }

                    // Object create and delete
                    if (keys['c']) {
                        keys['c'] = false;
                        this.addObject(object);
                    }
                    if (keys['backspace'] || keys['delete']) this.deleteObject(object.pivot.id);
                }

                // Camera position
                const cameraSpeed = 15;
                const oldCameraY = camera.position.y;
                if (keys['w']) camera.translateZ(-cameraSpeed * delta);
                if (keys['s']) camera.translateZ(cameraSpeed * delta);
                if (keys['a']) camera.translateX(-cameraSpeed * delta);
                if (keys['d']) camera.translateX(cameraSpeed * delta);
                camera.position.y = oldCameraY;
                if (keys[' ']) camera.position.y += cameraSpeed * delta;
                if (keys['shift']) camera.position.y -= cameraSpeed * delta;

                // Rotate sprites
                for (const sprite of sprites) {
                    const spritePosition = sprite.position.clone();
                    if (!('pivot' in sprite.userData)) sprite.parent.localToWorld(spritePosition);
                    sprite.rotation.y = Math.atan2((camera.position.x - spritePosition.x), (camera.position.z - spritePosition.z)) -
                        (!('pivot' in sprite.userData) ? sprite.parent.rotation.y : 0);
                }
            },

            renderLoop() {
                window.requestAnimationFrame(this.renderLoop.bind(this));
                if ('Stats' in window) stats.begin();
                this.update(clock.getDelta());
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

            sendChat() {
                this.connection.send('user.chat', { user_id: config.userId, message: this.chatMessage }, data => {
                    if (data.success) {
                        data.chat.user = user;
                        this.chats.push(data.chat);
                        setTimeout(() => {
                            this.chats = this.chats.filter(chat => chat.id != data.chat.id);
                        }, config.CHAT_FADE_TIMEOUT);
                        this.chatMessage = '';
                    }
                });
            }
        }
    });
}
