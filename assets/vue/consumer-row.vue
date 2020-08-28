<template>
    <tr>
        <td class="p-1">
            <span class="consumer-identifier"
                >{{ consumer.group }}:{{ consumer.name }}</span
            >
        </td>
        <td class="p-1">
            {{ consumer.description }}
        </td>
        <td class="p-1">
            <span
                role="button"
                class="badge badge-success"
                v-if="consumer.out_log[2] === true"
                v-on:click="getLog(consumer.group + ':' + consumer.name)"
                ><i class="fa fa-info-circle"></i>
                {{ consumer.out_log[1] }}</span
            >
            <span
                role="button"
                class="badge badge-danger"
                v-if="consumer.err_log[2] === true"
                v-on:click="getErrorLog(consumer.group + ':' + consumer.name)"
                ><i class="fa fa-exclamation-circle"></i>
                {{ consumer.err_log[1] }}</span
            >
        </td>
        <td class="p-1">
            <!-- STOPPED -->
            <span
                v-if="consumer.state == 0"
                class="user-select-none badge badge-dark"
            >
                {{ consumer.statename }}
            </span>
            <!-- STARTING -->
            <span
                v-else-if="consumer.state == 10"
                class="user-select-none badge badge-warning"
                >{{ consumer.statename }}</span
            >
            <!-- RUNNING -->
            <span
                v-else-if="consumer.state == 20"
                class="user-select-none badge badge-success"
                >{{ consumer.statename }}
            </span>
            <!-- BACKOFF -->
            <span
                v-else-if="consumer.state == 30"
                class="user-select-none badge badge-dark"
                >{{ consumer.statename }}
            </span>
            <!-- STOPPING -->
            <span
                v-else-if="consumer.state == 40"
                class="user-select-none badge badge-warning"
                >{{ consumer.statename }}
            </span>
            <!-- EXITED -->
            <span
                v-else-if="consumer.state == 100"
                class="user-select-none badge badge-dark"
                >{{ consumer.statename }}
            </span>
            <!-- FATAL -->
            <span
                v-else-if="consumer.state == 200"
                class="user-select-none badge badge-dark"
                >{{ consumer.statename }}</span
            >
            <!-- UNKNOWN - pls implement me-->
            <span
                v-else
                class="user-select-none badge badge-primary"
                style="float: right;"
                >{{ consumer.statename }} - {{ consumer.state }}</span
            >
        </td>
        <td class="p-1" style="text-align: right;">
            <span
                role="button"
                v-if="[20].includes(consumer.state)"
                class="far fa-stop-circle"
                v-on:click="stop(consumer.group + ':' + consumer.name)"
            ></span>
            <span
                role="button"
                v-if="[0].includes(consumer.state)"
                class="far fa-play-circle"
                v-on:click="start(consumer.group + ':' + consumer.name)"
            ></span>
            <span
                role="button"
                class="fas fa-redo"
                v-on:click="restart(consumer.group + ':' + consumer.name)"
            >
            </span>
        </td>
    </tr>
</template>

<script>
import { Api } from '../js/api';

export default {
    name: 'consumerRow',
    // TODO: add types:
    // props: {'consumer': Object, 'serverName': String, 'serverHash': String],
    props: ['consumer', 'serverName', 'serverHash'],
    data() {
        return { api: null };
    },
    mounted() {
        this.api = new Api(this.serverHash);
    },
    methods: {
        stop(consumerName) {
            this.api.stop(consumerName);
        },
        start(consumerName) {
            this.api.start(consumerName);
        },
        restart(consumerName) {
            this.api.restart(consumerName);
        },
        getLog(consumerName) {
            this.api.getFormattedLog(consumerName).then((log) => {
                $('#logModal .log-span').text(log);
                $('#logModal').modal();
            });
        },
        getErrorLog(consumerName) {
            this.api.getFormattedErrorLog(consumerName).then((log) => {
                $('#logModal .log-span').text(log);
                $('#logModal').modal();
            });
        },
    },
};
</script>

<style scoped></style>
