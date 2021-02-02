<template>
    <div class="card col-md-12 col-lg-6 p-2">
        <ul class="list-group">
            <li
                class="list-group-item"
                v-bind:class="[
                    isOnline
                        ? 'list-group-item-success'
                        : 'list-group-item-danger',
                ]"
            >
                {{ serverName }}
                <span
                    v-if="serverVersion"
                    class="badge badge-pill badge-primary"
                    >v {{ serverVersion }}</span
                >
                <span
                    class="badge badge-success badge-danger control-badge"
                    style="float: right"
                    v-on:click="restartAll"
                    ><span class="fas fa-redo"></span> Restart all</span
                >
                <span
                    class="badge badge-success badge-success control-badge"
                    style="float: right"
                    v-on:click="startAll"
                    ><span class="far fa-play-circle"></span> Start all</span
                >
                <span
                    class="badge badge-success badge-dark control-badge"
                    style="float: right"
                    v-on:click="stopAll"
                    ><span class="far fa-stop-circle"></span> Stop all</span
                >
            </li>
            <li class="list-group-item" v-cloak>
                <table class="table">
                    <tbody>
                        <!-- {# note for non-vue developers:-->
                        <!-- <table> can only contain <tr> but not <consumer-row> therefore vue supplies the 'is' #}-->
                        <tr
                            is="consumer-row"
                            v-for="consumer in consumerList"
                            v-bind:key="consumer.group + consumer.name"
                            v-bind:consumer="consumer"
                            v-bind:server-hash="serverHash"
                            v-bind:server-name="serverName"
                        ></tr>
                    </tbody>
                </table>
            </li>
        </ul>
    </div>
</template>

<script>
import { Api } from '../js/api';

import consumerRow from './consumer-row.vue';

export default {
    name: 'serverCard',
    components: { consumerRow },
    // TODO: add types:
    // props: {'serverName': String, 'serverHash': String],
    props: ['serverName', 'serverHash', 'serverUpdateInterval'],
    data() {
        return {
            logInfo: {},
            consumerList: [],
            serverVersion: null,
            isOnline: false,
            api: null,
            defaultUpdateInterval: {
                consumer: 2,
                logs: 10,
                version: 10,
            },
            isUpdating: {
                consumer: false,
                logs: false,
                version: false,
            },
            timeOfUpdate: {
                consumer: 0,
                logs: 0,
                version: 0,
            },
        };
    },
    mounted() {
        this.api = new Api(this.serverHash);

        this.heartbeat();
    },
    methods: {
        getDuration_s(old) {
            return (Date.now() - old) / 1000;
        },
        shouldUpdate(name) {
            if (this.isUpdating[name]) {
                return false;
            }

            const duration_s = this.getDuration_s(this.timeOfUpdate[name]);

            let interval_s = this.defaultUpdateInterval[name];

            if (typeof this.serverUpdateInterval[name] === 'number') {
                interval_s = this.serverUpdateInterval[name];
            }

            return duration_s >= interval_s;
        },
        signalRequestStart(name) {
            this.isUpdating[name] = true;
        },
        signalRequestStop(name, success) {
            this.isOnline = success;
            this.isUpdating[name] = false;
            this.timeOfUpdate[name] = Date.now();
        },
        heartbeat() {
            if (!this.isOnline) {
                if (this.shouldUpdate('version')) {
                    this.updateSupervisorVersion();
                }

                setTimeout(this.heartbeat, 200);

                return;
            }

            if (this.shouldUpdate('consumer')) {
                this.updateConsumerList();
            }

            if (this.shouldUpdate('logs')) {
                this.updateLogInfo();
            }

            setTimeout(this.heartbeat, 200);
        },
        updateConsumerList() {
            this.signalRequestStart('consumer');

            this.api
                .getConsumerList()
                .then((response) => {
                    const keys = Object.keys(response.data);

                    keys.forEach((value) => {
                        const data = response.data[value];

                        const name = data.name;

                        if (response.data[value].out_log === null) {
                            response.data[value].out_log = ['', 0, false];
                        }

                        if (response.data[value].err_log === null) {
                            response.data[value].err_log = ['', 0, false];
                        }

                        if (this.logInfo.hasOwnProperty(name)) {
                            response.data[value].out_log = this.logInfo[
                                name
                            ].out_log;
                            response.data[value].err_log = this.logInfo[
                                name
                            ].err_log;
                        }
                    });

                    this.consumerList = response.data;

                    this.signalRequestStop('consumer', true);
                })
                .catch((e) => {
                    this.signalRequestStop('consumer', false);
                });
        },
        updateLogInfo() {
            this.signalRequestStart('logs');

            this.api
                .getLogInfo()
                .then((response) => {
                    const keys = Object.keys(response.data);

                    let logInfo = {};

                    keys.forEach(function (value) {
                        const data = response.data[value];
                        const name = data.name;

                        logInfo[name] = {
                            out_log: data.out_log,
                            err_log: data.err_log,
                        };
                    });

                    this.logInfo = logInfo;

                    this.signalRequestStop('logs', true);
                })
                .catch((e) => {
                    this.signalRequestStop('logs', false);
                });
        },
        updateSupervisorVersion() {
            this.signalRequestStart('version');

            this.api
                .getSupervisorVersion()
                .then((response) => {
                    this.serverVersion = response.data;

                    this.signalRequestStop('version', true);
                })
                .catch((e) => {
                    this.signalRequestStop('version', false);
                });
        },
        stopAll() {
            this.api.stopAll();
        },
        startAll() {
            this.api.startAll();
        },
        restartAll() {
            this.api.restartAll();
        },
    },
};
</script>

<style scoped></style>
