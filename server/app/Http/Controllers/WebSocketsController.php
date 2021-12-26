<?php

namespace App\Http\Controllers;

use App\Models\GameObject;
use App\Models\Texture;
use App\Models\Taunt;
use App\Models\User;
use App\Models\World;
use App\Models\WorldChat;
use App\Models\WorldUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/*
### The PlaatWorld II websocket protocol (it's bad I know) ###
All messages: {id:Date.now(),type:string,data:object}
Response {id:same,type:string+'.response',data:object}

<- auth.login {token}
auth.login.response -> {success}

<- world.connect {worldId}
world.info -> {world{objects},textures}
user.connect -> world* (all) {userId,position,rotation}

<- user.move {position,rotation}
user.move -> world* (except sender) {userId,position,rotation}

<- user.chat {message}
user.chat -> world* (except sender) {userId,message}

[User disconnects]
user.disconnect -> world* {userId}
*/

function send($connection, $id, $type, $data) {
    $connection->send(json_encode(['id' => $id == 0 ? (int)(microtime(true) * 1000) : $id, 'type' => $type, 'data' => $data]));
}

function broadcast($userId, $worldId, $connections, $id, $type, $data) {
    foreach ($connections as $resourceId => $connection) {
        if ($connection['world'] != null && $connection['world']->id == $worldId && $connection['user']->id != $userId) {
            send($connection['connection'], $id, $type, $data);
        }
    }
}

class WebSocketsController extends Controller implements MessageComponentInterface {
    private $connections = [];

    public function onOpen(ConnectionInterface $connection) {
        echo 'Client connected' . PHP_EOL;
        $this->connections[$connection->resourceId] = ['connection' => $connection, 'user' => null, 'world' => null, 'worldUser' => null];
    }

    public function onMessage(ConnectionInterface $connection, $message) {
        // Validate basic incomming message
        $validation = Validator::make(['message' => $message, 'id' => json_decode($message)->id, 'type' => json_decode($message)->type], [
            'message' => 'required|json',
            'id' => 'required|numeric|min:0',
            'type' => 'required|string'
        ]);
        if ($validation->fails()) {
            print_r($validation->errors());
            return;
        }

        // Parse message
        $message = json_decode($message);
        $id = $message->id;
        $type = $message->type;
        $data = $message->data;
        echo $id . ' ' . $type . ' ' . json_encode($data) . PHP_EOL;

        // #################################################################################
        // ################################# AUTH MESSAGES #################################
        // #################################################################################

        if ($type == 'auth.login') {
            // Validate input
            $validation = Validator::make(['token' => $data->token], [
                'token' => 'required|string'
            ]);
            if ($validation->fails()) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => $validation->errors()]);
                return;
            }

            // Get user by token send
            $token = DB::table('personal_access_tokens')->where('token', hash('sha256', $data->token))->first();
            if ($token == null) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => ['token' => 'Auth token is not valid!']]);
                return;
            }

            // Get user and send response response
            $user = User::find($token->tokenable_id);
            $this->connections[$connection->resourceId]['user'] = $user;
            send($connection, $id, $type . '.response', ['success' => true, 'user' => $user]);
        }

        // #################################################################################
        // ################################ WORLD MESSAGES #################################
        // #################################################################################

        if ($type == 'world.connect') {
            // Validate input
            $validation = Validator::make(['world_id' => $data->world_id], [
                'world_id' => 'required|integer|exists:worlds,id'
            ]);
            if ($validation->fails()) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => $validation->errors()]);
                return;
            }

            // Get world and all its objects and its child objects
            $world = World::where('id', $data->world_id)->with('objects')->first();
            for ($i = 0; $i < $world->objects->count(); $i++) {
                if ($world->objects[$i]->type == GameObject::TYPE_GROUP) {
                    $world->objects[$i]->objects;
                }
            }

            // Check if user is authed and connected to a world
            $user = $this->connections[$connection->resourceId]['user'];
            if ($user == null) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => ['message' => 'User not authed!']]);
                return;
            }

            // Check if user is already connected to this world
            foreach ($this->connections as $resourceId => $otherConnection) {
                if (
                    $otherConnection['world'] != null &&
                    $otherConnection['world']->id == $world->id &&
                    $otherConnection['user']->id == $user->id
                ) {
                    send($connection, $id, $type . '.response', ['success' => false, 'errors' => ['world_id' => 'User already connected to this world!']]);
                    return;
                }
            }
            $this->connections[$connection->resourceId]['world'] = $world;

            // Get or create world user
            $worldUser = WorldUser::where('world_id', $world->id)->where('user_id', $user->id)->first();
            if ($worldUser == null) {
                $worldUser = new WorldUser();
                $worldUser->world_id = $world->id;
                $worldUser->user_id = $user->id;
                $worldUser->position_x = $world->spawn_position_x;
                $worldUser->position_y = $world->spawn_position_y;
                $worldUser->position_z = $world->spawn_position_z;
                $worldUser->rotation_x = ($world->spawn_rotation_x * pi()) / 180;
                $worldUser->rotation_y = ($world->spawn_rotation_y * pi()) / 180;
                $worldUser->rotation_z = ($world->spawn_rotation_z * pi()) / 180;
                $worldUser->save();
            }
            $this->connections[$connection->resourceId]['worldUser'] = $worldUser;

            // Send response message
            send($connection, $id, $type . '.response', [
                'success' => true,
                'world' => $world,
                'textures' => Texture::all(),
                'taunts' => Taunt::with('sound')->get()
            ]);

            // Send messages of all connected users and yourself to you
            foreach ($this->connections as $resourceId => $otherConnection) {
                if ($otherConnection['world'] != null && $otherConnection['world']->id == $world->id) {
                    send($connection, 0, 'user.connect', [
                        'user' => $otherConnection['user'],
                        'position' => ['x' => $otherConnection['worldUser']->position_x, 'y' => $otherConnection['worldUser']->position_y, 'z' => $otherConnection['worldUser']->position_z],
                        'rotation' => ['x' => $otherConnection['worldUser']->rotation_x, 'y' => $otherConnection['worldUser']->rotation_y, 'z' => $otherConnection['worldUser']->rotation_z]
                    ]);
                }
            }

            // Send all other users a connect message from you
            broadcast($user->id, $world->id, $this->connections, 0, 'user.connect', [
                'user' => $user,
                'position' => ['x' => $worldUser->position_x, 'y' => $worldUser->position_y, 'z' => $worldUser->position_z],
                'rotation' => ['x' => $worldUser->rotation_x, 'y' => $worldUser->rotation_y, 'z' => $worldUser->rotation_z]
            ]);
        }

        // #################################################################################
        // ################################# USER MESSAGES #################################
        // #################################################################################

        if ($type == 'user.move') {
            // Validate input
            $validation = Validator::make([
                'position' => ['x' => $data->position->x, 'y' => $data->position->y, 'z' => $data->position->z],
                'rotation' => ['x' => $data->rotation->x, 'y' => $data->rotation->y, 'z' => $data->rotation->z]
            ], [
                'position.x' => 'required|numeric',
                'position.y' => 'required|numeric',
                'position.z' => 'required|numeric',
                'rotation.x' => 'required|numeric',
                'rotation.y' => 'required|numeric',
                'rotation.z' => 'required|numeric'
            ]);
            if ($validation->fails()) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => $validation->errors()]);
                return;
            }

            // Check if user is authed and connected to a world
            $user = $this->connections[$connection->resourceId]['user'];
            $world = $this->connections[$connection->resourceId]['world'];
            if ($user == null || $world == null) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => ['message' => 'User not connected to a world!']]);
                return;
            }

            // Update world user
            $worldUser = $this->connections[$connection->resourceId]['worldUser'];
            $worldUser->position_x = $data->position->x;
            $worldUser->position_y = $data->position->y;
            $worldUser->position_z = $data->position->z;
            $worldUser->rotation_x = $data->rotation->x;
            $worldUser->rotation_y = $data->rotation->y;
            $worldUser->rotation_z = $data->rotation->z;

            // Broadcast message
            broadcast($user->id, $world->id, $this->connections, $id, $type, [
                'user_id' => $user->id,
                'position' => ['x' => $data->position->x, 'y' => $data->position->y, 'z' => $data->position->z],
                'rotation' => ['x' => $data->rotation->x, 'y' => $data->rotation->y, 'z' => $data->rotation->z]
            ]);
        }

        if ($type == 'user.chat') {
            // Validate input
            $validation = Validator::make(['message' => $data->message], [
                'message' => 'required|string|min:1|max:191'
            ]);
            if ($validation->fails()) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => $validation->errors()]);
                return;
            }

            // Check if user is authed and connected to a world
            $user = $this->connections[$connection->resourceId]['user'];
            $world = $this->connections[$connection->resourceId]['world'];
            if ($user == null || $world == null) {
                send($connection, $id, $type . '.response', ['success' => false, 'errors' => ['message' => 'User not connected to a world!']]);
                return;
            }

            // Save chat message
            $worldChat = new WorldChat();
            $worldChat->world_id = $world->id;
            $worldChat->user_id = $user->id;
            $worldChat->message = $data->message;
            $worldChat->save();

            // Broadcast message
            broadcast($user->id, $world->id, $this->connections, $id, $type, ['user_id' => $user->id, 'chat' => $worldChat]);

            // Send response message
            send($connection, $id, $type . '.response', ['success' => true, 'chat' => $worldChat]);
        }
    }

    public function onSave() {
        echo 'Save clients data' . PHP_EOL;

        // Save pending world user positions
        foreach ($this->connections as $connection) {
            if ($connection['worldUser'] != null) {
                $connection['worldUser']->save();
            }
        }
    }

    public function onClose(ConnectionInterface $connection) {
        echo 'Client disconnected' . PHP_EOL;

        // If user was in world broadcast all users disconnect message
        $world = $this->connections[$connection->resourceId]['world'];
        if ($world != null) {
            $user = $this->connections[$connection->resourceId]['user'];
            broadcast($user->id, $world->id, $this->connections, 0, 'user.disconnect', [
                'user_id' => $user->id
            ]);
        }

        // If it had a world user save to be sure
        if ($this->connections[$connection->resourceId]['worldUser'] != null) {
            $this->connections[$connection->resourceId]['worldUser']->save();
        }

        // Remove connection
        unset($this->connections[$connection->resourceId]);
    }

    public function onError(ConnectionInterface $connection, \Exception $error) {
        // When an error happens close the connection
        echo 'Server error: ' . $error->getTraceAsString() . PHP_EOL;
        $connection->close();
    }
}
