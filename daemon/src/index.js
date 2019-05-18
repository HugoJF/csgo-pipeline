import request from 'request';
import dotenv from 'dotenv';
import Server from "./Server";
import {listServersUrl, requestOptions} from "./helpers";

dotenv.config({path: __dirname + '/.env'});

/*************
 *    ENV    *
 *************/
const REDIS_KEY = 'entry';

/*******************
 *    VARIABLES    *
 *******************/
let servers = [];

/*******************
 *    FUNCTIONS    *
 *******************/

function log() {
    console.log(arguments);
}

function processServers(err, res, body) {
    if (err) {
        log('Error while request server list');
        return;
    }

    if (!body) {
        log('Empty body from API request');
        return
    }

    if (body.error !== false) {
        log('API returned error', body);
        return;
    }

    let response = body.response;

    servers = response.map(({hostname, name, ip, port}) => (
        new Server(REDIS_KEY, hostname, name, ip, port)
    ));

    servers.forEach((server) => server.boot());
}

function queryServers() {
    let url = listServersUrl();
    request.get(url, requestOptions, processServers);
}

/**********************
 *    STATIC CALLS    *
 **********************/

queryServers();