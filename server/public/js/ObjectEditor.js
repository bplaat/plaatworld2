const degrees = rad => rad * 180 / Math.PI;
const radians = deg => (deg * Math.PI) / 180;

function ObjectEditor(data) {
    let renderer, controls, stats, scene, camera, sprites = [], wireframe;
    const meshes = new THREE.Group();

    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);
    const wireframeMaterial = new THREE.LineBasicMaterial({ color: 0xff0000 });

    const materials = {};
    function createMaterial(texture_id) {
        if (materials[texture_id] == undefined) {
            const texture = data.textures.find(texture => texture.id == texture_id);
            materials[texture_id] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + texture.image),
                transparent: texture.transparent,
                side: THREE.DoubleSide
            });
        }
        return materials[texture_id];
    }

    function createMesh(object) {
        let mesh;
        if (object.type == data.OBJECT_TYPE_SPRITE || object.type == data.OBJECT_TYPE_FIXED_SPRITE) {
            mesh = new THREE.Mesh(planeGeometry, createMaterial(object.texture_id));
            mesh.scale.set(object.width, object.height, 0);
            if (object.type == data.OBJECT_TYPE_SPRITE) {
                sprites.push(mesh);
            }
        }
        if (object.type == data.OBJECT_TYPE_CUBE) {
            mesh = new THREE.Mesh(boxGeometry, createMaterial(object.texture_id));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_CYLINDER) {
            mesh = new THREE.Mesh(cylinderGeometry, createMaterial(object.texture_id));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_SPHERE) {
            mesh = new THREE.Mesh(sphereGeometry, createMaterial(object.texture_id));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_PYRAMID) {
            mesh = new THREE.Mesh(new THREE.CylinderGeometry(0, Math.min(object.width, object.depth), object.height, 4), createMaterial(object.texture_id));
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
                rotation_x: 0, rotation_y: 0, rotation_z: 0
            }
        },

        mounted() {
            data.livewire.on('updateObjectIds', this.updateObjectIds.bind(this));
            this.initRenderer();
            this.renderLoop();
            this.syncObjects();
            if (data.editorUser.selected_object_id != null) {
                this.selectObjectId(data.editorUser.selected_object_id);
            }
            this.loadObjectRenders();
        },

        watch: {
            'object.objects': function () {
                this.syncObjects();
            },

            selectedObjectId() {
                const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);
                if (wireframe != undefined) wireframe.removeFromParent();
                if (mesh != null) {
                    wireframe = new THREE.LineSegments(new THREE.EdgesGeometry(mesh.geometry), wireframeMaterial);
                    wireframe.position.set(mesh.position.x, mesh.position.y, mesh.position.z);
                    wireframe.scale.set(object.width * 1.1, object.height * 1.1, object.depth * 1.1);
                    wireframe.rotation.set(mesh.rotation.x, mesh.rotation.y, mesh.rotation.z);
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
                renderer.domElement.addEventListener('mouseup', this.rendererMouseup.bind(this));
                renderer.domElement.addEventListener('dblclick', this.rendererDoubleclick.bind(this));
                window.addEventListener('keydown', this.rendererKeydown.bind(this));
                this.rendererResize();

                // Controls
                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.update();

                // Grid
                const gridSize = Math.max(this.object.width, this.object.depth);
                const grid = new THREE.GridHelper(gridSize, gridSize);
                grid.position.y = -0.01;
                scene.add(grid);
            },

            rendererResize() {
                const height = window.innerHeight - document.querySelector('.navbar').offsetHeight - document.getElementById('objects-selector').offsetHeight;
                camera.aspect = window.innerWidth / height;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, height);

                stats.dom.style.bottom = (16 + document.getElementById('objects-selector').offsetHeight) + 'px';
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

            rendererMouseup(event) {
                const intersects = this.sendRaycaster(meshes.children, event.clientX, event.clientY);
                if (intersects.length > 0 && 'pivot' in intersects[0].object.userData) {
                    this.selectObjectId(intersects[0].object.userData.pivot.id);
                }
            },

            rendererDoubleclick(event) {
                if (this.selectObjectId != null) {
                    const intersects = this.sendRaycaster(scene.children, event.clientX, event.clientY);
                    if (intersects.length > 0) {
                        const point = intersects[0].point;
                        this.selectedObject.position_x = point.x;
                        this.selectedObject.position_z = point.z;
                    }
                }
            },

            rendererKeydown(event) {
                if (event.target != document.body) return;
                const key = event.key.toLowerCase();
                if (this.selectedObjectId != null) {
                    const object = this.object.objects.find(object => object.pivot.id == this.selectedObjectId);
                    const mesh = meshes.children.find(mesh => mesh.userData.pivot.id == this.selectedObjectId);

                    const positionStep = 0.1, rotationStep = 9;
                    if (key == 'a') mesh.translateX(-positionStep);
                    if (key == 'd') mesh.translateX(positionStep);
                    if (key == ' ') this.selectedObject.position_y = parseFloat(this.selectedObject.position_y) + positionStep;
                    if (key == 'shift') this.selectedObject.position_y = parseFloat(this.selectedObject.position_y) - positionStep;
                    if (key == 'w') mesh.translateZ(-positionStep);
                    if (key == 's') mesh.translateZ(positionStep);
                    if (key == 'a' || key == 'd' || key == 'w' || key == 's') {
                        this.selectedObject.position_x = mesh.position.x;
                        this.selectedObject.position_z = mesh.position.z;
                    }

                    if (object.type != data.OBJECT_TYPE_SPRITE) {
                        if (key == 'q') this.selectedObject.rotation_y = parseFloat(this.selectedObject.rotation_y) - rotationStep;
                        if (key == 'e') this.selectedObject.rotation_y = parseFloat(this.selectedObject.rotation_y) + rotationStep;
                    }

                    if (key == 'c') this.addObject(object);
                }
            },

            renderLoop() {
                window.requestAnimationFrame(this.renderLoop.bind(this));
                if ('Stats' in window) stats.begin();
                controls.update();

                // Rotate sprites
                for (const sprite of sprites) {
                    const position = new THREE.Vector3();
                    position.setFromMatrixPosition(sprite.matrixWorld);
                    sprite.rotation.y = Math.atan2((camera.position.x - position.x), (camera.position.z - position.z));
                    if ('pivot' in sprite.userData && editor.selectedObjectId == sprite.userData.pivot.id) {
                        wireframe.rotation.set(sprite.rotation.x, sprite.rotation.y, sprite.rotation.z);
                    }
                }

                renderer.render(scene, camera);
                if ('Stats' in window) stats.end();
            },

            syncObjects() {
                meshes.clear();
                sprites = [];
                for (const object of this.object.objects) {
                    const mesh = createMesh(object);
                    mesh.userData = object;
                    mesh.position.set(object.pivot.position_x, object.pivot.position_y + (object.type == data.OBJECT_TYPE_SPHERE ? object.height : object.height / 2), object.pivot.position_z);
                    mesh.rotation.set(object.pivot.rotation_x, object.pivot.rotation_y, object.pivot.rotation_z);
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
                        rotation_z: selectedObject != undefined ? selectedObject.pivot.rotation_z : 0
                    };
                }
                newObject.pivot.id = Date.now();
                this.object.objects.push(newObject);
                this.selectObjectId(newObject.pivot.id);
            },

            deleteObject(objectId) {
                this.object.objects = this.object.objects.filter(object => object.pivot.id != objectId);
                if (this.selectedObjectId == objectId) {
                    this.selectedObjectId = null;
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
                data.editorUser.selected_object_id = editor.selectedObjectId;

                // Send save object message
                data.livewire.saveObject({ editorUser: data.editorUser, object: this.object });
                this.saved = true;
                setTimeout(() => { this.saved = false; }, 250);
            }
        }
    });
}
