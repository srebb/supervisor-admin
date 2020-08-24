<template>
    <div class="card col-md-12 col-lg-6 p-2">
        <ul class="list-group">
            <li
                class="list-group-item"
                v-bind:class="[isOnline ? 'list-group-item-success' : 'list-group-item-danger']"
            >
                {{ serverName }}
                <span v-if="serverVersion" class="badge badge-pill badge-primary">v {{ serverVersion }}</span>
                <span
                    class="badge badge-success badge-danger control-badge"
                    style="float: right;"
                    v-on:click="restartAll"
                    ><span class="fas fa-redo"></span> Restart all</span
                >
                <span
                    class="badge badge-success badge-success control-badge"
                    style="float: right;"
                    v-on:click="startAll"
                    ><span class="far fa-play-circle"></span> Start all</span
                >
                <span class="badge badge-success badge-dark control-badge" style="float: right;" v-on:click="stopAll"
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
    props: ['serverName', 'serverHash'],
    data() {
        return {
            consumerList: [],
            serverVersion: null,
            isOnline: false,
            api: null,
        };
    },
    mounted() {
        this.api = new Api(this.serverHash);
        this.getSupervisorVersion();
    },
    methods: {
        getConsumerList() {
            this.api
                .getConsumerList()
                .then((response) => {
                    this.consumerList = response.data;
                    this.isOnline = true;
                    setTimeout(this.getConsumerList, 1000);
                })
                .catch((e) => {
                    this.isOnline = false;
                    setTimeout(this.getSupervisorVersion, 1000);
                });
        },
        getSupervisorVersion() {
            this.api
                .getSupervisorVersion()
                .then((response) => {
                    this.serverVersion = response.data;
                    this.getConsumerList();
                })
                .catch((e) => {
                    setTimeout(this.getSupervisorVersion, 3000);
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
