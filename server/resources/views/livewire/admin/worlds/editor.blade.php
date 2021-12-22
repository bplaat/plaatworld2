<div style="position: relative;" wire:ignore>
    <canvas id="canvas"></canvas>

    <div id="editor">
        <div style="position:absolute;top:20px;left:16px;background-color:#242424;padding:16px;">
            <h1 class="title is-5 mb-3">The PlaatWorld II Editor</h1>
            <p><i>Press C: Copy selected object</i></p>
            <p><i>Press Q: Rotate selected object to left</i></p>
            <p><i>Press E: Rotate selected object to right</i></p>
            <p><i>Double click: Move selected object</i></p>
            <div class="buttons mt-3 mb-0">
                <button class="button is-link" @click="saveWorld">Save</button>
                <a class="button is-danger" href="{{ route('admin.worlds.crud') }}">Exit</a>
            </div>
            <p><i>@{{ message }}</i></p>
        </div>

        <div style="position:absolute;top:20px;right:16px;background-color:#242424;padding:16px;min-width:200px;max-height:80%;overflow-y:scroll">
            <h2 class="title is-6 mb-3">World Objects:</h2>
            <button :class="{'button': true, 'my-1': true, 'is-small': true, 'is-warning': selectedWorldObject != null && selectedWorldObject.name == worldObject.name}"
                @click="selectedWorldObject = worldObject" :key="worldObject.name"
                style="display: block; width: 100%; text-align: left;" v-for="worldObject, index in worldObjects">
                @{{ worldObject.name }} (@{{ worldObject.object.name }}):
                @{{ worldObject.position.x }}x@{{ worldObject.position.y }}x@{{ worldObject.position.z }}
            </button>
            <p v-if="worldObjects.length == 0"><i>No objects placed</i></p>
        </div>

        <div style="position:absolute;left:16px;bottom:16px;background-color:#242424;padding:16px;">
            <h2 class="title is-6 mb-3">Objects that you can place:</h2>
            <div class="buttons">
                <button class="button" v-for="object in objects" @click="addObject(object)" :key="object.id">@{{ object.name }}</button>
            </div>
        </div>
    </div>

<script src="/js/vue.min.js"></script>
<script src="/js/three.min.js"></script>
<script src="/js/stats.min.js"></script>
<script src="/js/OrbitControls.min.js"></script>
<script>
document.addEventListener('livewire:load', function () {

const data = {
    OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
    OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
    world: @json($world),
    objects: @json(App\Models\GameObject::orderByRaw('LOWER(name)')->with('texture')->get()),
    worldObjects: @json($worldObjects),
    grassTextureImage: @json('/storage/textures/' . App\Models\Texture::where('name', 'Grass')->first()->image)
};

// ##################### THREE ###########################

// Load textures and create materials for objects
const objectMaterials = {};
for (const object of data.objects) {
    if (objectMaterials[object.id] == undefined) {
        if (object.type == data.OBJECT_TYPE_SPRITE) {
            objectMaterials[object.id] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image),
                transparent: true,
                side: THREE.DoubleSide
            });
        }
        if (object.type == data.OBJECT_TYPE_CUBE) {
            objectMaterials[object.id] = new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/storage/textures/' + object.texture.image),
                side: THREE.DoubleSide
            });
        }
    }
}

// Scene and camera
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, 0, 0.1, 1000);

// Renderer
const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('canvas') });
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
camera.position.set(0, Math.sqrt((data.world.width / 2) * (data.world.height / 2)), -(data.world.height / 2));
controls.update();

// Spawn cube
const spawnGeometry = new THREE.BoxGeometry(1, 1, 1);
const spawnMaterial = new THREE.MeshNormalMaterial();
const spawn = new THREE.Mesh(spawnGeometry, spawnMaterial);
spawn.position.set(data.world.spawn_position_x, data.world.spawn_position_y + 1.5, data.world.spawn_position_z);
scene.add(spawn);

// Ground
const grounds = new THREE.Group();
scene.add(grounds);
const groundGeometry = new THREE.PlaneGeometry(data.world.width, data.world.height);
const groundMaterial = new THREE.MeshBasicMaterial({ map: new THREE.TextureLoader().load(data.grassTextureImage), side: THREE.DoubleSide });
groundMaterial.map.repeat.set(data.world.width / 5, data.world.height / 5);
groundMaterial.map.wrapS = THREE.RepeatWrapping;
groundMaterial.map.wrapT = THREE.RepeatWrapping;
const ground = new THREE.Mesh(groundGeometry, groundMaterial);
ground.rotation.x = -Math.PI / 2;
ground.position.y = -0.01;
grounds.add(ground);

// Sprite objects group
const sprites = new THREE.Group();
const spriteGeomery = new THREE.PlaneGeometry(1, 1);
scene.add(sprites);

// Cube objects group
const cubes = new THREE.Group();
const cubeGeomery = new THREE.BoxGeometry(1, 1, 1);
scene.add(cubes);

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
    controls.update();

    const delta = clock.getDelta();

    // Rotate spawn
    spawn.rotation.x += 5 * delta;
    spawn.rotation.y += 1 * delta;

    // Rotate sprites
    for (const sprite of sprites.children) {
        sprite.rotation.y = Math.atan2( ( camera.position.x - sprite.position.x ), ( camera.position.z - sprite.position.z ) );
    }

    renderer.render(scene, camera);
    stats.end();
}
loop();

// ######################### VUE #######################

const editor = new Vue({
    el: '#editor',

    data: {
        message: '',
        worldObjects: data.worldObjects,
        selectedWorldObject: null,
        textures: data.textures,
        objects: data.objects
    },

    created() {
        for (const worldObject of this.worldObjects) {
            worldObject.object = this.objects.find(object => object.id == worldObject.object_id);
            this.createMesh(worldObject);
        }

        renderer.domElement.addEventListener('dblclick', event => {
            if (this.selectedWorldObject == undefined) return;

            const mouse = new THREE.Vector2();
            const top = document.querySelector('.navbar').offsetHeight;
            mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
            mouse.y = - ( (event.clientY - top) / (window.innerHeight - top) ) * 2 + 1;

            const raycaster = new THREE.Raycaster();
            raycaster.setFromCamera(mouse, camera);

            const intersects = raycaster.intersectObjects(grounds.children);
            if (intersects.length > 0) {
                const point = intersects[0].point;
                this.selectedWorldObject.position.x = point.x.toFixed(3);
                this.selectedWorldObject.position.z = point.z.toFixed(3);
                this.selectedWorldObject.mesh.position.x = this.selectedWorldObject.position.x;
                this.selectedWorldObject.mesh.position.z = this.selectedWorldObject.position.z;
            }
        });

        window.addEventListener('keydown', event => {
            const key = event.key.toLowerCase();
            if (this.selectedWorldObject != undefined) {
                if (key == 'c') {
                    this.addObject(this.selectedWorldObject.object);
                }
                if (key == 'q') {
                    this.selectedWorldObject.rotation.y -= 0.05;
                    this.selectedWorldObject.mesh.rotation.y = this.selectedWorldObject.rotation.y;
                }
                if (key == 'e') {
                    this.selectedWorldObject.rotation.y += 0.05;
                    this.selectedWorldObject.mesh.rotation.y = this.selectedWorldObject.rotation.y;
                }
            }
        })
    },

    methods: {
        createMesh(worldObject) {
            if (worldObject.object.type == data.OBJECT_TYPE_SPRITE) {
                worldObject.mesh = new THREE.Mesh(spriteGeomery, objectMaterials[worldObject.object.id]);
                worldObject.mesh.scale.x = worldObject.object.width;
                worldObject.mesh.scale.y = worldObject.object.height;
                worldObject.mesh.position.set(worldObject.position.x, worldObject.position.y, worldObject.position.z);
                sprites.add(worldObject.mesh);
            }
            if (worldObject.object.type == data.OBJECT_TYPE_CUBE) {
                worldObject.mesh = new THREE.Mesh(cubeGeomery, objectMaterials[worldObject.object.id]);
                worldObject.mesh.scale.x = worldObject.object.width;
                worldObject.mesh.scale.y = worldObject.object.height;
                worldObject.mesh.scale.z = worldObject.object.depth;
                worldObject.mesh.position.set(worldObject.position.x, worldObject.position.y, worldObject.position.z);
                worldObject.mesh.rotation.set(worldObject.rotation.x, worldObject.rotation.y, worldObject.rotation.z);
                cubes.add(worldObject.mesh);
            }
        },

        addObject(object) {
            const selectedWorldObject = this.selectedWorldObject || { position: {x: 0, z: 0}};
            const worldObject = {
                object_id: object.id,
                object,
                name: object.name + ' #' + Date.now(),
                position: { x: selectedWorldObject.position.x, y: object.height / 2, z: selectedWorldObject.position.z },
                rotation: { x: 0, y: 0, z: 0 }
            };
            this.selectedWorldObject = worldObject;
            this.worldObjects.push(worldObject);
            this.createMesh(worldObject);
        },

        saveWorld() {
            @this.saveWorld(this.worldObjects.map(worldObject => ({
                object_id: worldObject.object_id,
                name: worldObject.name,
                position: worldObject.position,
                rotation: worldObject.rotation
            })));
            this.message = 'Saved!';
            setTimeout(() => { this.message = ''; }, 250);
        }
    }
});

});
</script>

</div>
