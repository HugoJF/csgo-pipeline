import dotenv from 'dotenv';
import * as Sentry from '@sentry/node';

dotenv.config({path: __dirname + '/.env'});
Sentry.init({ dsn: process.env.SENTRY_DSN });

import request from 'request';
import Server from "./Server";
import {listServersUrl, requestOptions} from "./helpers";

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
        log('Error while request server list: ' + err);
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

    servers = response.map(({hostname, name, ip, port}) => {
        log(`Building server ${hostname} on ${ip}:${port}`);
        return new Server(hostname, name, ip, port)
    });

    servers.forEach((server) => server.boot());
}

function queryServers() {
    let url = listServersUrl();
    request.get(url, requestOptions, processServers);
}

/**********************
 *    STATIC CALLS    *
 **********************/
log('a log');
queryServers();