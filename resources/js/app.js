require('./bootstrap');

import Echo from 'laravel-echo';

window.Vue = require('vue');

const app = new Vue({
    el: '#app',
    data: {
        messages: [],
        newMessage: ''
    },
    created() {
        axios.get('/chat').then(response => {
            this.messages = response.data;
        });

        Echo.channel('chat')
            .listen('.MessageSent', (e) => {
                this.messages.push({
                    user: e.message.user,
                    message: e.message.message
                });
            });
    },
    methods: {
        sendMessage() {
            if (this.newMessage) {
                axios.post('/chat', {
                    message: this.newMessage
                }).then(response => {
                    console.log(response.data);
                });
                this.newMessage = '';
            }
        }
    }
});

