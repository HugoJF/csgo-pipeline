module.exports = {
    apps: [{
        name: 'csgo-pipeline',
        cwd: './daemon/build',
        script: 'index.js',

        // Options reference: https://pm2.keymetrics.io/docs/usage/application-declaration/
        args: '',
        autorestart: true,
        watch: false,
        time: true,
    }],
};
