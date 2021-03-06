import axios from 'axios';

/**
 * This is the object which is returned for a singel server
 *
 * @typedef {Object} ServerInfo
 *
 * @property {String} serverName
 * @property {String} nameHash
 * @property {String} host The IP adress
 * @property {Object} supervisor
 * @property {Object} updateInterval
 * @property {Number} updateInterval.consumer
 * @property {Number} updateInterval.logs
 */


/**
 * This takes a log and tries to parse each line as JSON
 * If a line is valid JSON then it is formatted nicely
 * Otherwise the original line is user
 *
 * @param {String} log
 *
 * @return {String}
 */
function formatLog(log) {
    const parts = log.split('\n');

    let formatted = '';

    parts.forEach((part) => {
        let beautified = null;
        try {
            const decoded = JSON.parse(part);

            beautified = JSON.stringify(decoded, null, 4);
        } catch (e) {}

        if (beautified === null) {
            formatted += part + '\n';

            return;
        }

        formatted += beautified + '\n';
    });

    return formatted;
}

class Api {
    /**
     * @param {String} [hash=null]
     */
    constructor(hash) {
        /**
         * @type {?string}
         */
        this.hash = null;

        if (typeof hash === 'string') {
            if (hash.length < 6) {
                throw new Error('the hash parameter must be a long-ish string');
            }

            this.hash = hash;
        }
    }

    _assertHashIsSet() {
        if (this.hash === null) {
            throw new Error('hash not set in api');
        }
    }

    /**
     * @param {String} hash
     */
    setHash(hash) {
        if (typeof hash !== 'string' || hash.length < 6) {
            throw new Error('the hash parameter must be a long-ish string');
        }

        this.hash = hash;
    }

    // TODO: make static
    getServerInfos() {
        return axios.get('api/server');
    }

    getConsumerList() {
        this._assertHashIsSet();

        return axios.get('api/server/' + this.hash + '/consumerlist');
    }

    getLogInfo() {
        this._assertHashIsSet();

        return axios.get('api/server/' + this.hash + '/loginfo');
    }


    getSupervisorVersion() {
        this._assertHashIsSet();

        return axios.get('api/server/' + this.hash + '/supervisorversion');
    }

    stopAll() {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/stopAll')
            .then((response) => {});
    }

    startAll() {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/startAll')
            .then((response) => {});
    }

    restartAll() {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/restartAll')
            .then((response) => {});
    }

    /**
     * @param {String} consumerName
     */
    stop(consumerName) {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/stop/' + consumerName)
            .then((response) => {});
    }

    /**
     * @param {String} consumerName
     */
    start(consumerName) {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/start/' + consumerName)
            .then((response) => {});
    }

    /**
     * @param {String} consumerName
     */
    restart(consumerName) {
        this._assertHashIsSet();

        axios
            .post('api/server/' + this.hash + '/restart/' + consumerName)
            .then((response) => {});
    }

    /**
     * @param {String} consumerName
     */
    getLog(consumerName) {
        this._assertHashIsSet();

        axios
            .get('api/server/' + this.hash + '/getLog/' + consumerName)
            .then((response) => {
                console.log(response.data);
            });
    }

    /**
     * @param {String} consumerName
     */
    getErrorLog(consumerName) {
        this._assertHashIsSet();

        axios
            .get('api/server/' + this.hash + '/getErrorLog/' + consumerName)
            .then((response) => {
                console.log(response.data);
            });
    }

    /**
     * @param {String} consumerName
     *
     * @return {Promise<string>}
     */
    getFormattedLog(consumerName) {
        this._assertHashIsSet();

        return new Promise((resolve, reject) => {
            axios
                .get('api/server/' + this.hash + '/getLog/' + consumerName)
                .then((response) => {
                    if (typeof response.data[0] !== 'string') {
                        reject('response does not contain expected data');
                    }

                    /** @type string */
                    let log = response.data[0];

                    let formatted = formatLog(log);

                    resolve(formatted);
                })
                .catch((e) => {
                    reject(e);
                });
        });
    }

    /**
     * @param {String} consumerName
     *
     * @return {Promise<string>}
     */
    getFormattedErrorLog(consumerName) {
        this._assertHashIsSet();

        return new Promise((resolve, reject) => {
            axios
                .get('api/server/' + this.hash + '/getErrorLog/' + consumerName)
                .then((response) => {
                    if (typeof response.data[0] !== 'string') {
                        reject('response does not contain expected data');
                    }

                    /** @type string */
                    let log = response.data[0];

                    let formatted = formatLog(log);

                    resolve(formatted);
                })
                .catch((e) => {
                    reject(e);
                });
        });
    }
}

export { Api };
