# Quickstart

### **Step 1.** Set up the Development Environment:
```bash
cd frontend/src/environments
cp environment.ts environment.loc.ts
```

### **Step 2.** Edit environment file:
```js
...
API_BASE_URL: "http://your-url.loc/api",
...
```
**API_BASE_URL** - the url to you backend server.

### **Step 3.** Return to root frontend folder and run local server:
```bash
cd frontend
npm run local
```
The `npm run local` command launches the server, watches your files, and rebuilds the app as you make changes to those files.

After run local server you can open your project by http://localhost:4200/.

### **Step 4.** For build your app and places it into the output path (web/ by default).:
```bash
npm run build
```