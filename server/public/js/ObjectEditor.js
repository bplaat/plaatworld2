function ObjectEditor(data) {
    console.log(data.object);

    // Scene
    const scene = new THREE.Scene();

    // Camera
    const camera = new THREE.PerspectiveCamera(75, 0, 0.1, 1000);

    // Renderer
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('object-editor-canvas') });
    function resize() {
        const height = window.innerHeight - document.querySelector('.navbar').offsetHeight;
        camera.aspect = window.innerWidth / height;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, height);
    }
    window.addEventListener('resize', resize);
    resize();

    // Controls
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    const gridSize = Math.max(5, Math.max(data.object.width, data.object.height, data.object.depth));
    camera.position.set(0, gridSize, gridSize * 1.5);
    controls.update();

    // Grid
    var grid = new THREE.GridHelper(gridSize * 2, gridSize * 2);
    scene.add(grid);

    // Create mesh
    const sprites = [];

    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);
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
            mesh.rotation.y = Math.PI / 4;
        }

        if (object.objects != undefined && object.objects.length > 0) {
            const group = new THREE.Group();
            group.add(mesh);
            for (const childObject of object.objects) {
                const childMesh = createMesh(childObject);
                childMesh.position.set(childObject.pivot.position_x, childObject.pivot.position_y  - object.height / 2 + childObject.height / 2, childObject.pivot.position_z);
                if (childObject.type != data.OBJECT_TYPE_SPRITE) {
                    childMesh.rotation.set(childObject.pivot.rotation_x, childObject.pivot.rotation_y, childObject.pivot.rotation_z);
                } else {
                    sprites.push(childMesh);
                }
                group.add(childMesh);
            }
            return group
        } else {
            return mesh;
        }
    }

    const mesh = createMesh(data.object);
    mesh.position.y = data.object.height / 2;
    scene.add(mesh);

    // // Selected object
    // var mat = new THREE.LineBasicMaterial({ color: 0xff0000 });
    // var wireframe = new THREE.LineSegments( new THREE.EdgesGeometry(mesh.geometry), mat );
    // wireframe.position.set(mesh.position.x, mesh.position.y, mesh.position.z);
    // wireframe.scale.set(mesh.scale.x * 1.1, mesh.scale.y * 1.1, mesh.scale.z * 1.1);
    // wireframe.rotation.set(mesh.rotation.x, mesh.rotation.y, mesh.rotation.z);
    // scene.add(wireframe);

    // Stats
    const stats = new Stats();
    stats.dom.style.top = '';
    stats.dom.style.left = '';
    stats.dom.style.right = '16px';
    stats.dom.style.bottom = '16px';
    document.body.appendChild(stats.dom);

    scene.updateMatrixWorld(true);
    // Loop
    function loop() {
        window.requestAnimationFrame(loop);
        stats.begin();
        controls.update();

        // Rotate sprites
        for (const sprite of sprites) {
            var position = new THREE.Vector3();
            position.setFromMatrixPosition(sprite.matrixWorld);
            sprite.rotation.y = Math.atan2((camera.position.x - position.x), (camera.position.z - position.z));
        }

        renderer.render(scene, camera);
        stats.end();
    }
    loop();
}
