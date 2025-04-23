const express = require("express");
const sql = require("mssql");
const path = require("path");

const app = express();
const port = 3000; // or 8080 if you prefer

// SQL Server config
const dbConfig = {
  user: "acer", // optional if using Windows auth
  password: "", // optional if using Windows auth
  server: "localhost\\SQLEXPRESS",
  database: "school_management",
  options: {
    trustServerCertificate: true,
  },
};

// Serve your static site from your XAMPP folder
app.use(express.static(path.join("E:", "xampp", "htdocs", "sms")));

// Sample API
app.get("/api/students", async (req, res) => {
  try {
    await sql.connect(dbConfig);
    const result = await sql.query`SELECT * FROM students`;
    res.json(result.recordset);
  } catch (err) {
    res.status(500).send(err.message);
  }
});

// Start the server
app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
