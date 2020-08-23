import { Api } from './api';

import Vue from 'vue/dist/vue.js';

import serverCard from './../vue/server-card.vue';
import consumerRow from './../vue/consumer-row.vue';

const app = new Vue({
    el: '#app',
    components: { serverCard },
    data: {
        serverList: [],
        api: null
    },
    created() {
        this.setup();
    },
    methods: {
        setup() {
            this.updateServerList();
        },
        updateServerList() {
            this.api = new Api();

            const _this = this;

            this.api.getServerList().then(response => {
                const serverList = response.data;

                const hashes = Object.keys(serverList);

                let server = [];

                hashes.forEach(hash => {
                    const currentServer = {
                        hash: serverList[hash].nameHash,
                        name: serverList[hash].serverName,
                    };

                    server.push(currentServer);
                });

                this.serverList = server;

                setTimeout(function () {
                    _this.updateServerList();
                }, 5000);
            }, e => {
                setTimeout(function () {
                    _this.updateServerList();
                }, 5000);
            });
        }
    }
});
