function ObjectViewer(data) {
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(data.backgroundColor);

    const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
    camera.position.y = data.object.type == data.OBJECT_TYPE_GROUP ? data.object.height : data.object.height / 2;
    camera.position.z = data.object.depth + Math.max(data.object.width, data.object.height, data.object.depth) / (data.object.type == data.OBJECT_TYPE_GROUP ? 2 : 1);

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
    const pyramidGeometry = new THREE.CylinderGeometry(0, 1, 1, 4);

    const materials = {};
    function createMaterial(object) {
        const textureId = object.texture.id + '@' + object.texture_repeat_x + 'x' + object.texture_repeat_y;
        if (materials[textureId] == undefined) {
            materials[textureId] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image, function () {
                    if (!data.animated) {
                        renderer.render(scene, camera);
                    }
                }),
                transparent: object.texture.transparent,
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

    const sprites = [];
    function createMesh(object) {
        let mesh;
        if (object.type == data.OBJECT_TYPE_GROUP) {
            mesh = new THREE.Group();
            for (const childObject of object.objects) {
                const child = createMesh(childObject);
                child.position.set(childObject.pivot.position_x, childObject.pivot.position_y + childObject.height / 2, childObject.pivot.position_z);
                child.rotation.set(childObject.pivot.rotation_x, childObject.pivot.rotation_y, childObject.pivot.rotation_z);
                child.scale.set(childObject.width * childObject.pivot.scale_x, childObject.height * childObject.pivot.scale_y, childObject.type == data.OBJECT_TYPE_SPRITE ? 1 : (childObject.depth * childObject.pivot.scale_z));
                mesh.add(child);
            }
        }
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

    // Add mesh, loop and rotate mesh
    const mesh = createMesh(data.object);
    if (mesh != undefined) {
        mesh.position.y = data.object.height / 2;
        if (data.object.type != data.OBJECT_TYPE_GROUP) {
            mesh.scale.set(data.object.width, data.object.height, data.object.type == data.OBJECT_TYPE_SPRITE ? 1 : data.object.depth);
        }
        scene.add(mesh);

        const clock = new THREE.Clock();
        function loop() {
            window.requestAnimationFrame(loop);
            const delta = clock.getDelta();

            // Rotate sprites when group
            if (data.object.type == data.OBJECT_TYPE_GROUP) {
                for (const sprite of sprites) {
                    const spritePosition = sprite.position.clone();
                    sprite.parent.localToWorld(spritePosition);
                    sprite.rotation.y = Math.atan2(camera.position.x - spritePosition.x, camera.position.z - spritePosition.z) - sprite.parent.rotation.y;
                }
            }

            // Rotate mesh
            if (data.object.type != data.OBJECT_TYPE_GROUP && data.object.type != data.OBJECT_TYPE_SPRITE && data.object.type != data.OBJECT_TYPE_FIXED_SPRITE) {
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
