const express = require("express");
const fs = require("fs");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

const FILE = "data.json";

// Create file if not exists
if (!fs.existsSync(FILE)) {
  fs.writeFileSync(FILE, "[]");
}

/* SUBMIT */
app.post("/apply", (req, res) => {
  let data = JSON.parse(fs.readFileSync(FILE));

  let id = "ITA" + Math.floor(100000 + Math.random() * 900000);

  let newApp = {
    id: id,
    status: "pending",
    ...req.body
  };

  data.push(newApp);

  fs.writeFileSync(FILE, JSON.stringify(data, null, 2));

  res.json({ success: true, id: id });
});

/* ADMIN DATA */
app.get("/admin", (req, res) => {
  let data = JSON.parse(fs.readFileSync(FILE));
  res.json(data);
});

/* APPROVE */
app.post("/approve/:id", (req, res) => {
  let data = JSON.parse(fs.readFileSync(FILE));

  data = data.map(app => {
    if (app.id === req.params.id) {
      app.status = "approved";
    }
    return app;
  });

  fs.writeFileSync(FILE, JSON.stringify(data, null, 2));

  res.json({ success: true });
});

/* STATUS CHECK */
app.get("/status/:id", (req, res) => {
  let data = JSON.parse(fs.readFileSync(FILE));

  let found = data.find(app => app.id === req.params.id);

  if (!found) {
    return res.json({ found: false });
  }

  res.json({ found: true, status: found.status });
});

app.listen(3000, () => {
  console.log("✅ Server running: http://localhost:3000");
});