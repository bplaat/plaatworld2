const degrees = rad => rad * 180 / Math.PI;
const radians = deg => (deg * Math.PI) / 180;

function ObjectEditor(data) {
    let renderer, mouse = { drag: false, button: 0, x: 0, y: 0 }, keys = {},
        stats, scene, grid, clock, camera, sprites = [], wireframe;
    const meshes = new THREE.Group();

    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);
    const pyramidGeometry = new THREE.CylinderGeometry(0, 1, 1, 4);
    const wireframeMaterial = new THREE.LineBasicMaterial({ color: 0xff0000 });

    const materials = {};
    function createMaterial(object) {
        const textureId = object.texture_id + '@' + object.texture_repeat_x + 'x' + object.texture_repeat_y;
        if (materials[textureId] == undefined) {
            const texture = data.textures.find(texture => texture.id == object.texture_id);
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
        if (object.type == data.OBJECT_TYPE_SPRITE || object.type == data.OBJECT_TYPE_FIXED_SPRITE) {
            mesh = new THREE.Mesh(planeGeometry, createMaterial(object));
            if (object.type == data.OBJECT_TYPE_SPRITE) {
                sprites.push(mesh);
            }
        }
        if (object.type == data.OBJECT_TYPE_CUBE) {
            mesh = new THREE.Mesh(boxGeometry, createMaterial(object));
        }
        if (object.type == data.OBJECT_TYPE_CYLINDER) {
            mesh = new THREE.Mesh(cylinderGeometry, createMaterial(object));
        }
        if (object.type == data.OBJECT_TYPE_SPHERE) {
            mesh = new THREE.Mesh(sphereGeometry, createMaterial(object));
        }
        if (object.type == data.OBJECT_TYPE_PYRAMID) {
            mesh = new THREE.Mesh(pyramidGeometry, createMaterial(object));
        }
        return mesh;
    }

    const editor = new Vue({
        el: '#object-editor',

        data: {
            saved: false,
            objects: data.objects,
            object: data.object,
            selectedObjectId: null,
            selectedObject: {
                stopWatching: false, type: 0, name: '',
                position_x: 0, position_y: 0, position_z: 0,
                rotation_x: 0, rotation_y: 0, rotation_z: 0,
                scale_x: 0, scale_y: 0, scale_z: 0
            }
        },

        mounted() {
            data.livewire.on('updateObjectIds', this.updateObjectIds.bind(this));
            this.initRenderer();
            this.createObjects();
            clock = new THREE.Clock();
            this.renderLoop();
            if (data.editorUser.selected_object_id != null) {
                this.selectObjectId(data.editorUser.selected_object_id);
            }
            this.loadObjectRenders();
        },

        watch: {
            selectedObjectId() {
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                if (wireframe != undefined) wireframe.removeFromParent();
                if (mesh != null) {
                    wireframe = new THREE.LineSegments(new THREE.EdgesGeometry(mesh.geometry), wireframeMaterial);
                    wireframe.position.set(mesh.position.x, mesh.position.y, mesh.position.z);
                    wireframe.rotation.set(mesh.rotation.x, mesh.rotation.y, mesh.rotation.z);
                    wireframe.scale.set(mesh.scale.x, mesh.scale.y, mesh.scale.z);
                    scene.add(wireframe);
                }
            },

            'selectedObject.name': function (name) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.name = name;
            },
            'selectedObject.position_x': function (position_x) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.position_x = position_x;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.position.x = position_x;
                wireframe.position.x = mesh.position.x;
            },
            'selectedObject.position_y': function (position_y) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.position_y = position_y;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.position.y = parseFloat(position_y) + (object.type == data.OBJECT_TYPE_SPHERE ? object.height : object.height / 2);
                wireframe.position.y = mesh.position.y;
            },
            'selectedObject.position_z': function (position_z) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.position_z = position_z;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.position.z = position_z;
                wireframe.position.z = mesh.position.z;
            },
            'selectedObject.rotation_x': function (rotation_x) {
                if (this.stopWatching) return;
                const real_rotation_x = radians(rotation_x);
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.rotation_x = real_rotation_x;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.rotation.x = real_rotation_x;
                wireframe.rotation.x = mesh.rotation.x;
            },
            'selectedObject.rotation_y': function (rotation_y) {
                if (this.stopWatching) return;
                const real_rotation_y = radians(rotation_y);
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                if (object.type == data.OBJECT_TYPE_SPRITE) return;
                object.pivot.rotation_y = real_rotation_y;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.rotation.y = real_rotation_y;
                wireframe.rotation.y = mesh.rotation.y;
            },
            'selectedObject.rotation_z': function (rotation_z) {
                if (this.stopWatching) return;
                const real_rotation_z = radians(rotation_z);
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.rotation_z = real_rotation_z;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.rotation.z = real_rotation_z;
                wireframe.rotation.z = mesh.rotation.z;
            },
            'selectedObject.scale_x': function (scale_x) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.scale_x = scale_x;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.scale.x = object.width * scale_x;
                wireframe.scale.x = mesh.scale.x;
            },
            'selectedObject.scale_y': function (scale_y) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.scale_y = scale_y;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.scale.y = object.height * scale_y;
                wireframe.scale.y = mesh.scale.y;
            },
            'selectedObject.scale_z': function (scale_z) {
                if (this.stopWatching) return;
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                object.pivot.scale_z = scale_z;
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                mesh.scale.z = object.type == data.OBJECT_TYPE_SPRITE ? 1 : (object.depth * scale_z);
                wireframe.scale.z = mesh.scale.z;
            }
        },

        methods: {
            // Renderer
            initRenderer() {
                // Scene
                scene = new THREE.Scene();
                scene.background = new THREE.Color(getComputedStyle(document.querySelector('.has-navbar-fixed-top')).backgroundColor);
                scene.add(meshes);

                // Camera
                camera = new THREE.PerspectiveCamera(75, 0, 0.1, 1000);
                camera.position.set(data.editorUser.camera_position_x, data.editorUser.camera_position_y, data.editorUser.camera_position_z);
                camera.rotation.set(data.editorUser.camera_rotation_x, data.editorUser.camera_rotation_y, data.editorUser.camera_rotation_z);

                // Stats
                if ('Stats' in window) {
                    stats = new Stats();
                    stats.dom.style.top = '';
                    stats.dom.style.left = '';
                    stats.dom.style.right = '16px';
                    document.body.appendChild(stats.dom);
                }

                // Renderer
                renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('object-editor-canvas') });
                window.addEventListener('resize', this.rendererResize.bind(this));
                renderer.domElement.addEventListener('mousedown', this.rendererMousedown.bind(this));
                window.addEventListener('mousemove', this.rendererMousemove.bind(this));
                window.addEventListener('mouseup', this.rendererMouseup.bind(this));
                renderer.domElement.addEventListener('wheel', this.rendererWheel.bind(this));
                window.addEventListener('contextmenu', event => event.preventDefault());
                window.addEventListener('keydown', this.rendererKeydown.bind(this));
                window.addEventListener('keyup', this.rendererKeyup.bind(this));
                this.rendererResize();

                // Grid
                const gridSize = Math.max(this.object.width, this.object.depth);
                grid = new THREE.GridHelper(gridSize, gridSize);
                grid.position.y = -0.01;
                scene.add(grid);
            },

            rendererResize() {
                const height = window.innerHeight - document.querySelector('.navbar').offsetHeight - document.getElementById('objects-selector').offsetHeight;
                camera.aspect = window.innerWidth / height;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, height);

                if ('Stats' in window) stats.dom.style.bottom = (16 + document.getElementById('objects-selector').offsetHeight) + 'px';
            },

            sendRaycaster(children, x, y) {
                const top = document.querySelector('.navbar').offsetHeight;
                const height = window.innerHeight - top - document.getElementById('objects-selector').offsetHeight;
                const mouse = new THREE.Vector2();
                mouse.x = (x / window.innerWidth) * 2 - 1;
                mouse.y = - ((y - top) / height) * 2 + 1;

                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, camera);
                return intersects = raycaster.intersectObjects(children);
            },

            rendererMousedown(event) {
                mouse.drag = true;
                mouse.button = event.button;
                mouse.x = event.clientX;
                mouse.y = event.clientY;
            },

            rendererMousemove(event) {
                if (mouse.drag) {
                    const rotateSensitivity = 0.004;
                    const moveSensitivity = 0.015;
                    const scrollSensitivity = 0.015;

                    if (mouse.button == 0) {
                        const euler = new THREE.Euler(0, 0, 0, 'YXZ');
                        euler.setFromQuaternion(camera.quaternion);
                        euler.y -= event.movementX * rotateSensitivity;
                        euler.x -= event.movementY * rotateSensitivity;
                        euler.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, euler.x));
                        camera.quaternion.setFromEuler(euler);
                    }

                    if (mouse.button == 1 && this.selectedObjectId != null) {
                        const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                        const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                        const oldY = mesh.position.y;
                        mesh.translateX(event.movementX * moveSensitivity / 2);
                        mesh.translateZ(event.movementY * moveSensitivity / 2);
                        mesh.position.y = oldY;
                        this.selectedObject.position_x = mesh.position.x;
                        this.selectedObject.position_y = mesh.position.y - (object.type == data.OBJECT_TYPE_SPHERE ? object.height : object.height / 2);
                        this.selectedObject.position_z = mesh.position.z;
                    }

                    if (mouse.button == 2) {
                        const oldY = camera.position.y;
                        camera.translateX(-event.movementX * scrollSensitivity);
                        camera.translateZ(-event.movementY * scrollSensitivity);
                        camera.position.y = oldY;
                    }
                }
            },

            rendererMouseup(event) {
                mouse.drag = false;
                if (Math.sqrt((event.clientX - mouse.x) ** 2 + (event.clientY - mouse.y) ** 2) < 4) {
                    if (mouse.button == 1 && this.selectedObjectId != null) {
                        const intersects = this.sendRaycaster([ grid ], event.clientX, event.clientY);
                        if (intersects.length > 0) {
                            this.selectedObject.position_x = intersects[0].point.x;
                            this.selectedObject.position_y = 0;
                            this.selectedObject.position_z = intersects[0].point.z;
                        }
                    } else {
                        const intersects = this.sendRaycaster(meshes.children, event.clientX, event.clientY);
                        if (intersects.length > 0) {
                            const mesh = intersects[0].object;
                            const userData = 'pivot' in mesh.parent.userData ? mesh.parent.userData : mesh.userData;
                            this.selectObjectId(userData.pivot.id);
                        } else {
                            this.selectedObjectId = null;
                        }
                    }
                }
            },

            rendererWheel(event) {
                const zoomSensitivity = 0.005;
                camera.translateZ(event.deltaY * zoomSensitivity);
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
                    const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                    const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);

                    // Object position
                    const positionStep = 2 * delta, rotationStep = 45 * delta;
                    const oldY = mesh.position.y;
                    if (keys['arrowleft']) mesh.translateX(-positionStep);
                    if (keys['arrowright']) mesh.translateX(positionStep);
                    if (keys['arrowup']) mesh.translateZ(-positionStep);
                    if (keys['arrowdown']) mesh.translateZ(positionStep);
                    mesh.position.y = oldY;
                    if (keys['arrowleft'] || keys['arrowright'] || keys['arrowup'] || keys['arrowdown']) {
                        this.selectedObject.position_x = mesh.position.x;
                        this.selectedObject.position_y = mesh.position.y - (object.type == data.OBJECT_TYPE_SPHERE ? object.height : object.height / 2);
                        this.selectedObject.position_z = mesh.position.z;
                    }

                    // Object rotation
                    if (object.type != data.OBJECT_TYPE_SPRITE) {
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
                const cameraSpeed = 10;
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
                    const position = new THREE.Vector3();
                    position.setFromMatrixPosition(sprite.matrixWorld);
                    sprite.rotation.y = Math.atan2((camera.position.x - position.x), (camera.position.z - position.z));
                    if ('pivot' in sprite.userData && this.selectedObjectId == sprite.userData.pivot.id) {
                        wireframe.rotation.set(sprite.rotation.x, sprite.rotation.y, sprite.rotation.z);
                    }
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
                for (const object of this.object.objects) {
                    const mesh = createMesh(object);
                    mesh.userData = object;
                    mesh.position.set(object.pivot.position_x, object.pivot.position_y + (object.type == data.OBJECT_TYPE_SPHERE ? object.height : object.height / 2), object.pivot.position_z);
                    mesh.rotation.set(object.pivot.rotation_x, object.pivot.rotation_y, object.pivot.rotation_z);
                    mesh.scale.set(object.width * object.pivot.scale_x, object.height * object.pivot.scale_y, object.type == data.OBJECT_TYPE_SPRITE ? 1 : (object.depth * object.pivot.scale_z));
                    meshes.add(mesh);
                }
            },

            loadObjectRenders() {
                for (const object of this.objects) {
                    object.texture = data.textures.find(texture => texture.id == object.texture_id);
                    new ObjectViewer({
                        OBJECT_TYPE_GROUP: data.OBJECT_TYPE_GROUP,
                        OBJECT_TYPE_SPRITE: data.OBJECT_TYPE_SPRITE,
                        OBJECT_TYPE_FIXED_SPRITE: data.OBJECT_TYPE_FIXED_SPRITE,
                        OBJECT_TYPE_CUBE: data.OBJECT_TYPE_CUBE,
                        OBJECT_TYPE_CYLINDER: data.OBJECT_TYPE_CYLINDER,
                        OBJECT_TYPE_SPHERE: data.OBJECT_TYPE_SPHERE,
                        OBJECT_TYPE_PYRAMID: data.OBJECT_TYPE_PYRAMID,

                        canvas: document.getElementById('object-' + object.id + '-canvas'),
                        backgroundColor: getComputedStyle(document.getElementById('object-button')).backgroundColor,
                        canvasSize: () => 100,
                        object: object,
                        animated: false
                    });
                }
            },

            // Editor
            updateObjectIds(objectIds) {
                this.object.objects.map(object => {
                    if (object.pivot.id in objectIds) {
                        object.pivot.id = objectIds[object.pivot.id];
                    }
                    return object;
                });
                if (this.selectedObjectId in objectIds) {
                    this.selectedObjectId = objectIds[this.selectedObjectId];
                }
            },

            addObject(object) {
                const newObject = JSON.parse(JSON.stringify(object));
                if (newObject.pivot == undefined) {
                    const selectedObject = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                    newObject.pivot = {
                        name: object.name,
                        position_x: selectedObject != undefined ? selectedObject.pivot.position_x : 0,
                        position_y: selectedObject != undefined ? selectedObject.pivot.position_y : 0,
                        position_z: selectedObject != undefined ? selectedObject.pivot.position_z : 0,
                        rotation_x: selectedObject != undefined ? selectedObject.pivot.rotation_x : 0,
                        rotation_y: selectedObject != undefined ? selectedObject.pivot.rotation_y : 0,
                        rotation_z: selectedObject != undefined ? selectedObject.pivot.rotation_z : 0,
                        scale_x: selectedObject != undefined ? selectedObject.pivot.scale_x : 1,
                        scale_y: selectedObject != undefined ? selectedObject.pivot.scale_y : 1,
                        scale_z: selectedObject != undefined ? selectedObject.pivot.scale_z : 1
                    };
                }
                newObject.pivot.id = Date.now();
                this.object.objects.push(newObject);
                this.selectObjectId(newObject.pivot.id);

                // Create mesh
                const mesh = createMesh(newObject);
                mesh.userData = newObject;
                mesh.position.set(newObject.pivot.position_x, newObject.pivot.position_y + (newObject.type == data.OBJECT_TYPE_SPHERE ? newObject.height : newObject.height / 2), newObject.pivot.position_z);
                mesh.rotation.set(newObject.pivot.rotation_x, newObject.pivot.rotation_y, newObject.pivot.rotation_z);
                mesh.scale.set(newObject.width * newObject.pivot.scale_x, newObject.height * newObject.pivot.scale_y, newObject.type == data.OBJECT_TYPE_SPRITE ? 1 : (newObject.depth * newObject.pivot.scale_z));
                meshes.add(mesh);
            },

            deleteObject(objectId) {
                this.object.objects = this.object.objects.filter(object => object.pivot.id != objectId);
                if (this.selectedObjectId == objectId) {
                    this.selectedObjectId = null;
                }

                // Delete mesh
                for (const mesh of meshes.children) {
                    if (mesh.userData.pivot.id == objectId) {
                        if (mesh.userData.type == data.OBJECT_TYPE_SPRITE) {
                            sprites.splice(sprites.indexOf(mesh), 1);
                        }
                        mesh.removeFromParent();
                        break;
                    }
                }
            },

            selectObjectId(objectId) {
                const object = this.object.objects.find(object => object.pivot.id == objectId);
                if (object != null) {
                    this.selectedObjectId = objectId;
                    this.stopWatching = true;
                    this.selectedObject.type = object.type;
                    this.selectedObject.name = object.pivot.name;
                    this.selectedObject.position_x = object.pivot.position_x;
                    this.selectedObject.position_y = object.pivot.position_y;
                    this.selectedObject.position_z = object.pivot.position_z;
                    this.selectedObject.rotation_x = degrees(object.pivot.rotation_x);
                    this.selectedObject.rotation_y = degrees(object.pivot.rotation_y);
                    this.selectedObject.rotation_z = degrees(object.pivot.rotation_z);
                    this.selectedObject.scale_x = object.pivot.scale_x;
                    this.selectedObject.scale_y = object.pivot.scale_y;
                    this.selectedObject.scale_z = object.pivot.scale_z;
                    this.stopWatching = false;
                } else {
                    this.selectedObjectId = null;
                }
            },

            saveObject() {
                // Update editor user
                data.editorUser.camera_position_x = camera.position.x;
                data.editorUser.camera_position_y = camera.position.y;
                data.editorUser.camera_position_z = camera.position.z;
                data.editorUser.camera_rotation_x = camera.rotation.x;
                data.editorUser.camera_rotation_y = camera.rotation.y;
                data.editorUser.camera_rotation_z = camera.rotation.z;
                data.editorUser.selected_object_id = this.selectedObjectId;

                // Send save object message
                data.livewire.saveObject({ editorUser: data.editorUser, object: this.object });
                this.saved = true;
                setTimeout(() => { this.saved = false; }, 250);
            }
        }
    });
}
