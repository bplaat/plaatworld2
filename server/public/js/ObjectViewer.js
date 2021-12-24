function ObjectViewer(data) {
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(data.backgroundColor);

    const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
    camera.position.y = data.object.height / 2;
    camera.position.z = data.object.depth + Math.max(data.object.width, data.object.height, data.object.depth);

    const renderer = new THREE.WebGLRenderer({ canvas: data.canvas });
    function resize() {
        const size = data.canvasSize();
        renderer.setSize(size, size);
        if (!data.animated) {
            renderer.render(scene, camera);
        }
    }
    window.addEventListener('resize', resize);
    resize();

    // Create meshes
    const planeGeometry = new THREE.PlaneGeometry(1, 1);
    const boxGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cylinderGeometry = new THREE.CylinderGeometry(1, 1, 1, 32);
    const sphereGeometry = new THREE.SphereGeometry(1, 32, 16);

    const materials = {};
    function createMaterial(texture) {
        if (materials[texture.id] == undefined) {
            materials[texture.id] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + texture.image, function () {
                    if (!data.animated) {
                        renderer.render(scene, camera);
                    }
                }),
                transparent: texture.transparent,
                side: THREE.DoubleSide
            });
        }
        return materials[texture.id];
    }

    function createMesh(object) {
        let mesh;
        if (object.type == data.OBJECT_TYPE_GROUP) {
            mesh = new THREE.Group();
            for (const childObject of object.objects) {
                const child = createMesh(childObject);
                child.position.set(childObject.pivot.position_x, childObject.pivot.position_y + childObject.height / 2, childObject.pivot.position_z);
                child.rotation.set(childObject.pivot.rotation_x, childObject.pivot.rotation_y, childObject.pivot.rotation_z);
                mesh.add(child);
            }
        }
        if (object.type == data.OBJECT_TYPE_SPRITE || object.type == data.OBJECT_TYPE_FIXED_SPRITE) {
            mesh = new THREE.Mesh(planeGeometry, createMaterial(object.texture));
            mesh.scale.set(object.width, object.height, 0);
        }
        if (object.type == data.OBJECT_TYPE_CUBE) {
            mesh = new THREE.Mesh(boxGeometry, createMaterial(object.texture));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_CYLINDER) {
            mesh = new THREE.Mesh(cylinderGeometry, createMaterial(object.texture));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_SPHERE) {
            mesh = new THREE.Mesh(sphereGeometry, createMaterial(object.texture));
            mesh.scale.set(object.width, object.height, object.depth);
        }
        if (object.type == data.OBJECT_TYPE_PYRAMID) {
            mesh = new THREE.Mesh(new THREE.CylinderGeometry(0, Math.min(object.width, object.depth), object.height, 4), createMaterial(object.texture));
            mesh.rotation.y = Math.PI / 4;
        }
        return mesh;
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
            if (data.object.type != data.OBJECT_TYPE_SPRITE && data.object.type != data.OBJECT_TYPE_FIXED_SPRITE) {
                mesh.rotation.x += 0.5 * delta;
            }
            mesh.rotation.y += 1 * delta;
            renderer.render(scene, camera);
        }
        if (data.animated) {
            loop();
        } else {
            renderer.render(scene, camera);
        }
    }
}
