
const WebSocket = require('ws');

const ip = 'localhost'; // Replace with your machine's IP address
const wss = new WebSocket.Server({ host: ip, port: 8081 });


wss.on('connection', (ws) => {
  ws.on('message', (message) => {
    
    wss.clients.forEach((client) => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(message);
      }
    });
  });
});
