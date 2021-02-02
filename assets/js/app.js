import { Api } from './api';

import Vue from 'vue/dist/vue.js';

import serverCard from './../vue/server-card.vue';

let created = false;

/**
 * @return Vue
 */
function createApp() {
    if (created) {
        throw new Error('you can pnly start this app once!!!');
    }

    created = true;

    return new Vue({
        el: '#app',
        components: { serverCard },
        data: {
            serverInfos: [], // array of ServerInfo
            api: null,
        },
        created() {
            this.setup();
        },
        methods: {
            setup() {
                this.updateServerInfos();
            },
            updateServerInfos() {
                this.api = new Api();

                const _this = this;

                this.api.getServerInfos().then(
                    (response) => {
                        /**
                         * response.data contains an object with all serverinfos
                         * each server info is index by the hash of the server
                         */
                        const rawServerInfo = response.data;

                        const hashes = Object.keys(rawServerInfo);

                        let serverInfos = [];

                        hashes.forEach((hash) => {
                            /** @type ServerInfo */
                            const serverInfo = rawServerInfo[hash];

                            if (!serverInfo.updateInterval) {
                                serverInfo.updateInterval = {
                                    consumer: 2,
                                    logs: 10,
                                };
                            }

                            if (!serverInfo.updateInterval.logs) {
                                serverInfo.updateInterval.logs = 10;
                            }

                            if (!serverInfo.updateInterval.logs) {
                                serverInfo.updateInterval.logs = 2;
                            }

                            serverInfos.push(serverInfo);
                        });

                        this.serverInfos = serverInfos;

                        setTimeout(function () {
                            _this.updateServerInfos();
                        }, 5000);
                    },
                    (e) => {
                        setTimeout(function () {
                            _this.updateServerInfos();
                        }, 10000);
                    }
                );
            },
        },
    });
}

export { createApp };
