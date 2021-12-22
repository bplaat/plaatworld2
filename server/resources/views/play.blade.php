@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)

<script>

class Connection {
    constructor() {
        this.ws = new WebSocket('ws://localhost:8080/');
        this.connected = false;
        this.ws.onopen = this.onOpen.bind(this);
        this.ws.onmessage = this.onMessage.bind(this);
        this.ws.onclose = this.onClose.bind(this);
        this.ws.onerror = this.onError.bind(this);
        this.listeners = [];
    }

    send(type, data, callback = null) {
        if (this.connected) {
            if (callback != null) {
                this.listeners.push({ type: type, callback: callback });
            }
            this.ws.send(JSON.stringify({ type, data }));
        }
    }

    onOpen() {
        this.connected = true;
        console.log('Ws open');
        if (this.onConnected != undefined) {
            this.onConnected();
        }
    }

    onMessage(event) {
        const { type, data } = JSON.parse(event.data);

        // Resolve pending listeners
        for (const listener of this.listeners) {
            if (listener + '.response' == type) {
                listener.callback(data);
            }
        }
        this.listeners = this.listeners.filter(listener => listener.type + '.response' != type);
    }

    onClose() {
        this.connected = false;
        console.log('Ws close');
    }

    onError() {

    }
}

const connection = new Connection();
connection.onConnected = () => {
    connection.send('auth.login', {
        'token': 'token'
    });
};
</script>
@endcomponent
