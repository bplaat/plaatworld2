function ObjectViewer(data) {
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(getComputedStyle(document.querySelector('.card')).backgroundColor);

    const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
    camera.position.y = data.object.height / 2;
    camera.position.z = data.object.depth + Math.max(data.object.width, data.object.height, data.object.depth);

    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('object-' + data.object.id + '-canvas') });
    function resize() {
        const size = document.querySelector('.card-image').offsetWidth;
        camera.updateProjectionMatrix();
        renderer.setSize(size, size);
    }
    window.addEventListener('resize', resize);
    resize();

    // Create meshes
    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);
    function createMesh(object) {
        // if (object.type == data.OBJECT_TYPE_GROUP) {
        //     const group = new THREE.Group();

        //     const mesh = new THREE.Mesh(new THREE.BoxGeometry(1, 1, 1), new THREE.MeshBasicMaterial({
        //         color: 0xffffff,
        //         wireframe: true
        //     }));
        //     mesh.scale.x = object.width;
        //     mesh.scale.y = object.height;
        //     mesh.scale.z = object.depth;
        //     group.add(mesh);

        //     for (const childObject of object.objects) {
        //         const childMesh = createMesh(childObject);
        //         childMesh.position.set(childObject.pivot.position_x, childObject.pivot.position_y, childObject.pivot.position_z);
        //         childMesh.rotation.set(childObject.pivot.rotation_x, childObject.pivot.rotation_y, childObject.pivot.rotation_z);
        //         group.add(childMesh);
        //     }
        //     return group;
        // }
        if (object.type == data.OBJECT_TYPE_SPRITE) {
            const mesh = new THREE.Mesh(planeGeometry, new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image),
                transparent: true,
                side: THREE.DoubleSide
            }));
            mesh.scale.x = object.width;
            mesh.scale.y = object.height;
            return mesh;
        }
        if (object.type == data.OBJECT_TYPE_CUBE) {
            const mesh = new THREE.Mesh(boxGeometry, new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image)
            }));
            mesh.scale.x = object.width;
            mesh.scale.y = object.height;
            mesh.scale.z = object.depth;
            return mesh;
        }
        if (object.type == data.OBJECT_TYPE_CYLINDER) {
            const mesh = new THREE.Mesh(cylinderGeometry, new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image)
            }));
            mesh.scale.x = object.width;
            mesh.scale.y = object.height;
            mesh.scale.z = object.depth;
            return mesh;
        }
        if (object.type == data.OBJECT_TYPE_SPHERE) {
            const mesh = new THREE.Mesh(sphereGeometry, new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image)
            }));
            mesh.scale.x = object.width;
            mesh.scale.y = object.height;
            mesh.scale.z = object.depth;
            return mesh;
        }
        if (object.type == data.OBJECT_TYPE_PYRAMID) {
            const mesh = new THREE.Mesh(new THREE.CylinderGeometry(0, Math.min(object.width, object.depth), object.height, 4), new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image)
            }));
            return mesh;
        }
    }

    // Add mesh, loop and rotate mesh
    const mesh = createMesh(data.object);
    if (mesh != undefined) {
        mesh.position.y = data.object.height / 2;
        scene.add(mesh);

        const clock = new THREE.Clock();
        function loop() {
            window.requestAnimationFrame(loop);
            const delta = clock.getDelta();
            if (data.object.type != data.OBJECT_TYPE_SPRITE) {
                mesh.rotation.x += 0.5 * delta;
            }
            mesh.rotation.y += 1 * delta;
            renderer.render(scene, camera);
        }
        loop();
    }
}
