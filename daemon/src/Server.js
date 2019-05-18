import {executeUrl, requestOptions} from "./helpers";
import {LogReceiver} from "srcds-log-receiver";
import redis from 'redis';
import request from 'request';

const redisC = redis.createClient(); //creates a new client

/**
 * BINDINGS
 */
redisC.on('connect', function () {
    console.log('REDIS is connected');
});

/**
 * RUNTIME VARIABLES
 */
let receiverPortIndex = 10001;

class Server {
    constructor(redisKey, hostname, name, ip, port) {
        /**************
         * PROPERTIES *
         **************/
        this.redisKey = redisKey;
        this.hostname = hostname;
        this.name = name;
        this.ip = ip;
        this.port = port;
        this.receiverPort = receiverPortIndex++;

        /***********
         * HANDLES *
         ***********/
        this.receiver = undefined;

        /*************
         * INTERVALS *
         *************/
        this.bindReceiverInterval = undefined;
        this.highDetailsInterval = undefined;
    }

    /**
     * Boot server
     */
    boot() {
        console.log(`${this.toString()} Booting...`);
        this.startReceiver();
        this.startIntervals();
    }

    /**
     * Calls the server API to execute a command
     *
     * @param command - command to be executed
     */
    execute(command) {
        let url = executeUrl(this.ip, this.port, command);

        request.get(url, requestOptions, this.onExecuted.bind(this));
    }

    /**
     * Callback when the server API responds after a command execution
     *
     * @param err - request error
     * @param res - request response
     * @param body - response body
     */
    onExecuted(err, res, body) {
        console.log('Command executed', body);
    }

    /**
     * Start intervals that keep the CS:GO server updated to send logs
     */
    startIntervals() {
        clearInterval(this.bindReceiverInterval);
        clearInterval(this.highDetailsInterval);

        this.bindReceiver();
        this.setHighDetails();

        this.bindReceiverInterval = setInterval(this.bindReceiver.bind(this), 30000);
        this.highDetailsInterval = setInterval(this.setHighDetails.bind(this), 30000);
    }

    /**
     * Sends logaddress command to server
     */
    bindReceiver() {
        console.log('Bound to receiver!: ' + process.env.LISTENING_IP);
        this.execute(`logaddress_add ${process.env.LISTENING_IP}:${this.receiverPort}`)
    }

    /**
     * Forces mp_logdetail to 3 (full log)
     */
    setHighDetails() {
        console.log('Forcing mp_logdetail 3');
        this.execute('mp_logdetail 3');
    }

    /**
     * Initializes log receiver for this server
     */
    startReceiver() {
        console.log('Binding receiver on port: ' + this.receiverPort);
        this.receiver = new LogReceiver({port: this.receiverPort});
        this.bindOnData();
        this.bindOnInvalid();
    }

    /**
     * Bind data event on log receiver
     */
    bindOnData() {
        this.receiver.on("data", this.onData.bind(this));
    }

    /**
     * Callback for CS:GO server logs
     * @param data
     */
    onData(data) {
        if (data.isValid)
            this.onValidData(data);
    }

    /**
     * On valid CS:GO server logs
     * @param data
     */
    onValidData(data) {
        redisC.rpush([this.redisKey, `${this.address()} - ${data.message}`], this.redisCallback);
    }

    /**
     * Redis callback
     *
     * @param err
     * @param reply
     */
    redisCallback(err, reply) {
        if (err) {
            console.error(err);
        } else {
            console.log(reply);
        }
    }

    /**
     * Binds invalid event on log receiver
     */
    bindOnInvalid() {
        this.receiver.on("invalid", this.onInvalid)
    }

    /**
     * Callback for invalid CS:GO logs
     * @param err
     */
    onInvalid(err) {
        console.log(`Received invalid message: ${err}`);
    }

    /**
     * Server address
     *
     * @returns {string}
     */
    address() {
        return `${this.ip}:${this.port}`;
    }

    /**
     * Pretty print server name and address
     *
     * @returns {string}
     */
    toString() {
        return `[${this.name} (${this.address()})]`;
    }

}

export default Server;