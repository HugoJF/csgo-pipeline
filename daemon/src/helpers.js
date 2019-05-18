export const requestOptions = {
    json: true,
    timeout: 10000,
};

export function listServersUrl() {
    let API_URL = process.env.API_URL;
    let TOKEN = process.env.TOKEN;

    return `${API_URL}/list?token=${TOKEN}`;
}

export function executeUrl(ip, port, command) {
    let API_URL = process.env.API_URL;
    let TOKEN = process.env.TOKEN;

    return `${API_URL}/send?token=${TOKEN}&ip=${ip}&port=${port}&command=${command}`;
}

export function response(res, message) {
    return JSON.stringify({
        error: false,
        message: message,
        response: res
    });
}

export function error(message) {
    return JSON.stringify({
        error: true,
        message: message,
    });
}